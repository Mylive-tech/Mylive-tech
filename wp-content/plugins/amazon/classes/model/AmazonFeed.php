<?php
/**
 * WPLA_AmazonFeed class
 *
 */

// class WPLA_AmazonFeed extends WPLA_NewModel {
class WPLA_AmazonFeed {

	const TABLENAME = 'amazon_feeds';

	var $id      = null;
	var $data    = null;
	var $results = null;

	function __construct( $id = null ) {
		
		$this->init();

		if ( $id ) {
			$this->id = $id;
			
			// load data into object
			$feed = self::getFeed( $id );
			foreach( $feed AS $key => $value ){
			    $this->$key = $value;
			}

			return $this;
		}

	}

	function init()	{
		global $wpla_logger;
		$this->logger = &$wpla_logger;

		$this->types = array(
			'_POST_FLAT_FILE_PRICEANDQUANTITYONLY_UPDATE_DATA_' => 'Price and Quantity Update Feed',
			'_POST_FLAT_FILE_LISTINGS_DATA_'                 	=> 'Listings Data Feed',
			'_CHECK_FLAT_FILE_LISTINGS_DATA_'                 	=> 'Listings Data Feed (check only)',
			'_POST_FLAT_FILE_FULFILLMENT_DATA_'      			=> 'Order Fulfillment Feed',
			'_POST_FLAT_FILE_FULFILLMENT_ORDER_REQUEST_DATA_'   => 'FBA Shipment Fulfillment Feed',
			'_POST_FLAT_FILE_INVLOADER_DATA_'                   => 'Inventory Loader Feed',
		);

		$this->fieldnames = array(
			'FeedSubmissionId',
			'FeedType',
			'template_name',
			'FeedProcessingStatus',
			'results',
			'success',
			'status',
			'SubmittedDate',
			'StartedProcessingDate',
			'CompletedProcessingDate',
			'GeneratedFeedId',
			'date_created',
			'account_id',
			'line_count',
			'data'
		);

	} // init()

	// get single feed
	static function getFeed( $id )	{
		global $wpdb;
		$table = $wpdb->prefix . self::TABLENAME;
		
		$item = $wpdb->get_row( $wpdb->prepare("
			SELECT *
			FROM $table
			WHERE id = %d
		", $id
		), OBJECT);

		return $item;
	}

	// get single feed by FeedSubmissionId
	static function getFeedBySubmissionId( $FeedSubmissionId )	{
		global $wpdb;
		$table = $wpdb->prefix . self::TABLENAME;
		
		$item = $wpdb->get_row( $wpdb->prepare("
			SELECT *
			FROM $table
			WHERE FeedSubmissionId = %s
		", $FeedSubmissionId
		), OBJECT);

		return $item;
	}

	// get all feeds
	static function getAll() {
		global $wpdb;
		$table = $wpdb->prefix . self::TABLENAME;

		$items = $wpdb->get_results("
			SELECT *
			FROM $table
			ORDER BY sort_order ASC
		", OBJECT_K);

		return $items;
	}

	// get pending feed
	static function getPendingFeedId( $feed_type, $template_name, $account_id ) {
		global $wpdb;
		$table = $wpdb->prefix . self::TABLENAME;
		$template_name = esc_sql( $template_name );
		$where_sql     = $template_name ? "AND template_name = '$template_name'" : '';

		$item = $wpdb->get_var( $wpdb->prepare("
			SELECT id
			FROM $table
			WHERE status     = 'pending'
			  AND account_id = %d
			  AND FeedType   = %s
			  $where_sql
		",
		$account_id,
		$feed_type
		));

		return $item;
	}

	// get all pending feeds for account
	static function getAllPendingFeedsForAccount( $account_id ) {
		global $wpdb;
		$table = $wpdb->prefix . self::TABLENAME;

		$item_ids = $wpdb->get_col( $wpdb->prepare("
			SELECT id
			FROM $table
			WHERE status     = 'pending'
			  AND account_id = %d
		", $account_id ));

		$feeds = array();
		foreach ( $item_ids as $feed_id ) {
			$feeds[] = new WPLA_AmazonFeed( $feed_id );
		}

		return $feeds;
	}

	// get pending feeds summary
	static function getAllPendingFeeds() {
		global $wpdb;
		$table = $wpdb->prefix . self::TABLENAME;

		$item_ids = $wpdb->get_col("
			SELECT id
			FROM $table
			WHERE status = 'pending'
		");

		return $item_ids;
	}


	function getDataArray() {
		if ( ! $this->data || empty( $this->data ) ) return array();

		$feed_data = $this->data;
		if ( in_array( $this->FeedType, array('_POST_FLAT_FILE_LISTINGS_DATA_','_CHECK_FLAT_FILE_LISTINGS_DATA_') ) ) {
			// remove first two rows - headers are in 3rd row
			$rows_to_remove = 2;
			if ( $this->template_name == 'Offer' ) $rows_to_remove = 1;
			$feed_data = implode("\n", array_slice(explode("\n", $feed_data), $rows_to_remove ));
		}

		$rows = $this->csv_to_array( $feed_data );
		return $rows;		
	}


	function createCheckFeed() {
		if ( ! $this->id ) return;
		if ( ! $this->data ) return;

		// clone feed
		$this->id = null;
		$this->FeedType = str_replace( '_POST_', '_CHECK_', $this->FeedType );
		$this->add();
		
		// submit cloned feed
		$result = $this->submit();

		return $result;
	}

	function isCheckFeed() {
		if ( ! $this->FeedType ) return false;
		if ( substr( $this->FeedType, 0, 7 ) == '_CHECK_' ) return true;
		return false;
	}


	function cancel() {
		if ( ! $this->id ) return;

		$api = new WPLA_AmazonAPI( $this->account_id );
		$result = $api->cancelFeedSubmission( $this->FeedSubmissionId );
		// echo "<pre>";print_r($result);echo"</pre>";die();

		if ( $result->success ) {
			
			// update feed status
			// $this->FeedSubmissionId     = $result->FeedSubmissionId;
			// $this->FeedProcessingStatus = $result->FeedProcessingStatus;
			// $this->SubmittedDate        = $result->SubmittedDate;
			// $this->status 		    	= 'cancelled';
			// $this->update();

		} // success

		return $result;
	} // cancel()

	function submit() {
		if ( ! $this->id ) return;
		if ( ! $this->data ) return;
        if ( $this->status != 'pending' ) return;

		$api = new WPLA_AmazonAPI( $this->account_id );

		// adjust feed encoding
		$feed_content = $this->data;
		if ( get_option( 'wpla_feed_encoding' ) != 'UTF-8' ) {
			$feed_content = utf8_decode( $feed_content );
		}

		$result = $api->submitFeed( $this->FeedType, $feed_content );
		// echo "<pre>";print_r($result);echo"</pre>";die();

		if ( $result->success ) {
			
			// update feed status
			$this->FeedSubmissionId     = $result->FeedSubmissionId;
			$this->FeedProcessingStatus = $result->FeedProcessingStatus;
			$this->SubmittedDate        = $result->SubmittedDate;
			$this->status 		    	= 'submitted';
			$this->update();

			// increase feeds in progress
		    $feeds_in_progress = get_option( 'wpla_feeds_in_progress', 0 );
			update_option( 'wpla_feeds_in_progress', $feeds_in_progress + 1 );


			// update status of submitted products - except for check feeds
			if ( ! $this->isCheckFeed() ) {

				$lm = new WPLA_ListingsModel();
				// $rows = $this->csv_to_array( $this->data );
				$rows = $this->getDataArray();
				foreach ($rows as $row) {

					$listing_sku = isset( $row['sku'] ) ? $row['sku'] : $row['item_sku'];
					$listing_item = $lm->getItemBySKU( $listing_sku );

					if ( $listing_item ) {

						// check feed type
						switch ($this->FeedType) {

							// Listing Data feed
							case '_POST_FLAT_FILE_LISTINGS_DATA_':

								$listing_data = array();
								$listing_data['status']  = 'submitted';
								$listing_data['history'] = '';
								$this->logger->info('changing status to submitted for SKU '.$listing_sku);

								// update date_published - only if not set
								if ( ! $listing_item->date_published )
									$listing_data['date_published'] = date('Y-m-d H:i:s');

								break;
							
							// Price And Quantity feed
							case '_POST_FLAT_FILE_PRICEANDQUANTITYONLY_UPDATE_DATA_':

								$listing_data = array();
								$listing_data['pnq_status'] = '2'; // submitted
								$this->logger->info('changing PNQ status to 2 (submitted) for SKU '.$listing_sku);

								break;
							
							// Inventory Loader (delete) feed
							case '_POST_FLAT_FILE_INVLOADER_DATA_':

								$listing_data = array();
								$listing_data['status'] = 'trashed'; // submitted for deletion
								$this->logger->info('changing status to trashed for SKU '.$listing_sku);

								break;
							
							default:
								$this->logger->warn('nothing to process for feed type '.$this->FeedType.' - SKU '.$listing_sku);
								break;
						}

						// update database
						$where_array = array( 'sku' => $listing_sku, 'account_id' => $this->account_id );
						$lm->updateWhere( $where_array, $listing_data );				

					} else {
						$this->logger->warn('no listing found for SKU '.$listing_sku);
					} // if $listing_item
					
				} // for each row

			} // not check feed

		} // success

		return $result;
	} // submit()

	function loadSubmissionResult() {
		if ( ! $this->id ) return;
		if ( ! $this->FeedSubmissionId ) return;
		if ( $this->FeedProcessingStatus != '_DONE_' ) return;

		$api = new WPLA_AmazonAPI( $this->account_id );

		$result = $api->getFeedSubmissionResult( $this->FeedSubmissionId );

		if ( $result && $result->success ) {
			$this->results = utf8_encode( $result->content ); // required for amazon.fr
			$this->update();
		}

		return $result;
	} // loadSubmissionResult()

	function processSubmissionResult() {
		$this->logger->info('processSubmissionResult()');
		if ( ! $this->id ) return;
		if ( ! $this->results ) return;

		$this->errors   = array();
		$this->warnings = array();

		// fetch list of submitted product SKUs
		$feed_rows = $this->getDataArray();
		$this->logger->info('data rows   for feed '.$this->FeedSubmissionId.' ('.$this->id.'): '.sizeof($feed_rows));

		// extract result csv data
		$result_content = implode("\n", array_slice(explode("\n", $this->results), 4)); // remove summary rows
		$result_rows = $this->csv_to_array( $result_content );
		$this->logger->info('result rows for feed '.$this->FeedSubmissionId.' ('.$this->id.'): '.sizeof($result_rows));
		$this->logger->info('result rows '.print_r($result_rows,1));

		// process results
		if ( $this->FeedType == '_POST_FLAT_FILE_FULFILLMENT_DATA_' ) {
			$this->processOrderFulfillmentResults( $feed_rows, $result_rows );
		} elseif ( $this->FeedType == '_POST_FLAT_FILE_FULFILLMENT_ORDER_REQUEST_DATA_' ) {
			$this->processOrderFbaResults( $feed_rows, $result_rows );
		} elseif ( $this->FeedType == '_POST_FLAT_FILE_PRICEANDQUANTITYONLY_UPDATE_DATA_' ) {
			$this->processListingPnqResults( $feed_rows, $result_rows );
		} else {
			$this->processListingDataResults( $feed_rows, $result_rows );			
		}

		// update feed status
		$this->success = sizeof( $this->warnings ) > 0 ? 'warning' : 'success';
		$this->success = sizeof( $this->errors ) > 0 ? 'error' : $this->success;
		$this->status = 'processed';
		$this->update();
		$this->logger->info('feed has been processed');

		return true;
	} // processSubmissionResult()

	public function processListingDataResults( $feed_rows, $result_rows ) {

		$lm = new WPLA_ListingsModel();

		// index results by SKU
		$results = array();
		foreach ( $result_rows as $r ) {
			if ( ! isset( $r['sku'] ) && isset( $r['SKU'] ) ) $r['sku'] = $r['SKU']; // translate column SKU -> sku
			if ( ! isset( $r['sku'] ) || empty( $r['sku'] ) ) continue;
			$results[ $r['sku'] ][] = $r;
			$this->logger->info('result sku: '.$r['sku']);
		}

		// process each result row
		foreach ($feed_rows as $row) {
			$listing_data = array();

			$row_sku = isset( $row['item_sku'] ) ? $row['item_sku'] : $row['sku'];
			if ( ! $row_sku ) {
				$this->logger->warn('skipping row without SKU: '.print_r($row,1));
				continue;
			}

			$row_results = isset( $results[ $row_sku ] ) ? $results[ $row_sku ] : false;
			$this->logger->info('processing feed sku: '.$row_sku);

			// check if this is a delete feed (Inventory Loader)
			$add_delete_column = isset($row['add-delete']) ? $row['add-delete'] : '';
			$is_delete_feed = $add_delete_column == 'x' ? true : false;

			// if there are no result rows for this SKU, set status to 'online'
			if ( ! $row_results ) {

				if ( $is_delete_feed ) {
					$listing = $lm->getItemBySKU( $row_sku );
					if ( ! $listing ) continue;
					if ( $listing->status == 'trashed' ) {
						$lm->deleteItem( $listing->id );
						$this->logger->info('DELETED listing ID '.$listing->id.' SKU: '.$row_sku);
					} else {					
						$this->logger->warn('INVALID listing status for deletion - ID '.$listing->id.' / SKU: '.$row_sku.' / status: '.$listing->status);
					}
					continue;
				}

				$listing_data['status']  = 'online';
				$listing_data['history'] = '';
				$lm->updateWhere( array( 'sku' => $row_sku, 'account_id' => $this->account_id ), $listing_data );				
				$this->logger->info('changed status to online: '.$row_sku);
				continue;

			}

			// handle errors and warnings
			$errors         = array();
			$warnings       = array();
			$processed_keys = array();
			foreach ($row_results as $row_result) {

				// translate error-type
				if ( $row_result['error-type'] == 'Fehler' ) 		$row_result['error-type'] = 'Error';	// amazon.de
				if ( $row_result['error-type'] == 'Warnung' ) 		$row_result['error-type'] = 'Warning';
				if ( $row_result['error-type'] == 'Erreur' ) 		$row_result['error-type'] = 'Error';	// amazon.fr
				if ( $row_result['error-type'] == 'Avertissement' ) $row_result['error-type'] = 'Warning';

				// compute hash to identify duplicate errors
				$row_key = md5( $row_result['sku'] . $row_result['error-code'] . $row_result['error-type'] . $row_result['original-record-number'] );

				// store feed id in error array
				$row_result['feed_id'] = $this->id;

				if ( 'Error' == $row_result['error-type'] ) {

					$this->logger->info('error: '.$row_sku.' - '.$row_key.' - '.$row_result['error-message']);
					if ( ! in_array($row_key, $processed_keys) ) {
						$errors[]         = $row_result;
						$processed_keys[] = $row_key;
					}

				} elseif ( 'Warning' == $row_result['error-type'] ) {

					$this->logger->info('warning: '.$row_sku.' - '.$row_key.' - '.$row_result['error-message']);
					if ( ! in_array($row_key, $processed_keys) ) {
						$warnings[]       = $row_result;
						$processed_keys[] = $row_key;
					}

				}

			} // foreach result row

			// update listing
			if ( ! empty( $errors ) ) {

				$listing_data['status']  = 'failed';
				$listing_data['history'] = serialize( array( 'errors' => $errors, 'warnings' => $warnings ) );
				$lm->updateWhere( array( 'sku' => $row_sku, 'account_id' => $this->account_id ), $listing_data );				
				$this->logger->info('changed status to FAILED: '.$row_sku);

				$this->errors   = array_merge( $this->errors, $errors);
				$this->warnings = array_merge( $this->warnings, $warnings);

			} elseif ( ! empty( $warnings ) ) {

				$listing_data['status']  = $is_delete_feed ? 'trashed' : 'online';
				$listing_data['history'] = serialize( array( 'errors' => $errors, 'warnings' => $warnings ) );
				$lm->updateWhere( array( 'sku' => $row_sku, 'account_id' => $this->account_id ), $listing_data );				

				$this->logger->info('changed status to online: '.$row_sku);
				$this->warnings = array_merge( $this->warnings, $warnings);

			}

		} // foreach row

	} // processListingDataResults()

	public function processListingPnqResults( $feed_rows, $result_rows ) {

		$lm = new WPLA_ListingsModel();

		// index results by SKU
		$results = array();
		foreach ( $result_rows as $r ) {
			if ( ! isset( $r['sku'] ) || empty( $r['sku'] ) ) continue;
			$results[ $r['sku'] ][] = $r;
			$this->logger->info('result sku: '.$r['sku']);
		}

		// process each result row
		foreach ($feed_rows as $row) {
			$listing_data = array();

			$row_sku = $row['sku'];
			if ( ! $row_sku ) {
				$this->logger->warn('skipping row without SKU: '.print_r($row,1));
				continue;
			}

			$row_results = isset( $results[ $row_sku ] ) ? $results[ $row_sku ] : false;
			$this->logger->info('processing feed sku: '.$row_sku);

			// if there are no result rows for this SKU, set status to 'online'
			if ( ! $row_results ) {

				$listing_data['pnq_status']  = '0';
				$lm->updateWhere( array( 'sku' => $row_sku, 'account_id' => $this->account_id ), $listing_data );				
				$this->logger->info('changed status to online: '.$row_sku);
				continue;

			}

			// handle errors and warnings
			$errors         = array();
			$warnings       = array();
			$processed_keys = array();
			foreach ($row_results as $row_result) {

				// translate error-type
				if ( $row_result['error-type'] == 'Fehler' ) 		$row_result['error-type'] = 'Error';	// amazon.de
				if ( $row_result['error-type'] == 'Warnung' ) 		$row_result['error-type'] = 'Warning';
				if ( $row_result['error-type'] == 'Erreur' ) 		$row_result['error-type'] = 'Error';	// amazon.fr
				if ( $row_result['error-type'] == 'Avertissement' ) $row_result['error-type'] = 'Warning';

				// compute hash to identify duplicate errors
				$row_key = md5( $row_result['sku'] . $row_result['error-code'] . $row_result['error-type'] . $row_result['original-record-number'] );

				if ( 'Error' == $row_result['error-type'] ) {

					$this->logger->info('error: '.$row_sku.' - '.$row_key.' - '.$row_result['error-message']);
					if ( ! in_array($row_key, $processed_keys) ) {
						$errors[]         = $row_result;
						$processed_keys[] = $row_key;
					}

				} elseif ( 'Warning' == $row_result['error-type'] ) {

					$this->logger->info('warning: '.$row_sku.' - '.$row_key.' - '.$row_result['error-message']);
					if ( ! in_array($row_key, $processed_keys) ) {
						$warnings[]       = $row_result;
						$processed_keys[] = $row_key;
					}

				}

			} // foreach result row

			// update listing
			if ( ! empty( $errors ) ) {

				$listing_data['pnq_status']  = '-1';
				$lm->updateWhere( array( 'sku' => $row_sku, 'account_id' => $this->account_id ), $listing_data );				
				$this->logger->info('changed PNQ status to FAILED (-1): '.$row_sku);

				$this->errors   = array_merge( $this->errors, $errors);
				$this->warnings = array_merge( $this->warnings, $warnings);

			} elseif ( ! empty( $warnings ) ) {

				$listing_data['pnq_status']  = '0';
				$lm->updateWhere( array( 'sku' => $row_sku, 'account_id' => $this->account_id ), $listing_data );				

				$this->logger->info('changed PNQ status to 0: '.$row_sku);
				$this->warnings = array_merge( $this->warnings, $warnings);

			}

		} // foreach row

	} // processListingPnqResults()

	public function processOrderFulfillmentResults( $feed_rows, $result_rows ) {

		$om = new WPLA_OrdersModel();

		// index results by OrderID
		$results = array();
		foreach ( $result_rows as $r ) {
			if ( ! isset( $r['order-id'] ) || empty( $r['order-id'] ) ) continue;
			$results[ $r['order-id'] ][] = $r;
			$this->logger->info('result order_id: '.$r['order-id']);
		}

		// process each result row
		foreach ($feed_rows as $row) {
			$order_data = array();

			$row_order_id = $row['order-id'];
			if ( ! $row_order_id ) {
				$this->logger->warn('skipping row without OrderID: '.print_r($row,1));
				continue;
			}

			$row_results = isset( $results[ $row_order_id ] ) ? $results[ $row_order_id ] : false;
			$this->logger->info('processing feed OrderID: '.$row_order_id);

			$order = $om->getOrderByOrderID( $row_order_id );
			$post_id = $order->post_id;

			// if there are no result rows for this OrderID, set status to 'Shipped'
			if ( ! $row_results ) {

				$order_data['status']  = 'Shipped';
				// $order_data['history'] = '';
				// $om->updateWhere( array( 'order_id' => $row_order_id, 'account_id' => $this->account_id ), $order_data );				
				$this->logger->info('changed status to Shipped: '.$row_order_id);
				if ( $post_id ) update_post_meta( $post_id, '_wpla_submission_result', 'success' );
				continue;

			}

			// handle errors and warnings
			$errors = array();
			$warnings = array();
			$this->logger->info('processing row results: '.print_r($row_results,1));
			foreach ($row_results as $row_result) {

				if ( 'Error' == $row_result['error-type'] ) {

					$this->logger->info('error: '.$row_order_id.' - '.$row_result['error-message']);
					$errors[] = $row_result;

				} elseif ( 'Warning' == $row_result['error-type'] ) {

					$this->logger->info('warning: '.$row_order_id.' - '.$row_result['error-message']);
					$warnings[] = $row_result;

				}

			} // foreach result row

			// update order
			if ( ! empty( $errors ) ) {

				$order_data['status']  = 'failed';
				// $order_data['history'] = serialize( array( 'errors' => $errors, 'warnings' => $warnings ) );
				// $om->updateWhere( array( 'order_id' => $row_order_id, 'account_id' => $this->account_id ), $order_data );				
				if ( $post_id ) update_post_meta( $post_id, '_wpla_submission_result', serialize( array( 'errors' => $errors, 'warnings' => $warnings ) ) );

				$this->logger->info('changed status to FAILED: '.$row_order_id);
				$this->errors   = array_merge( $this->errors, $errors);
				$this->warnings = array_merge( $this->warnings, $warnings);

			} elseif ( ! empty( $warnings ) ) {

				$order_data['status']  = 'Shipped';
				// $order_data['history'] = serialize( array( 'errors' => $errors, 'warnings' => $warnings ) );
				// $om->updateWhere( array( 'order_id' => $row_order_id, 'account_id' => $this->account_id ), $order_data );				
				if ( $post_id ) update_post_meta( $post_id, '_wpla_submission_result', serialize( array( 'errors' => $errors, 'warnings' => $warnings ) ) );

				$this->logger->info('changed status to Shipped: '.$row_order_id);
				$this->warnings = array_merge( $this->warnings, $warnings);

			}

		} // foreach row

	} // processOrderFulfillmentResults()

	public function processOrderFbaResults( $feed_rows, $result_rows ) {
		$om = new WPLA_OrdersModel();

		// index results by OrderID
		// TODO: examine real world processing result
		$results = array();
		foreach ( $result_rows as $r ) {
			if ( ! isset( $r['order-id'] ) || empty( $r['order-id'] ) ) continue;
			$results[ $r['order-id'] ][] = $r;
			$this->logger->info('result order_id: '.$r['order-id']);
		}

		// process each result row
		foreach ($feed_rows as $row) {
			$order_data = array();

			$row_order_id = str_replace( '#', '', $row['MerchantFulfillmentOrderID'] );
			if ( ! $row_order_id ) {
				$this->logger->warn('skipping row without OrderID: '.print_r($row,1));
				continue;
			}

			$row_results = isset( $results[ $row_order_id ] ) ? $results[ $row_order_id ] : false;
			$this->logger->info('processing feed OrderID: '.$row_order_id);

			// $order   = $om->getOrderByOrderID( $row_order_id );
			// $post_id = $order->post_id;
			$post_id = $row_order_id;

			// if there are no result rows for this OrderID, set FBA status to 'success'
			if ( ! $row_results ) {

				$this->logger->info('changed FBA status to success: '.$row_order_id);
				if ( $post_id ) update_post_meta( $post_id, '_wpla_fba_submission_status', 'success' );
				continue;
			}

			// handle errors and warnings
			// TODO: ...

		} // foreach row

	} // processOrderFbaResults()

	static public function processFeedsSubmissionList( $feeds, $account ) {

		$feeds_in_progress = 0;

		foreach ($feeds as $feed) {
			
			// check if feed exists
			$existing_record = WPLA_AmazonFeed::getFeedBySubmissionId( $feed->FeedSubmissionId );
			if ( $existing_record ) {

				$new_feed = new WPLA_AmazonFeed( $existing_record->id );

				$new_feed->FeedSubmissionId        = $feed->FeedSubmissionId;
				$new_feed->FeedType                = $feed->FeedType;
				$new_feed->FeedProcessingStatus    = $feed->FeedProcessingStatus;
				$new_feed->SubmittedDate           = $feed->SubmittedDate;
				$new_feed->CompletedProcessingDate = isset( $feed->CompletedProcessingDate ) ? $feed->CompletedProcessingDate : '';
				$new_feed->account_id              = $account->id;
				// $new_feed->results                 = maybe_serialize( $feed );

				// save new record
				$new_feed->update();

			} else {

				// add new record
				$new_feed = new WPLA_AmazonFeed();
				$new_feed->FeedSubmissionId        = $feed->FeedSubmissionId;
				$new_feed->FeedType                = $feed->FeedType;
				$new_feed->FeedProcessingStatus    = $feed->FeedProcessingStatus;
				$new_feed->SubmittedDate           = $feed->SubmittedDate;
				$new_feed->CompletedProcessingDate = isset( $feed->CompletedProcessingDate ) ? $feed->CompletedProcessingDate : '';
				$new_feed->date_created            = $feed->SubmittedDate;
				$new_feed->account_id              = $account->id;
				// $new_feed->results                 = maybe_serialize( $feed );

				// save new record
				$new_feed->add();
			}

			if ( ! $new_feed->results ) {
				$new_feed->loadSubmissionResult();
				$new_feed->processSubmissionResult();				
			}

			// check if feed is in progress
			if ( in_array( $feed->FeedProcessingStatus, array('_SUBMITTED_','_IN_PROGRESS_') ) ) {
				$feeds_in_progress++;
			}			

		}

		// update feed progress status
		update_option( 'wpla_feeds_in_progress', $feeds_in_progress );

	} // static processFeedsSubmissionList()


	function updatePendingFeeds() {
		$this->logger->info('updatePendingFeeds()');

		$accounts = WPLA_AmazonAccount::getAll();
		// $this->logger->info('found accounts: '.print_r($accounts,1));

		foreach ($accounts as $account ) {
			$this->updatePendingFeedForAccount( $account );
		}

	} // updatePendingFeeds()


	function updatePendingFeedForAccount( $account ) {
		$this->logger->info('updatePendingFeedForAccount('.$account->id.') - '.$account->title);
		$this->logger->info('------------------------------');
		$lm = new WPLA_ListingsModel();

		// build feed(s) for updated (changed,prepared,matched) products
		$this->logger->start('getGroupedPendingProductsForAccount');
		$grouped_items = $lm->getPendingProductsForAccount_GroupedByTemplateType( $account->id );
	   	$this->logger->logTime('getGroupedPendingProductsForAccount');
		$this->logger->info('found '.sizeof($grouped_items).' different templates to process...');
		// $this->logger->info('grouped items: '.print_r($grouped_items,1));
		// echo "<pre>";print_r($grouped_items);echo"</pre>";#die();

		// each template
		$processed_tpl_types = array();
		foreach ( $grouped_items as $tpl_id => $grouped_inner_items ) {

			// get template
			$template      = $tpl_id ? new WPLA_AmazonFeedTemplate( $tpl_id ) : false;
			$template_type = $template ? $template->name : 'Offer';

			// each profile
			foreach ( $grouped_inner_items as $profile_id => $items ) {

				$this->logger->info('building listing items feed for profile_id: '.$profile_id);
				$this->logger->info('TemplateType: '.$template_type.' - tpl_id: '.$tpl_id);
				$this->logger->info('number of items: '.sizeof($items));

				// get profile
				$profile  = new WPLA_AmazonProfile( $profile_id );

				// append if a feed with the same template type has been generated just now
				$append_feed = in_array( $template_type, $processed_tpl_types );

				// build Listing Data or ListingLoader feed
				$this->logger->start('buildFeed');
				$success = $this->buildFeed( '_POST_FLAT_FILE_LISTINGS_DATA_', $items, $account, $profile, $append_feed );
			   	$this->logger->logTime('buildFeed');
				
				// if a feed was created, add template type to list of processed templates
				if ( $success ) $processed_tpl_types[] = $template_type;				

			}

			// $this->logger->logSpentTime('parseProductColumn');
			// $this->logger->logSpentTime('parseProfileShortcode');
			// $this->logger->logSpentTime('parseVariationAttributeColumn');
			// $this->logger->logSpentTime('processAttributeShortcodes');
			// $this->logger->logSpentTime('processCustomMetaShortcodes');

		} // foreach $grouped_items


		// build Price and Quantity feed for this account
		$items = $lm->getAllProductsForAccountByPnqStatus( $account->id, 1 );
		$this->logger->info('number of PNQ items: '.sizeof($items));
		$this->buildFeed( '_POST_FLAT_FILE_PRICEANDQUANTITYONLY_UPDATE_DATA_', $items, $account );


		// build delete products feed for this account
		$items = $lm->getAllProductsInTrashForAccount( $account->id, 1 );
		$this->logger->info('listings in trash: '.sizeof($items));
		$this->buildFeed( '_POST_FLAT_FILE_INVLOADER_DATA_', $items, $account );


	} // updatePendingFeedForAccount()


	// deprecated - groups items by profile (better group by template type)
	function updatePendingFeedForAccount_v2( $account ) {
		$this->logger->info('updatePendingFeedForAccount_v2('.$account->id.') - '.$account->title);
		$this->logger->info('----------');
		$lm = new WPLA_ListingsModel();

		// build feed(s) for updated (changed,prepared,matched) products
		$this->logger->start('getGroupedPendingProductsForAccount');
		$grouped_items = $lm->getGroupedPendingProductsForAccount( $account->id );
	   	$this->logger->logTime('getGroupedPendingProductsForAccount');
		$this->logger->info('found '.sizeof($grouped_items).' different profiles to process...');
		// $this->logger->info('grouped items: '.print_r($grouped_items,1));

		$processed_tpl_types = array();
		foreach ( $grouped_items as $profile_id => $items ) {

			// get profile
			$profile  = new WPLA_AmazonProfile( $profile_id );
			$template = $profile->tpl_id ? new WPLA_AmazonFeedTemplate( $profile->tpl_id ) : false;
			$template_type = $template ? $template->name : 'Offer';

			$this->logger->info('building listing items feed for profile_id: '.$profile_id);
			$this->logger->info('TemplateType according to db: '.$template_type.' - tpl_id: '.$profile->tpl_id);
			$this->logger->info('number of items: '.sizeof($items));

			// skip feed if a feed with the same template type has been generated just now
			// TODO: merge feeds by template type instead of profile_id
			if ( in_array( $template_type, $processed_tpl_types) ) {
				$this->logger->info('*** SKIPPED feed - other feed was already built for template '.$template_type);
				continue;
			}


			// build Listing Data or ListingLoader feed
			$this->logger->start('buildFeed');
			$success = $this->buildFeed( '_POST_FLAT_FILE_LISTINGS_DATA_', $items, $account, $profile );
		   	$this->logger->logTime('buildFeed');

			// if a feed was created, add template type to list of processed templates
			if ( $success ) $processed_tpl_types[] = $template_type;				

			$this->logger->logSpentTime('parseProductColumn');
			$this->logger->logSpentTime('parseProfileShortcode');
			$this->logger->logSpentTime('parseVariationAttributeColumn');
			$this->logger->logSpentTime('processAttributeShortcodes');
			$this->logger->logSpentTime('processCustomMetaShortcodes');

		} // foreach $grouped_items


		// build Price and Quantity feed for this account
		$items = $lm->getAllProductsForAccountByPnqStatus( $account->id, 1 );
		$this->logger->info('number of PNQ items: '.sizeof($items));
		$this->buildFeed( '_POST_FLAT_FILE_PRICEANDQUANTITYONLY_UPDATE_DATA_', $items, $account );


		// build delete products feed for this account
		$items = $lm->getAllProductsInTrashForAccount( $account->id, 1 );
		$this->logger->info('listings in trash: '.sizeof($items));
		$this->buildFeed( '_POST_FLAT_FILE_INVLOADER_DATA_', $items, $account );


	} // updatePendingFeedForAccount_v2()


	// deprecated
	function updatePendingFeedForAccount_v1( $account ) {
		$this->logger->info('updatePendingFeedForAccount_v1('.$account->id.') - '.$account->title);
		$this->logger->info('----------');
		$lm = new WPLA_ListingsModel();


		// build feed for changed (updated) products
		// $items = $lm->getAllChangedForAccount( $account->id );
		// $this->logger->info('changed items: '.sizeof($items));
		// if ( $items )
		// 	$this->buildFeed( '_POST_FLAT_FILE_PRICEANDQUANTITYONLY_UPDATE_DATA_', $items, $account );


		// build feed(s) for updated (changed) products
		$grouped_items = $lm->getGroupedProductsByStatus( $account->id, 'changed' );
		// $this->logger->info('changed items: '.print_r($grouped_items,1));

		foreach ( $grouped_items as $profile_id => $items ) {
			$this->logger->info('building changed items feed for profile_id: '.$profile_id);
			$this->logger->info('changed items: '.sizeof($items));

			// build Listing Data feed - if items have a profile with feed template assigned
			$profile  = new WPLA_AmazonProfile( $profile_id );
			// $template = new WPLA_AmazonFeedTemplate( $profile->tpl_id );
			// $this->logger->info('profile object: '.print_r($profile,1));
			// $this->logger->info('template object: '.print_r($template,1));

			// build Listing Data or ListingLoader feed
			$this->buildFeed( '_POST_FLAT_FILE_LISTINGS_DATA_', $items, $account, $profile );

			/*
			// don't create Price & Quantity feeds for changed items - they don't contain sale price and condition data
			if ( $profile_id && $template && $template->id ) {
				// build Listing Data feed
				$this->buildFeed( '_POST_FLAT_FILE_LISTINGS_DATA_', $items, $account, $profile );
			} else {
				// build Price & Quantity feed
				$this->buildFeed( '_POST_FLAT_FILE_PRICEANDQUANTITYONLY_UPDATE_DATA_', $items, $account );
			}
			*/

		}


		// build feed(s) for new (prepared) products
		$grouped_items = $lm->getGroupedProductsByStatus( $account->id, 'prepared' );
		// $this->logger->info('prepared items: '.print_r($grouped_items,1));

		foreach ( $grouped_items as $profile_id => $items ) {
			$this->logger->info('building new items feed for profile_id: '.$profile_id);
			$this->logger->info('prepared items: '.sizeof($items));

			$profile  = new WPLA_AmazonProfile( $profile_id );
			// $template = new WPLA_AmazonFeedTemplate( $profile->tpl_id );

			// if ( ! $template ) continue;
			$this->buildFeed( '_POST_FLAT_FILE_LISTINGS_DATA_', $items, $account, $profile );
		}


		// build feed(s) for matched (pending) products
		$grouped_items = $lm->getGroupedProductsByStatus( $account->id, 'matched' );
		// $this->logger->info('pending items: '.print_r($grouped_items,1));

		foreach ( $grouped_items as $profile_id => $items ) {
			$this->logger->info('building matched items feed for profile_id: '.$profile_id);
			$this->logger->info('matched items: '.sizeof($items));

			$profile  = new WPLA_AmazonProfile( $profile_id );
			// $template = new WPLA_AmazonFeedTemplate( $profile->tpl_id );

			// if ( ! $template ) continue;
			$this->buildFeed( '_POST_FLAT_FILE_LISTINGS_DATA_', $items, $account, $profile );
		}

	} // updatePendingFeedForAccount_v1()


	// build feed for updated products
	function buildFeed( $feed_type, $items, $account, $profile = false, $append_feed = false ) {
		$this->logger->info('buildFeed() '.$feed_type.' - account id: '.$account->id);
		$this->logger->info('items count: '.sizeof($items));
		// $this->logger->info('items: '.print_r($items,1));

		// limit feed size to prevent timeout
		$max_feed_size = get_option( 'wpla_max_feed_size', 1000 );
		if ( sizeof($items) > $max_feed_size ) {
			$items = array_slice( $items, 0, $max_feed_size );
		}
	
		// generate CSV data
		switch ( $feed_type ) {
			case '_POST_FLAT_FILE_PRICEANDQUANTITYONLY_UPDATE_DATA_':
				# price and quantity feed
				$this->logger->info('building price and quantity feed...');			
				$this->logger->start('buildPriceAndQuantityFeedData');
				$csv_object = WPLA_FeedDataBuilder::buildPriceAndQuantityFeedData( $items, $account->id );
			   	$this->logger->logTime('buildPriceAndQuantityFeedData');
				break;
			
			case '_POST_FLAT_FILE_LISTINGS_DATA_':
				# new products feed
				$this->logger->info('building new products feed...');			
				$this->logger->start('buildNewProductsFeedData');
				$csv_object = WPLA_FeedDataBuilder::buildNewProductsFeedData( $items, $account->id, $profile, $append_feed );
			   	$this->logger->logTime('buildNewProductsFeedData');
				break;
			
			case '_POST_FLAT_FILE_INVLOADER_DATA_':
				# delete products feed (Inventory Loader)
				$this->logger->info('building delete products feed...');			
				$this->logger->start('buildInventoryLoaderFeedData');
				$csv_object = WPLA_FeedDataBuilder::buildInventoryLoaderFeedData( $items, $account->id, $profile );
			   	$this->logger->logTime('buildInventoryLoaderFeedData');
				break;
			
			default:
				# default
				$this->logger->error('unsupported feed type '.$feed_type);
				$csv_object = false;
				break;
		}

		if ( ! $csv_object || empty( $csv_object->data ) ) {
			$this->logger->warn('no feed data - not creating feed');
			return false;
		}
		// $this->logger->info('CSV: '.$csv_object->data);

		// // extract TemplateType from listing data feed
		// $template_name = '';
		// if ( preg_match('/TemplateType=(.*)\t/U', $csv_object->data, $matches) ) {
		// 	$template_name = $matches[1];
		// 	$this->logger->info('TemplateType: '.$template_name);
		// }

		// get template name / type from CSV object
		$template_name = '';
		if ( '_POST_FLAT_FILE_LISTINGS_DATA_' == $feed_type ) {
			$template_name = $csv_object->template_type;
			$this->logger->info('TemplateType: '.$template_name);
		}
		if ( '_POST_FLAT_FILE_INVLOADER_DATA_' == $feed_type ) {
			$template_name = 'Product Removal';
		}

		// set feed properties (required since $this is recycled here...)
		$this->data                 = $csv_object->data;
		$this->line_count           = sizeof( $items );
		$this->FeedType             = $feed_type;
		$this->template_name        = $template_name;
		$this->FeedProcessingStatus = 'pending';
		$this->status               = 'pending';
		$this->account_id           = $account->id;
		$this->date_created         = date('Y-m-d H:i:s');

		// check if a pending feed of this type already exists
		$existing_feed_id = $this->getPendingFeedId( $feed_type, $template_name, $account->id );
		// echo "<pre>template name: ";print_r($template_name);echo"</pre>";
		// echo "<pre>existing feed: ";print_r($existing_feed_id);echo"</pre>";

		if ( $existing_feed_id && $append_feed ) {

			// update existing feed (append)
			$existing_feed       = $this->getFeed( $existing_feed_id );
			$this->data          = $existing_feed->data ."\n" . $csv_object->data;
			$this->id            = $existing_feed_id;
			$this->template_name = $existing_feed->template_name;
			$this->line_count   += $existing_feed->line_count;
			$this->update();
			$this->logger->info('appended content to existing feed '.$this->id);			

		} elseif ( $existing_feed_id && ! $append_feed ) {

			// update existing feed (replace)
			$this->id = $existing_feed_id;
			$this->update();
			$this->logger->info('updated existing feed '.$this->id);			

		} else {

			// add new feed
			$this->id = null;
			$this->add();
			$this->logger->info('added NEW feed - id '.$this->id);

		}

		$this->logger->info('feed was built - '.$this->id);	
		$this->logger->info('------');

		return true;
	} // buildFeed()



	// build feed for shipped orders - $post_id refers to the internal WooCommerce order ID
	function updateShipmentFeed( $post_id ) {

		$feed_type = '_POST_FLAT_FILE_FULFILLMENT_DATA_';
		$order_id  = get_post_meta( $post_id, '_wpla_amazon_order_id', true );

		$om        = new WPLA_OrdersModel();
		$order     = $om->getOrderByOrderID( $order_id );

		$account   = new WPLA_AmazonAccount( $order->account_id );
		// echo "<pre>";print_r($account);echo"</pre>";die();

		$this->logger->info('updateShipmentFeed() '.$feed_type.' - order id: '.$order_id);
		$this->logger->info('updateShipmentFeed() - post id: '.$post_id.' - account id: '.$account->id);
	
		// create pending feed if it doesn't exist
		if ( ! $this->id = $this->getPendingFeedId( $feed_type, null, $account->id ) ) {

			# build feed data
			$this->logger->info('building shipment data feed...');			
			$csv = WPLA_FeedDataBuilder::buildShippingFeedData( $post_id, $order_id, $account->id, true );

			if ( ! $csv ) {
				$this->logger->warn('no feed data - not creating feed');
				return;
			}

			// add new feed
			$this->FeedType      = $feed_type;
			$this->status        = 'pending';
			$this->account_id    = $account->id;
			$this->date_created  = date('Y-m-d H:i:s');
			$this->data          = $csv;
			$this->add();
			$this->logger->info('added NEW feed - id '.$this->id);

		} else {
			$this->logger->info('found existing feed '.$this->id);			
			$existing_feed = new WPLA_AmazonFeed( $this->id );

			# append feed data
			$this->logger->info('updating shipment data feed...');			
			$csv = WPLA_FeedDataBuilder::buildShippingFeedData( $post_id, $order_id, $account->id, false );
			$this->data          = $existing_feed->data . $csv;

		}

		// update feed
		$this->line_count           = sizeof( $csv );
		$this->FeedProcessingStatus = 'pending';
		$this->date_created         = date('Y-m-d H:i:s');
		$this->update();
		$this->logger->info('feed was built and updated - '.$this->id);			

	} // updateShipmentFeed()




	// build feed for shipping WooCommerce orders via FBA - $order_post_id refers to the internal WooCommerce order ID
	function updateFbaSubmissionFeed( $order_post_id ) {

		// get order and items
		$_order      = wc_get_order( $order_post_id );
		$order_items = $_order->get_items();
		$this->logger->info('updateFbaSubmissionFeed() - no. of items: '.count($order_items) );

		foreach ( $order_items as $order_item ) {
			$this->processFbaSubmissionOrderItem( $order_item, $_order );
		}

	} // updateFbaSubmissionFeed()

	function processFbaSubmissionOrderItem( $order_item, $_order ) {

		// Flat File FBA Shipment Injection Fulfillment Feed
		$feed_type = '_POST_FLAT_FILE_FULFILLMENT_ORDER_REQUEST_DATA_'; 

		// use account from first order item (for now)
		$lm = new WPLA_ListingsModel();
		$post_id    = $order_item['variation_id'] ? $order_item['variation_id'] : $order_item['product_id'];
		$listing    = $lm->getItemByPostID( $post_id );
		$account_id = $listing->account_id;
		$account    = new WPLA_AmazonAccount( $account_id );

		$this->logger->info('updateFbaSubmissionFeed() '.$feed_type.' - post id: '.$post_id.' - account id: '.$account->id);

		// create pending feed if it doesn't exist
		if ( ! $this->id = $this->getPendingFeedId( $feed_type, null, $account->id ) ) {

			# build feed data
			$this->logger->info('building FBA submission feed...');			
			$csv = WPLA_FeedDataBuilder::buildFbaSubmissionFeedData( $post_id, $_order, $order_item, $listing, $account->id, true );

			if ( ! $csv ) {
				$this->logger->warn('no feed data - not creating feed');
				return;
			}

			// add new feed
			$this->FeedType      = $feed_type;
			$this->status        = 'pending';
			$this->account_id    = $account->id;
			$this->date_created  = date('Y-m-d H:i:s');
			$this->data          = $csv;
			$this->add();
			$this->logger->info('added NEW feed - id '.$this->id);

		} else {
			$this->logger->info('found existing feed '.$this->id);			
			$existing_feed = new WPLA_AmazonFeed( $this->id );

			# append feed data
			$this->logger->info('updating FBA submission feed...');			
			$csv = WPLA_FeedDataBuilder::buildFbaSubmissionFeedData( $post_id, $_order, $order_item, $listing, $account->id, false );
			$this->data          = $existing_feed->data . $csv;

		}

		// update feed
		$this->line_count           = sizeof( $csv );
		$this->FeedProcessingStatus = 'pending';
		$this->date_created         = date('Y-m-d H:i:s');
		$this->update();
		$this->logger->info('feed was built and updated - '.$this->id);			

	} // processFbaSubmissionOrderItem()



	// add feed
	function add() {
		global $wpdb;
		$table = $wpdb->prefix . self::TABLENAME;

		$data = array();
		foreach ( $this->fieldnames as $key ) {
			if ( isset( $this->$key ) ) {
				$data[ $key ] = $this->$key;
			} 
		}

		if ( sizeof( $data ) > 0 ) {
			$result = $wpdb->insert( $table, $data );
			echo $wpdb->last_error;

			$this->id = $wpdb->insert_id;
			return $this->id;		
		}

	}

	// update feed
	function update() {
		global $wpdb;
		$table = $wpdb->prefix . self::TABLENAME;

		$data = array();
		foreach ( $this->fieldnames as $key ) {
			if ( isset( $this->$key ) ) {
				$data[ $key ] = $this->$key;
			} 
		}

		if ( sizeof( $data ) > 0 ) {
			$result = $wpdb->update( $table, $data, array( 'id' => $this->id ) );
			echo $wpdb->last_error;
			// echo "<pre>";print_r($wpdb->last_query);echo"</pre>";#die();

			// return $wpdb->insert_id;		
		}

	}


	function delete() {
		global $wpdb;
		$table = $wpdb->prefix . self::TABLENAME;

		if ( ! $this->id ) return;

		$wpdb->delete( $table, array( 'id' => $this->id ), array( '%d' ) );
		echo $wpdb->last_error;
	}


	function getRecordTypeName( $type ) {
		if ( isset( $this->types[$type] ) ) {
			return $this->types[$type];
		}
		return $type;
	}



	function getPageItems( $current_page, $per_page ) {
		global $wpdb;
		$table = $wpdb->prefix . self::TABLENAME;

		$orderby  = (!empty($_REQUEST['orderby'])) ? esc_sql( $_REQUEST['orderby'] ) : 'date_created DESC, SubmittedDate'; //If no sort, default to title
		$order    = (!empty($_REQUEST['order']))   ? esc_sql( $_REQUEST['order']   ) : 'desc'; //If no order, default to asc
		$offset   = ( $current_page - 1 ) * $per_page;
		$per_page = esc_sql( $per_page );

        // handle filters
        $where_sql = ' WHERE 1 = 1 ';

        // views
        if ( isset( $_REQUEST['feed_status'] ) ) {
            $status = esc_sql( $_REQUEST['feed_status'] );
            // if ( in_array( $status, array('Success','Error','pending','unknown') ) ) {
            if ( $status ) {
                if ( $status == 'unknown' ) {
                    $where_sql .= " AND status IS NULL ";
                } else {
                    $where_sql .= " AND status = '$status' ";
                }
            }
        }

        // filter account_id
		$account_id = ( isset($_REQUEST['account_id']) ? esc_sql( $_REQUEST['account_id'] ) : false);
		if ( $account_id ) {
			$where_sql .= "
				 AND account_id = '".$account_id."'
			";
		} 

        // search box
        if ( isset( $_REQUEST['s'] ) ) {
            $query = esc_sql( $_REQUEST['s'] );
            $where_sql .= " AND ( 
                                    ( FeedSubmissionId = '$query' ) OR 
                                    ( FeedType = '$query' ) OR
                                    ( data LIKE '%$query%' ) OR
                                    ( results LIKE '%$query%' ) OR
                                    ( FeedProcessingStatus LIKE '%$query%' ) OR
                                    ( success LIKE '%$query%' ) 
                                )
                            /* AND NOT amazon_id = 0 */
                            ";
        }

        // get items
		$items = $wpdb->get_results("
			SELECT *
			FROM $table
            $where_sql
			ORDER BY $orderby $order
            LIMIT $offset, $per_page
		", ARRAY_A);

		// get total items count - if needed
		if ( ( $current_page == 1 ) && ( count( $items ) < $per_page ) ) {
			$this->total_items = count( $items );
		} else {
			$this->total_items = $wpdb->get_var("
				SELECT COUNT(*)
				FROM $table
	            $where_sql
				ORDER BY $orderby $order
			");			
		}

		foreach( $items as &$pfeed ) {
			$pfeed['FeedTypeName'] = $this->getRecordTypeName( $pfeed['FeedType'] );
		}

		return $items;
	} // getPageItems()

	static function getStatusSummary() {
		global $wpdb;
		$table = $wpdb->prefix . self::TABLENAME;

		$result = $wpdb->get_results("
			SELECT status, count(*) as total
			FROM $table
			GROUP BY status
		");

		$summary = new stdClass();
		foreach ($result as $row) {
            $status = $row->status ? $row->status : 'unknown';
			$summary->$status = $row->total;
		}

		// count total items as well
		$total_items = $wpdb->get_var("
			SELECT COUNT( id ) AS total_items
			FROM $table
		");
		$summary->total_items = $total_items;

		return $summary;
	} // getStatusSummary()





	function csv_to_array( $input, $delimiter = "\t" ) {

		$header  = null;
		$data    = array();
		$csvData = str_getcsv( $input, "\n", '' );
		$line = 0;

		// echo "<pre>";print_r($csvData);echo"</pre>";die();

	    foreach( $csvData as $csvLine ) {

            if ( $csvLine == null ) continue; // skip empty lines

	        if ( is_null($header) ) {
	        	$header = explode($delimiter, $csvLine);	
	        } else{

	            $items = explode($delimiter, $csvLine);

            	// $line++;
            	// echo "line $line <br>";

	            for ( $n = 0, $m = count($header); $n < $m; $n++ ){
	                $prepareData[$header[$n]] = $items[$n];
	            }

	            $data[] = $prepareData;
	        }
	    }

	    return $data;
	} // csv_to_array()


} // WPLA_AmazonFeed()



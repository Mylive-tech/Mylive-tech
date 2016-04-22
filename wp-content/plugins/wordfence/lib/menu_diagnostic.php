<?php
$diagnostic = new wfDiagnostic;
$plugins = get_plugins();
$activePlugins = array_flip(get_option('active_plugins'));
$activeNetworkPlugins = is_multisite() ? array_flip(wp_get_active_network_plugins()) : array();
$muPlugins = get_mu_plugins();
$themes = wp_get_themes();
$currentTheme = wp_get_theme();
$cols = 3;

$w = new wfConfig();
?>

<div class="wrap wordfence">
	<?php require('menuHeader.php'); ?>
	<h2 id="wfHeading">
		Diagnostics
	</h2>
	<br clear="both"/>

	<form id="wfConfigForm">
		<table class="wf-table"<?php echo !empty($inEmail) ? ' border=1' : '' ?>>
			<?php foreach ($diagnostic->getResults() as $title => $tests): ?>
				<tbody class="thead">
				<tr>
					<th colspan="<?php echo $cols ?>"><?php echo esc_html($title) ?></th>
				</tr>
				</tbody>
				<tbody>
				<?php foreach ($tests as $result): ?>
					<tr>
						<td style="width: 75%;"
						    colspan="<?php echo $cols - 1 ?>"><?php echo wp_kses($result['label'], array(
								'code'   => array(),
								'strong' => array(),
								'em'     => array(),
								'a'      => array('href' => true),
							)) ?></td>
						<?php if ($result['test']): ?>
							<td class="success"><?php echo esc_html($result['message']) ?></td>
						<?php else: ?>
							<td class="error"><?php echo esc_html($result['message']) ?></td>
						<?php endif ?>
					</tr>
				<?php endforeach ?>
				</tbody>
				<tbody class="empty-row">
				<tr>
					<td colspan="<?php echo $cols ?>"></td>
				</tr>
				</tbody>
			<?php endforeach ?>

			<tbody class="thead">
			<tr>
				<th>IPs</th>
				<th>Value</th>
				<th>Used</th>
			</tr>
			</tbody>
			<tbody>
			<?php
			$howGet = wfConfig::get('howGetIPs', false);
			list($currentIP, $currentServerVarForIP) = wfUtils::getIPAndServerVarible();
			foreach (array(
				         'REMOTE_ADDR'           => 'REMOTE_ADDR',
				         'HTTP_CF_CONNECTING_IP' => 'CF-Connecting-IP',
				         'HTTP_X_REAL_IP'        => 'X-Real-IP',
				         'HTTP_X_FORWARDED_FOR'  => 'X-Forwarded-For',
			         ) as $variable => $label): ?>
				<tr>
					<td><?php echo $label ?></td>
					<td><?php echo esc_html(array_key_exists($variable, $_SERVER) ? $_SERVER[$variable] : '(not set)') ?></td>
					<?php if ($currentServerVarForIP && $currentServerVarForIP === $variable): ?>
						<td class="success">In use</td>
					<?php elseif ($howGet === $variable): ?>
						<td class="error">Configured, but not valid</td>
					<?php else: ?>
						<td></td>
					<?php endif ?>
				</tr>
			<?php endforeach ?>
			</tbody>
			<tbody class="empty-row">
			<tr>
				<td colspan="<?php echo $cols ?>"></td>
			</tr>
			</tbody>

			<tbody class="thead">
			<tr>
				<th colspan="<?php echo $cols ?>">WordPress Plugins</th>
			</tr>
			</tbody>
			<tbody>
			<?php foreach ($plugins as $plugin => $pluginData): ?>
				<tr>
					<td colspan="<?php echo $cols - 1 ?>"><strong><?php echo esc_html($pluginData['Name']) ?></strong>
						<?php if (!empty($pluginData['Version'])): ?>
							- Version <?php echo esc_html($pluginData['Version']) ?>
						<?php endif ?>
					</td>
					<?php if (array_key_exists(trailingslashit(WP_PLUGIN_DIR) . $plugin, $activeNetworkPlugins)): ?>
						<td class="success">Network Activated</td>
					<?php elseif (array_key_exists($plugin, $activePlugins)): ?>
						<td class="success">Active</td>
					<?php else: ?>
						<td class="inactive">Inactive</td>
					<?php endif ?>
				</tr>
			<?php endforeach ?>
			</tbody>

			<tbody class="empty-row">
			<tr>
				<td colspan="<?php echo $cols ?>"></td>
			</tr>
			</tbody>
			<tbody class="thead">
			<tr>
				<th colspan="<?php echo $cols ?>">Must-Use WordPress Plugins</th>
			</tr>
			</tbody>
			<?php if (!empty($muPlugins)): ?>
				<tbody>
				<?php foreach ($muPlugins as $plugin => $pluginData): ?>
					<tr>
						<td colspan="<?php echo $cols - 1 ?>">
							<strong><?php echo esc_html($pluginData['Name']) ?></strong>
							<?php if (!empty($pluginData['Version'])): ?>
								- Version <?php echo esc_html($pluginData['Version']) ?>
							<?php endif ?>
						</td>
						<td class="success">Active</td>
					</tr>
				<?php endforeach ?>
				</tbody>
			<?php else: ?>
				<tbody>
				<tr>
					<td colspan="<?php echo $cols ?>">No MU-Plugins</td>
				</tr>
				</tbody>

			<?php endif ?>

			<tbody class="empty-row">
			<tr>
				<td colspan="<?php echo $cols ?>"></td>
			</tr>
			</tbody>
			<tbody class="thead">
			<tr>
				<th colspan="<?php echo $cols ?>">Themes</th>
			</tr>
			</tbody>
			<?php if (!empty($themes)): ?>
				<tbody>
				<?php foreach ($themes as $theme => $themeData): ?>
					<tr>
						<td colspan="<?php echo $cols - 1 ?>">
							<strong><?php echo esc_html($themeData['Name']) ?></strong>
							Version <?php echo esc_html($themeData['Version']) ?></td>
						<?php if ($currentTheme instanceof WP_Theme && $theme === $currentTheme->get_stylesheet()): ?>
							<td class="success">Active</td>
						<?php else: ?>
							<td class="inactive">Inactive</td>
						<?php endif ?>
					</tr>
				<?php endforeach ?>
				</tbody>
			<?php else: ?>
				<tbody>
				<tr>
					<td colspan="<?php echo $cols ?>">No MU-Plugins</td>
				</tr>
				</tbody>

			<?php endif ?>

			<tbody class="empty-row">
			<tr>
				<td colspan="<?php echo $cols ?>"></td>
			</tr>
			</tbody>
			<tbody class="thead">
			<tr>
				<th colspan="<?php echo $cols ?>">Cron Jobs</th>
			</tr>
			</tbody>
			<tbody>
			<?php
			$cron = _get_cron_array();

			foreach ($cron as $timestamp => $values) {
				if (is_array($values)) {
					foreach ($values as $cron_job => $v) {
						if (is_numeric($timestamp)) {
							?>
							<tr>
								<td colspan="<?php echo $cols - 1 ?>"><?php echo esc_html(date('r', $timestamp)) ?></td>
								<td><?php echo esc_html($cron_job) ?></td>
							</tr>
							<?php
						}
					}
				}
			}
			?>
			</tbody>
		</table>
		<?php
		$wfdb = new wfDB();
		$q = $wfdb->querySelect("show table status");
		if ($q):
			$databaseCols = count($q[0]);
			?>
			<div style="max-width: 100%; overflow: auto; padding: 1px;">
				<table class="wf-table"<?php echo !empty($inEmail) ? ' border=1' : '' ?>>
					<tbody class="empty-row">
					<tr>
						<td colspan="<?php echo $databaseCols ?>"></td>
					</tr>
					</tbody>
					<tbody class="thead">
					<tr>
						<th colspan="<?php echo $databaseCols ?>">Database Tables</th>
					</tr>
					</tbody>
					<tbody class="thead thead-subhead" style="font-size: 85%">
					<?php
					$val = array_shift($q);
					?>
					<tr>
						<?php foreach ($val as $tkey => $tval): ?>
							<th><?php echo esc_html($tkey) ?></th>
						<?php endforeach; ?>
					</tr>
					</tbody>
					<tbody style="font-size: 85%">
					<?php
					foreach ($q as $val): ?>
						<tr>
							<?php foreach ($val as $tkey => $tval): ?>
								<td><?php echo esc_html($tval) ?></td>
							<?php endforeach; ?>
						</tr>
					<?php endforeach; ?>
					</tbody>

				</table>
			</div>
		<?php endif ?>
	</form>
</div>

<?php if (!empty($inEmail)): ?>
	<?php phpinfo(); ?>
<?php endif ?>

<?php if (!empty($emailForm)): ?>
	<h3>Other Tests</h3>

	<ul>
		<li>
			<a href="<?php echo wfUtils::siteURLRelative(); ?>?_wfsf=sysinfo&nonce=<?php echo wp_create_nonce('wp-ajax'); ?>"
			   target="_blank">Click to view your system's configuration in a new window</a>
			<a href="http://docs.wordfence.com/en/Wordfence_options#Click_to_view_your_system.27s_configuration_in_a_new_window"
			   target="_blank" class="wfhelp"></a></li>
		<li>
			<a href="<?php echo wfUtils::siteURLRelative(); ?>?_wfsf=testmem&nonce=<?php echo wp_create_nonce('wp-ajax'); ?>"
			   target="_blank">Test your WordPress host's available memory</a>
			<a href="http://docs.wordfence.com/en/Wordfence_options#Test_your_WordPress_host.27s_available_memory"
			   target="_blank" class="wfhelp"></a>
		</li>
		<li>
			Send a test email from this WordPress server to an email address:<a
				href="http://docs.wordfence.com/en/Wordfence_options#Send_a_test_email_from_this_WordPress_server_to_an_email_address"
				target="_blank" class="wfhelp"></a>
			<input type="text" id="testEmailDest" value="" size="20" maxlength="255" class="wfConfigElem"/>
			<input class="button" type="button" value="Send Test Email"
			       onclick="WFAD.sendTestEmail(jQuery('#testEmailDest').val());"/>
		</li>
	</ul>

	<div id="sendByEmailThanks" class="hidden">
		<h3>Thanks for sending your diagnostic page over email</h3>
	</div>
	<div id="sendByEmailDiv">
		<h3>Send Report by Email</h3>

		<div id="sendByEmailForm" class="hidden">
			<table class="wfConfigForm">
				<tr>
					<th>Email address:</th>
					<td><input type="email" id="_email" value="samples@wordfence.com"/></td>
					<td colspan="2"><input class="button" type="button" id="doSendEmail" value="Send"/></td>
				</tr>
			</table>
		</div>
		<input class="button" type="submit" id="sendByEmail" value="Send Report by Email"/>
	</div>

	<?php if (!WFWAF_SUBDIRECTORY_INSTALL): ?>
	<div id="updateWAFRules">
		<h3>Firewall Rules</h3>

		<p>
			<button type="button" onclick="WFAD.wafUpdateRules()" class="button button-primary">
				Manually refresh firewall rules
			</button>
<!--			<em id="waf-rules-last-updated"></em>-->
		</p>
		<?php
		try {
			$lastUpdated = wfWAF::getInstance()->getStorageEngine()->getConfig('rulesLastUpdated');
		} catch (wfWAFStorageFileException $e) {
			error_log($e->getMessage());
		}
		if (!empty($lastUpdated)): ?>
			<script>
				var lastUpdated = <?php echo (int) $lastUpdated ?>;
				WFAD.renderWAFRulesLastUpdated(new Date(lastUpdated * 1000));
			</script>
		<?php endif ?>

	</div>
	<?php endif ?>

	<h3>Debugging Options</h3>
	<form action="#" id="wfDebuggingConfigForm">
		<table class="wfConfigForm">
			<tr>
				<th>Add a debugging comment to HTML source of cached pages.<a
						href="http://docs.wordfence.com/en/Wordfence_options#Add_a_debugging_comment_to_HTML_source_of_cached_pages"
						target="_blank" class="wfhelp"></a></th>
				<td><input type="checkbox" id="addCacheComment" class="wfConfigElem" name="addCacheComment"
				           value="1" <?php $w->cb('addCacheComment'); ?> />
				</td>
			</tr>

			<tr>
				<th>Enable debugging mode (increases database load)<a
						href="http://docs.wordfence.com/en/Wordfence_options#Enable_debugging_mode_.28increases_database_load.29"
						target="_blank" class="wfhelp"></a></th>
				<td><input type="checkbox" id="debugOn" class="wfConfigElem" name="debugOn"
				           value="1" <?php $w->cb('debugOn'); ?> /></td>
			</tr>

			<tr>
				<th>Start all scans remotely<a
						href="http://docs.wordfence.com/en/Wordfence_options#Start_all_scans_remotely"
						target="_blank" class="wfhelp"></a></th>
				<td><input type="checkbox" id="startScansRemotely" class="wfConfigElem" name="startScansRemotely"
				           value="1" <?php $w->cb('startScansRemotely'); ?> />
					(Try this if your scans aren't starting and your site is publicly accessible)
				</td>
			</tr>
			<tr>
				<th>Disable config caching<a
						href="http://docs.wordfence.com/en/Wordfence_options#Disable_config_caching" target="_blank"
						class="wfhelp"></a></th>
				<td><input type="checkbox" id="disableConfigCaching" class="wfConfigElem"
				           name="disableConfigCaching" value="1" <?php $w->cb('disableConfigCaching'); ?> />
					(Try this if your options aren't saving)
				</td>
			</tr>

			<tr>
				<th><label for="ssl_verify">Enable SSL Verification</label><a
						href="http://docs.wordfence.com/en/Wordfence_options#Enable_SSL_Verification"
						target="_blank" class="wfhelp"></a>
				</th>
				<td style="vertical-align: top;"><input type="checkbox" id="ssl_verify" class="wfConfigElem"
				                                        name="ssl_verify"
				                                        value="1" <?php $w->cb('ssl_verify'); ?> />
					(Disable this if you are <strong><em>consistently</em></strong> unable to connect to the Wordfence
					servers.)
				</td>
			</tr>

			<tr>
				<th><label for="betaThreatDefenseFeed">Enable beta threat defense feed</label></th>
				<td style="vertical-align: top;"><input type="checkbox" id="betaThreatDefenseFeed"
				                                        class="wfConfigElem"
				                                        name="betaThreatDefenseFeed"
				                                        value="1" <?php $w->cb('betaThreatDefenseFeed'); ?> />
				</td>
			</tr>

		</table>
		<br>
		<table border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td><input type="button" id="button1" name="button1" class="button-primary" value="Save Changes"
				           onclick="WFAD.saveDebuggingConfig();"/></td>
				<td style="height: 24px;">
					<div class="wfAjax24"></div>
					<span class="wfSavedMsg">&nbsp;Your changes have been saved!</span></td>
			</tr>
		</table>
	</form>

<?php endif ?>

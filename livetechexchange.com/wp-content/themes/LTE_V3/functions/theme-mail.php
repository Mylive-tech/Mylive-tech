<?php
/**
 * Contact form mail functions.
 *

 */

$to = htmlspecialchars( stripslashes( trim( $_POST['To'] ) ) );
$name = htmlspecialchars( stripslashes( trim( $_POST['Name'] ) ) );
$email = htmlspecialchars( stripslashes( trim( $_POST['Email'] ) ) );
$message = htmlspecialchars( stripslashes( trim( $_POST['Message'] ) ) );
$subject = htmlspecialchars( stripslashes( trim( $_POST['Subject'] ) ) );

$headers = 'From: '. $name .' <'. $email .'>';
$subject .= ', from: ' .$name ;

if( @mail( $to, $subject, $message, $headers ) ){
	echo json_encode( array(
		'status' => 'ok'
	));	  
} else {
	echo json_encode( array(
		'status' => 'error'
	));	  
}

?>
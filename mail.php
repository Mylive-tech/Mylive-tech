<?php
$to = "webmaster@mylive-tech.com";
$subject = "Testing";
$txt = "Hello world!";
$headers = "From: abc@xyz.com" . "\r\n" .
"CC: 123@xyz.com";

mail($to,$subject,$txt,$headers);
?>

<?php
class PHPMailerCustom extends PHPMailer {
    public function getAllRecipients() {
        return $this->all_recipients;
    }

    public function Send() {
        $rec = $this->getAllRecipients();
        if (!empty($rec)) {
            return parent::Send();
        }
        return true;
    }
}

?>
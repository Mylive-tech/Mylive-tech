<?php
// RAC Table row can be deleted, 
//to provide exact report we count it and have it options table
class FPRacCounter{
	    public static function rac_do_recovered_count() {
        if (get_option('rac_recovered_count')) { // count started already
            $recovered_count = get_option('rac_recovered_count');
            $recovered_count++;
            update_option('rac_recovered_count', $recovered_count);
        } else {// first time counting
            update_option('rac_recovered_count', 1);
        }
    }

    public static function rac_do_abandoned_count() {
        if (get_option('rac_abandoned_count')) { // count started already
            $abandoned_count = get_option('rac_abandoned_count');
            $abandoned_count++;
            update_option('rac_abandoned_count', $abandoned_count);
        } else {// first time counting
            update_option('rac_abandoned_count', 1);
        }
    }

    public static function rac_do_mail_count() {
        if (get_option('rac_mail_count')) { // count started already
            $mail_count = get_option('rac_mail_count');
            $mail_count++;
            update_option('rac_mail_count', $mail_count);
        } else {// first time counting
            update_option('rac_mail_count', 1);
        }
    }

    public static function rac_do_linkc_count() {
        if (get_option('rac_link_count')) { // count started already
            $link_count = get_option('rac_link_count');
            $link_count++;
            update_option('rac_link_count', $link_count);
        } else {// first time counting
            update_option('rac_link_count', 1);
        }
    }
}
?>
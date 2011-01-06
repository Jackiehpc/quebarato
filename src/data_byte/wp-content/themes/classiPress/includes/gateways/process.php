<?php
/**
 * Payment processing script to store transaction
 * details into the database
 * @author AppThemes
 * @version 3.0
 * @package ClassiPress
 *
 */

global $wpdb;

if($_POST['txn_id']) {

    // since paypal sends over the date as a string, we need to convert it
    // into a mysql date format. There will be a time difference due to PayPal's
    // US pacific time zone and your server time zone
    $payment_date = strtotime($_POST['payment_date']);
    $payment_date = strftime('%Y-%m-%d %H:%M:%S', $payment_date);
    

    // check and make sure this transaction hasn't already been added
    $sql = "SELECT txn_id "
         . "FROM " . $wpdb->prefix . "cp_order_info "
         . "WHERE txn_id = '".$wpdb->escape(appthemes_clean($_POST['txn_id']))."' LIMIT 1";

    $results = $wpdb->get_row($sql);

    if(!$results) :

        // $_GET['aid'] = '2656';

        $sql = $wpdb->prepare("INSERT INTO " . $wpdb->prefix . "cp_order_info" .
                " (ad_id, first_name, last_name, payer_email, residence_country, transaction_subject, item_name,
                   item_number, payment_type, payer_status, payer_id, receiver_id, parent_txn_id, txn_id, mc_gross, mc_fee, payment_status,
                   pending_reason, txn_type, tax, mc_currency, reason_code, custom, test_ipn, payment_date, create_date
                ) " .
                "VALUES ('" .
                    $wpdb->escape(appthemes_clean($_REQUEST['aid'])) . "','" .
                    $wpdb->escape(appthemes_clean($_POST['first_name'])) . "','" .
                    $wpdb->escape(appthemes_clean($_POST['last_name'])) . "','" .
                    $wpdb->escape(appthemes_clean($_POST['payer_email'])) . "','" .
                    $wpdb->escape(appthemes_clean($_POST['residence_country'])) . "','" .
                    $wpdb->escape(appthemes_clean($_POST['transaction_subject'])) . "','" .
                    $wpdb->escape(appthemes_clean($_POST['item_name'])) . "','" .
                    $wpdb->escape(appthemes_clean($_POST['item_number'])) . "','" .
                    $wpdb->escape(appthemes_clean($_POST['payment_type'])) . "','" .
                    $wpdb->escape(appthemes_clean($_POST['payer_status'])) . "','" .
                    $wpdb->escape(appthemes_clean($_POST['payer_id'])) . "','" .
                    $wpdb->escape(appthemes_clean($_POST['receiver_id'])) . "','" .
                    $wpdb->escape(appthemes_clean($_POST['parent_txn_id'])) . "','" .
                    $wpdb->escape(appthemes_clean($_POST['txn_id'])) . "','" .
                    $wpdb->escape(appthemes_clean($_POST['mc_gross'])) . "','" .
                    $wpdb->escape(appthemes_clean($_POST['mc_fee'])) . "','" .
                    $wpdb->escape(appthemes_clean($_POST['payment_status'])) . "','" .
                    $wpdb->escape(appthemes_clean($_POST['pending_reason'])) . "','" .
                    $wpdb->escape(appthemes_clean($_POST['txn_type'])) . "','" .
                    $wpdb->escape(appthemes_clean($_POST['tax'])) . "','" .
                    $wpdb->escape(appthemes_clean($_POST['mc_currency'])) . "','" .
                    $wpdb->escape(appthemes_clean($_POST['reason_code'])) . "','" .
                    $wpdb->escape(appthemes_clean($_POST['custom'])) . "','" .
                    $wpdb->escape(appthemes_clean($_POST['test_ipn'])) . "','" .
                    $wpdb->escape($payment_date) . "','" .
                    gmdate('Y-m-d H:i:s') .
                "')");

        $results = $wpdb->query($sql);

    // ad transaction already exists so it must be an update via PayPal IPN (refund, etc)
    else:

        $update = "UPDATE " . $wpdb->prefix . "cp_order_info SET" .
                " payment_status = '" . $wpdb->escape(appthemes_clean($_POST['payment_status'])) . "'," .
                " mc_gross = '" . $wpdb->escape(appthemes_clean($_POST['mc_gross'])) . "'," .
                " txn_type = '" . $wpdb->escape(appthemes_clean($_POST['txn_type'])) . "'," .
                " reason_code = '" . $wpdb->escape(appthemes_clean($_POST['reason_code'])) . "'," .
                " mc_currency = '" . $wpdb->escape(appthemes_clean($_POST['mc_currency'])) . "'," .
                " test_ipn = '" . $wpdb->escape(appthemes_clean($_POST['test_ipn'])) . "'," .
                " create_date = '" . $wpdb->escape($payment_date) . "'" .
                " WHERE txn_id ='" . $wpdb->escape($_POST['txn_id']) ."'";

        $results = $wpdb->query($update);


    endif;

}

?>

    



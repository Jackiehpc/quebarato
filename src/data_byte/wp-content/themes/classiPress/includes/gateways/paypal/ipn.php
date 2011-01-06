<?php

/* Process Ad Payment - PayPal IPN
 *
 * @author AppThemes
 * @version 3.0.4
 * 
 *
 */


// this is the paypal ipn listener which waits for the request
function cp_ipn_listener() {

    // validate the paypal request by sending it back to paypal
    function cp_ipn_request_check() {
		
		define('SSL_P_URL', 'https://www.paypal.com/cgi-bin/webscr');
		define('SSL_SAND_URL','https://www.sandbox.paypal.com/cgi-bin/webscr');
			
		$hostname = gethostbyaddr ( $_SERVER ['REMOTE_ADDR'] );
		if (! preg_match ( '/paypal\.com$/', $hostname )) {
			$ipn_status = 'Validation post isn\'t from PayPal';
			if (get_option('cp_paypal_ipn_debug') == 'true')
				wp_mail(get_option('admin_email'), $ipn_status, 'fail');
			return false;
		}
		
		// parse the paypal URL
		$paypal_url = ($_POST['test_ipn'] == 1) ? SSL_SAND_URL : SSL_P_URL;
		$url_parsed = parse_url($paypal_url);


        $post_string = '';    
		foreach ($_POST as $field=>$value) { 
			$post_string .= $field.'='.urlencode(stripslashes($value)).'&'; 
		}
		$post_string.="cmd=_notify-validate"; // append ipn command

        // get the correct paypal url to post request to
        if (get_option('cp_paypal_sandbox') == 'true')
            $fp = fsockopen ( 'ssl://www.sandbox.paypal.com', "443", $err_num, $err_str, 60 );
        else
            $fp = fsockopen ( 'ssl://www.paypal.com', "443", $err_num, $err_str, 60 );
        
        
        $ipn_response = '';
        
        
        if(!$fp) {
			// could not open the connection.  If loggin is on, the error message
			// will be in the log.
			$ipn_status = "fsockopen error no. $err_num: $err_str";
			if (get_option('cp_paypal_ipn_debug') == 'true')
				wp_mail(get_option('admin_email'), $ipn_status, 'fail');
			return false;
		} else { 
			// Post the data back to paypal
			fputs($fp, "POST $url_parsed[path] HTTP/1.1\r\n"); 
			fputs($fp, "Host: $url_parsed[host]\r\n"); 
			fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n"); 
			fputs($fp, "Content-length: ".strlen($post_string)."\r\n"); 
			fputs($fp, "Connection: close\r\n\r\n"); 
			fputs($fp, $post_string . "\r\n\r\n"); 
		
			// loop through the response from the server and append to variable
			while(!feof($fp)) { 
		   	$ipn_response .= fgets($fp, 1024); 
		   } 
		  fclose($fp); // close connection
		}    
        
        // Invalid IPN transaction.  Check the $ipn_status and log for details.
		if (! eregi("VERIFIED",$ipn_response)) {
			$ipn_status = 'IPN Validation Failed';
			if (get_option('cp_paypal_ipn_debug') == 'true')
				wp_mail(get_option('admin_email'), $ipn_status, 'fail');
			return false;
		} else {
			$ipn_status = "IPN VERIFIED";
			if (get_option('cp_paypal_ipn_debug') == 'true')
				wp_mail(get_option('admin_email'), $ipn_status, 'SUCCESS');
			return true;
		}
    }
	
	
		
    // if the test variable is set (sandbox mode), send a debug email with all values
    if (isset($_POST['test_ipn'])) {
        $_REQUEST = stripslashes_deep($_REQUEST);
		if (get_option('cp_paypal_ipn_debug') == 'true')
	        wp_mail(get_option('admin_email'), 'PayPal IPN Debug Email Test IPN', "".print_r($_REQUEST, true));
    }

    // make sure the request came from classipress (pid) or paypal (txn_id refund, update)
    if (isset($_POST['txn_id']) || isset($_REQUEST['invoice'])) {
        $_REQUEST = stripslashes_deep($_REQUEST);

        // if paypal sends a response code back let's handle it
        if (cp_ipn_request_check()) {

            // send debug email to see paypal ipn post vars
            if (get_option('cp_paypal_ipn_debug') == 'true')
                wp_mail(get_option('admin_email'), 'PayPal IPN Debug Email Main', "".print_r($_REQUEST, true));

            // process the ad since paypal gave us a valid response
            do_action('cp_init_ipn_response', $_REQUEST);

        }
		//this exit caused WSOD, unsure of its original purpose but it can be addressed with checkout
		//goes through detailed security testing and security fixes.
        //exit;
    }

}

// initialize the listener if IPN option is turned on
if (get_option('cp_enable_paypal_ipn') == 'yes')
    add_action('init', 'cp_ipn_listener');



function cp_handle_ipn_response($_POST) {
    global $wpdb;

	// make sure the ad unique trans id (stored in invoice var) is included
	if ( !empty($_POST['txn_type']) && !empty($_POST['invoice']) ) {


            // process the ad based on the paypal response
            switch (strtolower($_POST['payment_status'])) :

                // payment was made so we can approve the ad
                case 'completed' :

                    $pid = trim($_POST['invoice']);

                    $sql = $wpdb->prepare("SELECT p.ID, p.post_status
                            FROM $wpdb->posts p, $wpdb->postmeta m
                            WHERE p.ID = m.post_id
                            AND p.post_status <> 'publish'
                            AND m.meta_key = 'cp_sys_ad_conf_id'
                            AND m.meta_value = '$pid'
                            ");

                    $newadid = $wpdb->get_row($sql);


                    // if the ad is found, then publish it
                    if($newadid) {
                        $the_ad = array();
                        $the_ad['ID'] = $newadid->ID;
                        $the_ad['post_status'] = 'publish';
                        $ad_id = wp_update_post($the_ad);

                        // now we need to update the ad expiration date so they get the full length of time
                        // sometimes they didn't pay for the ad right away or they are renewing

                        // first get the ad duration and first see if ad packs are being used
                        // if so, get the length of time in days otherwise use the default
                        // prune period defined on the CP settings page

                        $ad_length = get_post_meta($ad_id, 'cp_sys_ad_duration', true);

                        if(isset($ad_length))
                            $ad_length = $ad_length;
                        else
                            $ad_length = get_option('cp_prun_period');

                        // set the ad listing expiration date
                        $ad_expire_date = date_i18n('m/d/Y H:i:s', strtotime('+' . $ad_length . ' days')); // don't localize the word 'days'

                        //now update the expiration date on the ad
                        update_post_meta($ad_id, 'cp_sys_expire_date', $ad_expire_date);
                    }

                break;

                case 'pending' :

                    // send an email if payment is pending
                    wp_mail(get_option('admin_email'), 'PayPal IPN - payment pending', "".print_r($_POST, true));

                break;

                // payment failed so don't approve the ad
                case 'denied' :
                case 'expired' :
                case 'failed' :
                case 'voided' :

                    // send an email if payment didn't work
                    wp_mail(get_option('admin_email'), 'PayPal IPN - payment failed', "".print_r($_POST, true));

                break;
            endswitch;

            // regardless of what happens, log the transaction
            if (file_exists(TEMPLATEPATH . '/includes/gateways/process.php'))
                include_once (TEMPLATEPATH . '/includes/gateways/process.php');
	}
}


add_action('cp_init_ipn_response', 'cp_handle_ipn_response');

?>
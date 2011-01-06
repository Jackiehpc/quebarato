<?php
/**
 *
 * Emails that get called and sent out for ClassiPress
 * @package ClassiPress
 * @author AppThemes
 * For wp_mail to work, you need the following:
 * settings SMTP and smtp_port need to be set in your php.ini
 * also, either set the sendmail_from setting in php.ini, or pass it as an additional header.
 *
 */


// send new ad notification email to admin
function cp_new_ad_email($post_id) {

    // get the post values
    $the_ad = get_post($post_id);
	$category = appthemes_get_custom_taxonomy($post_id, 'ad_cat', 'name');

    $ad_title = stripslashes($the_ad->post_title);
    $ad_cat = stripslashes($category);
    $ad_author = stripslashes(get_the_author_meta('user_login', $the_ad->post_author));
    $ad_slug = stripslashes($the_ad->guid);
    //$ad_content = appthemes_filter(stripslashes($the_ad->post_content));
    $adminurl = get_option('siteurl').'/wp-admin/post.php?action=edit&post='.$post_id;

    $mailto = get_option('admin_email');
    // $mailto = 'tester@127.0.0.1'; // USED FOR TESTING
    $subject = __('New Ad Submission','appthemes');
    $headers = 'From: '. __('ClassiPress Admin', 'appthemes') .' <'. get_option('admin_email') .'>' . "\r\n";

    // The blogname option is escaped with esc_html on the way into the database in sanitize_option
    // we want to reverse this for the plain text arena of emails.
    $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

    $message  = __('Dear Admin,', 'appthemes') . "\r\n\r\n";
    $message .= sprintf(__('The following ad listing has just been submitted on your %s website.', 'appthemes'), $blogname) . "\r\n\r\n";
    $message .= __('Ad Details', 'appthemes') . "\r\n";
    $message .= __('-----------------') . "\r\n";
    $message .= __('Title: ', 'appthemes') . $ad_title . "\r\n";
    $message .= __('Category: ', 'appthemes') . $ad_cat . "\r\n";
    $message .= __('Author: ', 'appthemes') . $ad_author . "\r\n";
    //$message .= __('Description: ', 'appthemes') . $ad_content . "\r\n";
    $message .= __('-----------------') . "\r\n\r\n";
    $message .= __('Preview Ad: ', 'appthemes') . $ad_slug . "\r\n";
    $message .= sprintf(__('Edit Ad: %s', 'appthemes'), $adminurl) . "\r\n\r\n\r\n";
    $message .= __('Regards,', 'appthemes') . "\r\n\r\n";
    $message .= __('ClassiPress', 'appthemes') . "\r\n\r\n";

    // ok let's send the email
    wp_mail($mailto, $subject, $message, $headers);

}


// send new ad notification email to ad owner
function cp_owner_new_ad_email($post_id) {

    // get the post values
    $the_ad = get_post($post_id);
    $category = appthemes_get_custom_taxonomy($post_id, 'ad_cat', 'name');

    $ad_title = stripslashes($the_ad->post_title);
    $ad_cat = stripslashes($category);
    $ad_author = stripslashes(get_the_author_meta('user_login', $the_ad->post_author));
    $ad_author_email = stripslashes(get_the_author_meta('user_email', $the_ad->post_author));
    $ad_status = stripslashes($the_ad->post_status);
    //$ad_content = appthemes_filter(stripslashes($the_ad->post_content));
    $siteurl = trailingslashit(get_option('home'));

    $dashurl = trailingslashit(CP_DASHBOARD_URL);

    // The blogname option is escaped with esc_html on the way into the database in sanitize_option
    // we want to reverse this for the plain text arena of emails.
    $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

    $mailto = $ad_author_email;
    //$mailto = 'tester@127.0.0.1'; // USED FOR TESTING

    $subject = sprintf(__('Your Ad Submission on %s','appthemes'), $blogname);
    $headers = 'From: '. sprintf(__('%s Admin', 'appthemes'), $blogname) .' <'. get_option('admin_email') .'>' . "\r\n";

    $message  = sprintf(__('Hi %s,', 'appthemes'), $ad_author) . "\r\n\r\n";
    $message .= sprintf(__('Thank you for your recent submission! Your ad listing has been submitted for review and will not appear live on our site until it has been approved. Below you will find a summary of your ad listing on the %s website.', 'appthemes'), $blogname) . "\r\n\r\n";
    $message .= __('Ad Details', 'appthemes') . "\r\n";
    $message .= __('-----------------') . "\r\n";
    $message .= __('Title: ', 'appthemes') . $ad_title . "\r\n";
    $message .= __('Category: ', 'appthemes') . $ad_cat . "\r\n";
    $message .= __('Status: ', 'appthemes') . $ad_status . "\r\n";
    //$message .= __('Description: ', 'appthemes') . $ad_content . "\r\n";
    $message .= __('-----------------') . "\r\n\r\n";
    $message .= __('You may check the status of your ad(s) at anytime by logging into your dashboard.', 'appthemes') . "\r\n";
    $message .= $dashurl . "\r\n\r\n\r\n\r\n";
    $message .= __('Regards,', 'appthemes') . "\r\n\r\n";
    $message .= sprintf(__('Your %s Team', 'appthemes'), $blogname) . "\r\n";
    $message .= $siteurl . "\r\n\r\n\r\n\r\n";

    // ok let's send the email
    wp_mail($mailto, $subject, $message, $headers);

}


// when an ad is approved or expires, send the ad owner an email
function cp_notify_ad_owner_email($new_status, $old_status, $post) {
    global $current_user, $wpdb;

    //$contributor = get_userdata($post->post_author);

    $the_ad = get_post($post->ID);
    $category = appthemes_get_custom_taxonomy($post_id, 'ad_cat', 'name');

    $ad_title = stripslashes($the_ad->post_title);
    $ad_cat = stripslashes($category);
    $ad_author_id = stripslashes(get_the_author_meta('ID', $the_ad->post_author));
    $ad_author = stripslashes(get_the_author_meta('user_login', $the_ad->post_author));
    $ad_author_email = stripslashes(get_the_author_meta('user_email', $the_ad->post_author));
    $ad_status = stripslashes($the_ad->post_status);
    $ad_content = appthemes_filter(stripslashes($the_ad->post_content));
    $siteurl = trailingslashit(get_option('home'));
    $dashurl = trailingslashit(CP_DASHBOARD_URL);


    // check to see if ad is legacy or not
    if(get_post_meta($post->ID, 'email', true))
        $mailto = get_post_meta($post->ID, 'email', true);
    else
        $mailto = $ad_author_email;
    
    //$mailto = 'tester@127.0.0.1'; // USED FOR TESTING

    // The blogname option is escaped with esc_html on the way into the database in sanitize_option
    // we want to reverse this for the plain text arena of emails.
    $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

    // make sure the admin wants to send emails
    $send_approved_email = get_option('cp_new_ad_email_owner');
    $send_expired_email = get_option('cp_expired_ad_email_owner');

    // if the ad has been approved send email to ad owner only if owner is not equal to approver
    // admin approving own ads or ad owner pausing and reactivating ad on his dashboard don't need to send email
    if ($old_status == 'pending' && $new_status == 'publish' && $current_user->ID != $ad_author_id && $send_approved_email == 'yes') {

        $subject = __('Your Ad Has Been Approved','appthemes');
        $headers = 'From: '. sprintf(__('%s Admin', 'appthemes'), $blogname) .' <'. get_option('admin_email') .'>' . "\r\n";

        $message  = sprintf(__('Hi %s,', 'appthemes'), $ad_author) . "\r\n\r\n";
        $message .= sprintf(__('Your ad listing, "%s" has been approved and is now live on our site.', 'appthemes'), $ad_title) . "\r\n\r\n";

        $message .= __('You can view your ad by clicking on the following link:', 'appthemes') . "\r\n";
        $message .= get_permalink($post->ID) . "\r\n\r\n\r\n\r\n";
        $message .= __('Regards,', 'appthemes') . "\r\n\r\n";
        $message .= sprintf(__('Your %s Team', 'appthemes'), $blogname) . "\r\n";
        $message .= $siteurl . "\r\n\r\n\r\n\r\n";

        // ok let's send the email
        wp_mail($mailto, $subject, $message, $headers);


    // if the ad has expired, send an email to the ad owner only if owner is not equal to approver
    } elseif ($old_status == 'publish' && $new_status == 'draft' && $current_user->ID != $ad_author_id && $send_expired_email == 'yes') {

        $subject = __('Your Ad Has Expired','appthemes');
        $headers = 'From: '. sprintf(__('%s Admin', 'appthemes'), $blogname) .' <'. get_option('admin_email') .'>' . "\r\n";

        $message  = sprintf(__('Hi %s,', 'appthemes'), $ad_author) . "\r\n\r\n";
        $message .= sprintf(__('Your ad listing, "%s" has expired.', 'appthemes'), $ad_title) . "\r\n\r\n";

        if (get_option('cp_allow_relist') == 'yes') {
            $message .= __('If you would like to relist your ad, please visit your dashboard and click the "relist" link.', 'appthemes') . "\r\n";
            $message .= $dashurl . "\r\n\r\n\r\n\r\n";
        }

        $message .= __('Regards,', 'appthemes') . "\r\n\r\n";
        $message .= sprintf(__('Your %s Team', 'appthemes'), $blogname) . "\r\n";
        $message .= $siteurl . "\r\n\r\n\r\n\r\n";

        // ok let's send the email
        wp_mail($mailto, $subject, $message, $headers);

    }
}

add_filter('transition_post_status', 'cp_notify_ad_owner_email', 10, 3);


// ad poster sidebar contact form email
function cp_contact_ad_owner_email($postID) {

    // wp_mail doesn't seem to work with cc or bcc in headers (as of 2.9.2)
    // this is here for adding it later
    // $Cc = 'youremailaddress@domain.com';
    // $Bcc = get_option('admin_email');

    // check to see if ad is legacy or not
    if(get_post_meta($postID, 'email', true))
        $mailto = get_post_meta($postID, 'email', true);
    else
        $mailto = get_the_author_meta('user_email');

    $from_name = strip_tags($_POST['from_name']);
    $from_email = strip_tags($_POST['from_email']);
    //$mailto = 'tester@127.0.0.1'; // USED FOR TESTING
    $subject = strip_tags($_POST['subject']);
    $headers = "From: $from_name <$from_email> \r\n";
    $headers .= "Reply-To: $from_name <$from_email> \r\n";
    // $headers .= "Cc: $Cc \r\n";
    // $headers .= "BCC: $Bcc \r\n";

    // The blogname option is escaped with esc_html on the way into the database in sanitize_option
    // we want to reverse this for the plain text arena of emails
    $sitename = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
    $siteurl = trailingslashit(get_option('home'));
    $permalink = get_permalink();

    $message  = sprintf(__('Someone is interested in your ad listing: %s', 'appthemes'), $permalink) . "\r\n\r\n";
    // $message  = sprintf(__('From: %s - %s', 'appthemes'), $from_name, $from_email) . "\r\n\r\n";
	$fixPostMessage =  stripslashes($_POST['message']);
    $message  .= wordwrap(strip_tags($fixPostMessage), 70) . "\r\n\r\n\r\n\r\n";
    $message  .= '-----------------------------------------' . "\r\n";
    $message .= sprintf(__('This message was sent from %s', 'appthemes'), $sitename) . "\r\n";
    $message .=  $siteurl . "\r\n\r\n";
	$message .= "Sent by IP Address: ".appthemes_get_ip()."\r\n\r\n"; 

    // ok let's send the email
    wp_mail($mailto, $subject, $message, $headers);

}



// overwrite the default generic WordPress from name and email address
if(get_option('cp_custom_email_header') == 'yes') {

    if (!class_exists('wp_mail_from')) :
        class wp_mail_from {

            function wp_mail_from() {
                add_filter('wp_mail_from', array(&$this, 'cp_mail_from'));
                add_filter('wp_mail_from_name', array(&$this, 'cp_mail_from_name'));
            }

            // new from name
            function cp_mail_from_name() {
                $name = get_option('blogname');
                $name = esc_attr($name);
                return $name;
            }

            // new email address
            function cp_mail_from() {
                $email = get_option('admin_email');
                $email = is_email($email);
                return $email;
            }

        }

        $wp_mail_from = new wp_mail_from();

    endif;

}


// email that gets sent out to new users once they register
function app_new_user_notification($user_id, $plaintext_pass = '') {
	global $app_abbr;
	
	$user = new WP_User($user_id);

	$user_login = stripslashes($user->user_login);
	$user_email = stripslashes($user->user_email);
	//$user_email = 'tester@127.0.0.1'; // USED FOR TESTING
	
	// variables that can be used by admin to dynamically fill in email content
	$find = array('/%username%/i', '/%password%/i', '/%blogname%/i', '/%siteurl%/i', '/%loginurl%/i', '/%useremail%/i');
	$replace = array($user_login, $plaintext_pass, get_option('blogname'), get_option('siteurl'), get_option('siteurl').'/wp-login.php', $user_email);

	// The blogname option is escaped with esc_html on the way into the database in sanitize_option
	// we want to reverse this for the plain text arena of emails.
	$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

	// send the site admin an email everytime a new user registers
	if (get_option($app_abbr.'_nu_admin_email') == 'yes') {	
		$message  = sprintf(__('New user registration on your site %s:'), $blogname) . "\r\n\r\n";
		$message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
		$message .= sprintf(__('E-mail: %s'), $user_email) . "\r\n";

		@wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration'), $blogname), $message);
	}

	if ( empty($plaintext_pass) )
		return;
	
	// check and see if the custom email option has been enabled
	// if so, send out the custom email instead of the default WP one
	if (get_option($app_abbr.'_nu_custom_email') == 'yes') {	
	
		// email sent to new user starts here				
		$from_name = strip_tags(get_option($app_abbr.'_nu_from_name'));
		$from_email = strip_tags(get_option($app_abbr.'_nu_from_email'));
				
		// search and replace any user added variable fields in the subject line
		$subject = stripslashes(get_option($app_abbr.'_nu_email_subject'));
		$subject = preg_replace($find, $replace, $subject);
		$subject = preg_replace("/%.*%/", "", $subject);	

		// search and replace any user added variable fields in the body
		$message = stripslashes(get_option($app_abbr.'_nu_email_body'));
		$message = preg_replace($find, $replace, $message);
		$message = preg_replace("/%.*%/", "", $message);
		
		// assemble the header
		$headers = "From: $from_name <$from_email> \r\n";
		$headers .= "Reply-To: $from_name <$from_email> \r\n";	
		$headers .= "Content-Type: ". get_option($app_abbr.'_nu_email_type') ." charset=\"" . get_option('blog_charset') . "\"\n";	

		
		// ok let's send the new user an email
		wp_mail($user_email, $subject, $message, $headers);
	
	// send the default email to debug
	} else {
	
		$message  = sprintf(__('Username: %s', 'appthemes'), $user_login) . "\r\n";
		$message .= sprintf(__('Password: %s', 'appthemes'), $plaintext_pass) . "\r\n";
		$message .= wp_login_url() . "\r\n";

		wp_mail($user_email, sprintf(__('[%s] Your username and password', 'appthemes'), $blogname), $message);
	
	}
	
}

// send new ad notification email to admin
function app_report_post($post_id) {

    // get the post values
    $the_ad = get_post($post_id);
    $category = appthemes_get_custom_taxonomy($post_id, 'ad_cat', 'name');

    $ad_title = stripslashes($the_ad->post_title);
    $ad_cat = stripslashes($category);
    $ad_author = stripslashes(get_the_author_meta('user_login', $the_ad->post_author));
    $ad_slug = stripslashes($the_ad->guid);
    //$ad_content = appthemes_filter(stripslashes($the_ad->post_content));
    $adminurl = get_option('siteurl').'/wp-admin/post.php?action=edit&post='.$post_id;

    $mailto = get_option('admin_email');
    //$mailto = 'tester@127.0.0.1'; // USED FOR TESTING
    $subject = __('Post Reported','appthemes');
    $headers = 'From: '. __('ClassiPress Admin', 'appthemes') .' <'. get_option('admin_email') .'>' . "\r\n";

    // The blogname option is escaped with esc_html on the way into the database in sanitize_option
    // we want to reverse this for the plain text arena of emails.
    $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

    $message  = __('Dear Admin,', 'appthemes') . "\r\n\r\n";
    $message .= sprintf(__('The following ad listing has just been reported on your %s website.', 'appthemes'), $blogname) . "\r\n\r\n";
    $message .= __('Ad Details', 'appthemes') . "\r\n";
    $message .= __('-----------------') . "\r\n";
    $message .= __('Title: ', 'appthemes') . $ad_title . "\r\n";
    $message .= __('Category: ', 'appthemes') . $ad_cat . "\r\n";
    $message .= __('Author: ', 'appthemes') . $ad_author . "\r\n";
    //$message .= __('Description: ', 'appthemes') . $ad_content . "\r\n";
    $message .= __('-----------------') . "\r\n\r\n";
    $message .= __('Preview Ad: ', 'appthemes') . $ad_slug . "\r\n";
    $message .= sprintf(__('Edit Ad: %s', 'appthemes'), $adminurl) . "\r\n\r\n\r\n";
	
    $message .= __('Regards,', 'appthemes') . "\r\n\r\n";
    $message .= __('ClassiPress', 'appthemes') . "\r\n\r\n";

    // ok let's send the email
    wp_mail($mailto, $subject, $message, $headers);

}




?>
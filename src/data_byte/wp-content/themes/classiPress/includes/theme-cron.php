<?php
/**
 *
 * Cron jobs that are executed on a timer ClassiPress
 * @package ClassiPress
 * @author AppThemes
 * @version 3.0.4
 * 
 */


//need to replace this with a check if the cron hook already exists, instead of using random variable
if (get_option('cp_ad_expired_check') != 'yes' && get_option('cp_ad_expired_check_recurrance') != 'none')
    cp_schedule_expire_check();

// If the user selects "None" from the Classipress Options page, remove all scheduled crons and set the scheduler check to NO (not set).
// To minimize use of this function, added a check to only run the function if the scheduler check is set to yes.
if (get_option('cp_ad_expired_check_recurrance') == 'none' && get_option('cp_ad_expired_check') == 'yes') {
    wp_clear_scheduled_hook('cp_ad_expired_check');
    update_option('cp_ad_expired_check', 'no');
}

//schedule the cron jobs
function cp_schedule_expire_check() {
    wp_clear_scheduled_hook('cp_ad_expired_check');

    if(!get_option('cp_ad_expired_check_recurrance')) $recurranceValue = 'daily';
    else $recurranceValue = get_option('cp_ad_expired_check_recurrance');
    wp_schedule_event(strtotime('today + 1 hour'), $recurranceValue, 'cp_ad_expired_check');
    add_option('cp_ad_expired_check', 'yes');
}

//cron jobs execute the following function everytime they run
function cp_check_expired_cron() {
    if( get_option('cp_post_prune') == 'yes' ) {
        global $wpdb;

        //keep in mind the email takes the tabbed formatting and uses it in the email, so please keep formatting of query below.
        $qryToString = "SELECT $wpdb->posts.ID FROM $wpdb->posts
        LEFT JOIN $wpdb->postmeta ON $wpdb->posts.ID = $wpdb->postmeta.post_id
        WHERE $wpdb->postmeta.meta_key = 'cp_sys_expire_date'
        AND timediff(STR_TO_DATE($wpdb->postmeta.meta_value, '%m/%d/%Y %H:%i:%s'), now()) <= 0
        AND $wpdb->posts.post_status = 'publish' AND $wpdb->posts.post_type = 'ad_listing'";

        $postids = $wpdb->get_col($qryToString);
        $messageDetails = '';

        //create msgDetails variable that has a set of links to expired ads that are found.
        if ($postids) foreach ($postids as $id) {
            $update_ad = array();
            $update_ad['ID'] = $id;
            $update_ad['post_status'] = 'draft';
            wp_update_post($update_ad);
            $messageDetails .= get_bloginfo ( 'url' ) . "/?p=" . $id . '' . "\r\n";
        }
        //get rid of the trailing comma
        $messageDetails = trim($messageDetails, ',');

        if($messageDetails == '')
            $messageDetails = __('No expired ads were found.', 'appthemes');
        else
            $messageDetails = __('The following ads expired and have been taken down from your website: ', 'appthemes') . "\r\n" . $messageDetails;
            $message = __('Your cron job has run successfully. ', 'appthemes') . "\r\n" . $messageDetails;
            //only enable this code to debug the SQL statement.
            //$message .= "\r\n\n\n" . "Output of the SQL select call for expired ads: "
            //. "\r\n" . $qryToString;
    } else {
        $message = __('Your cron job has run successfully. However, the pruning ads option is turned off so no expired ads were taken down from the website.', 'appthemes'); }

        if(get_option('cp_prune_ads_email') == 'yes')
            wp_mail(get_option('admin_email'), __('ClassiPress Ads Expired', 'appthemes'), $message . "\r\n\n" . __('Regards', 'appthemes') . ", \r\n" . __('ClassiPress'));
}

add_action('cp_ad_expired_check', 'cp_check_expired_cron');

?>
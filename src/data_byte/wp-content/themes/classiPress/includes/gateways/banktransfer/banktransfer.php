<?php

/**
 * Bank transfer payment gateway script
 *
 * @package ClassiPress
 * @author AppThemes
 * @version 3.0.4
 *
 * @param int $post_id
 * @param text $type 
 *
 */

// payment processing script that is used on the new ad confirmation page
// and also the ad dashboard so ad owners can pay for unpaid ads
function cp_banktransfer($post_id, $type) {
    global $wpdb;
?>

<h2><?php _e('Your Unique Ad Details', 'appthemes') ?></h2>

<p><?php _e('Please include the following transaction and ad numbers when sending the bank transfer. Once your transfer has been verified, we will then approve your ad.', 'appthemes') ?></p>

<p>
    <strong><?php _e('Transaction #:', 'appthemes') ?></strong> <?php echo get_post_meta($post_id, 'cp_sys_ad_conf_id', true); ?><br />
    <strong><?php _e('Ad Listing #:', 'appthemes') ?></strong> <?php echo $post_id ?><br />
    <strong><?php _e('Total Amount:', 'appthemes') ?></strong> <?php echo get_post_meta($post_id, 'cp_sys_total_ad_cost', true); ?> (<?php echo get_option('cp_curr_pay_type'); ?>)<br />

</p>

<br /><br />

<h2><?php _e('Bank Transfer Instructions', 'appthemes') ?></h2>

<p><?php echo stripslashes(appthemes_nl2br(get_option('cp_bank_instructions'))); ?></p>

<p><?php _e('For questions or problems, please contact us directly at', 'appthemes') ?> <?php echo get_option('admin_email'); ?></p>


<?php
}
?>



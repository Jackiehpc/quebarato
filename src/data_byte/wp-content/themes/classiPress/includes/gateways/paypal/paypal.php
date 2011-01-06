<?php

/**
 * PayPal payment gateway script
 *
 * @package ClassiPress
 * @author David Cowgill
 * @version 3.0
 *
 * @param int $post_id
 * @param text $type 
 *
 */

// payment processing script that is used on the new ad confirmation page
// and also the ad dashboard so ad owners can pay for unpaid ads
function cp_dashboard_paypal_button($post_id, $type) {
    global $wpdb;

    // figure out the number of days this ad was listed for
    if (get_post_meta($post_id, 'cp_sys_ad_duration', true)) $prun_period = get_post_meta($post_id, 'cp_sys_ad_duration', true); else $prun_period = get_option('cp_prun_period');
?>

   <form name="paymentform" action="<?php if (get_option('cp_paypal_sandbox') == 'true') echo 'https://www.sandbox.paypal.com/cgi-bin/webscr'; else echo 'https://www.paypal.com/cgi-bin/webscr'; ?>" method="post">
        
       <input type="hidden" name="cmd" value="_xclick" />
       <input type="hidden" name="business" value="<?php echo get_option('cp_paypal_email'); ?>" />
       <input type="hidden" name="item_name" value="<?php printf( __('Classified ad listing on %s for %s days', 'appthemes'), get_bloginfo('name'), $prun_period) ?>" />
       <input type="hidden" name="item_number" value="<?php echo get_post_meta($post_id, 'cp_sys_ad_conf_id', true); ?>" />
       <input type="hidden" name="amount" value="<?php echo get_post_meta($post_id, 'cp_sys_total_ad_cost', true); ?>" />
       <input type="hidden" name="no_shipping" value="1" />
       <input type="hidden" name="no_note" value="1" />

       <?php if(get_option('cp_enable_paypal_ipn') == 'yes') { ?>
           <input type="hidden" name="notify_url" value="<?php echo bloginfo('url'); ?>/index.php?invoice=<?php echo get_post_meta($post_id, 'cp_sys_ad_conf_id', true); ?>&amp;aid=<?php echo $post_id ?>" />
           
           <?php if (get_option('cp_paypal_sandbox') == 'true'): ?>
               <input type="hidden" name="test_ipn" value="1" />
           <?php endif; ?>
           
	   <?php } ?>
	   
       <input type="hidden" name="cancel_return" value="<?php echo get_option('home'); ?>" />
       <input type="hidden" name="return" value="<?php echo CP_ADD_NEW_CONFIRM_URL ?>?pid=<?php echo get_post_meta($post_id, 'cp_sys_ad_conf_id', true); ?>&amp;aid=<?php echo $post_id ?>" />
       <input type="hidden" name="rm" value="2" />
       <input type="hidden" name="cbt" value="<?php _e('Click here to publish your ad on','appthemes') ?> <?php bloginfo('name'); ?>" />
       <input type="hidden" name="currency_code" value="<?php echo get_option('cp_curr_pay_type'); ?>" />

       <?php if(get_option('cp_paypal_logo_url')) { ?>
           <input type="hidden" name="cpp_header_image" value="<?php echo get_option('cp_paypal_logo_url'); ?>" />
       <?php } ?>

       <?php if($type == 'dashboard') { ?>
           <input type="image" src="<?php bloginfo('template_directory'); ?>/images/paypal.png" name="submit" />
       <?php } else { ?>
           <center><input type="submit" class="btn_orange" value="<?php _e('Continue','appthemes');?> &rsaquo;&rsaquo;" /></center>
           <script type="text/javascript"> setTimeout("document.paymentform.submit();",500); </script>
       <?php } ?>

   </form>

<?php
}
?>
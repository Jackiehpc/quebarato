<?php
/**
 * This is step 2 of 3 for the ad submission form
 * 
 * @package ClassiPress
 * @subpackage New Ad
 * @author AppThemes
 * @copyright 2010
 *
 * here we are processing the images and gathering all the post values.
 * using sessions would be the optimal way but WP doesn't play nice so instead
 * we take all the form post values and put them into an associative array
 * and then store it in the wp_options table as a serialized array. essentially
 * we are using the wp_options table as our session holder and can access
 * the keys and values later and process the ad in step 3
 *
 */

global $userdata;
global $wpdb;

// check to see if there are images included
// then valid the image extensions
if (!empty($_FILES['image']))
    $error_msg = cp_validate_image();

// images are valid
if(!$error_msg) {

    // create the array that will hold all the post values
    $postvals = array();

    // upload the images and put into the new ad array
    if (!empty($_FILES['image']))
        $postvals = cp_process_new_image();

    // keep only numeric, commas or decimal values
    if (!empty($_POST['cp_price']))
        $postvals['cp_price'] = cp_clean_price($_POST['cp_price']);
    
    // keep only values and insert/strip commas if needed
    if (!empty($_POST['tags_input']))
        $postvals['tags_input'] = cp_clean_tags($_POST['tags_input']);

    // put all the posted form values into session vars
    foreach($_POST as $key => $value)
        $postvals[$key] = cp_clean($value);

    // store the user IP address, ID for later
    $postvals['cp_sys_userIP'] = cp_getIP();
    $postvals['user_id'] = $current_user->ID;

    // see if the featured ad checkbox has been checked
    if (isset($_POST['featured_ad'])) {
        $postvals['featured_ad'] = $_POST['featured_ad'];
        // get the featured ad price into the array
        $postvals['cp_sys_feat_price'] = get_option('cp_sys_feat_price');
    }

    // calculate the ad listing fee and put into a variable
    if(get_option('cp_charge_ads') == 'yes')
        $postvals['cp_sys_ad_listing_fee'] = cp_ad_listing_fee($_POST['cat'], $_POST['ad_pack_id'], $_POST['cp_price']);

    // check to prevent "Notice: Undefined index:" on php strict error checking. get ad pack id and lookup length
    $adpackid = '';
    if(isset($_POST['ad_pack_id'])) {
        $adpackid = $_POST['ad_pack_id'];
        $postvals['pack_duration'] = cp_get_ad_pack_length($adpackid);
    }

    // calculate the total cost of the ad
	if(isset($postvals['cp_sys_feat_price']))
    	$postvals['cp_sys_total_ad_cost'] = cp_calc_ad_cost($_POST['cat'], $adpackid, $postvals['cp_sys_feat_price'], $_POST['cp_price']);
	else $postvals['cp_sys_total_ad_cost'] = cp_calc_ad_cost($_POST['cat'], $adpackid, 0, $_POST['cp_price']);
    
    // Debugging section
    //echo '$_POST ATTACHMENT<br/>';
    //print_r($postvals['attachment']);

    //echo '$_POST PRINT<br/>';
    //print_r($_POST);

    //echo '<br/><br/>$postvals PRINT<br/>';
    //print_r($postvals);

    // now put the array containing all the post values into the database
    // instead of passing hidden values which are easy to hack and so we
    // can also retrieve it on the next step
    $option_name = 'cp_'.$postvals['oid'];
    update_option($option_name, $postvals);

    ?>

    <div id="step2"></div>

      <h2 class="dotted"><?php _e('Review Your Listing','cp');?></h2>

            <img src="<?php bloginfo('template_url'); ?>/images/step2.gif" alt="" class="stepimg" />


            <form name="mainform" id="mainform" class="form_step" action="" method="post" enctype="multipart/form-data">

                <ol>

                    <?php
                    // pass in the form post array and show the ad summary based on the formid
                    echo cp_show_review($postvals);

                    // debugging info
                    //echo get_option('cp_price_scheme') .'<-- pricing scheme<br/>';
                    //echo $postvals['cat'] .'<-- catid<br/>';
                    //echo get_option('cp_cat_price_'.$postvals['cat']) .'<-- cat price<br/>';
                    //echo $postvals['user_id'] .'<-- userid<br/>';
                    //echo get_option('cp_price_per_ad') .'<-- listing cost<br/>';
                    //echo get_option('cp_curr_symbol_pos') .'<-- currency position<br/>'; 
                    ?>
                <li>
                <?php if($postvals['cp_sys_total_ad_cost'] > 0) : ?>
                <label><?php _e('Payment Method','cp'); ?>:</label>
                <select name="cp_payment_method" class="dropdownlist required">
                    <?php if(get_option('cp_enable_paypal') == 'yes') { ?><option value="paypal"><?php echo _e('PayPal', 'cp') ?></option><?php } ?>
                    <?php if(get_option('cp_enable_bank') == 'yes') { ?><option value="banktransfer"><?php echo _e('Bank Transfer', 'cp') ?></option><?php } ?>
                    <?php if(get_option('cp_enable_gcheckout') == 'yes') { ?><option value="gcheckout"><?php echo _e('Google Checkout', 'cp') ?></option><?php } ?>
                    <?php if(get_option('cp_enable_2checkout') == 'yes') { ?><option value="2checkout"><?php echo _e('2Checkout', 'cp') ?></option><?php } ?>
                    <?php if(get_option('cp_enable_authorize') == 'yes') { ?><option value="authorize"><?php echo _e('Authorize.net', 'cp') ?></option><?php } ?>
                    <?php if(get_option('cp_enable_chronopay') == 'yes') { ?><option value="chronopay"><?php echo _e('Chronopay', 'cp') ?></option><?php } ?>
                    <?php if(get_option('cp_enable_mbookers') == 'yes') { ?><option value="mbookers"><?php echo _e('MoneyBookers', 'cp') ?></option><?php } ?>
                </select>
                <?php endif; ?>
                <div class="clr"></div>
                </li>
                </ol>
                <div class="pad10"></div>


		<div class="license">

                    <?php echo get_option('cp_ads_tou_msg'); ?>

		</div>

                <div class="clr"></div>


                <p class="light"><?php _e('By clicking the proceed button below, you agree to our terms and conditions.','cp'); ?>
                <br/>
                <?php _e('Your IP address has been logged for security purposes:','cp'); ?> <?php echo $postvals['cp_sys_userIP']; ?></p>



                <p class="btn2">
                    <input type="button" name="goback" class="btn_orange" value="<?php _e('Go back','cp') ?>" onclick="history.back()" />
                    <input type="submit" name="step2" id="step2" class="btn_orange" value="<?php _e('Proceed ','cp'); ?> &rsaquo;&rsaquo;" />
                </p>

                    <input type="hidden" id="oid" name="oid" value="<?php echo $postvals['oid']; ?>" />

	    </form>


		<div class="clear"></div>

<?php

} else {

?>

    <h2 class="dotted"><?php _e('An Error Has Occurred','cp') ?></h2>
    
    <div class="thankyou">
        <p><?php echo cp_error_msg($error_msg); ?></p>
        <input type="button" name="goback" class="btn_orange" value="&lsaquo;&lsaquo; <?php _e('Go Back','cp') ?>" onclick="history.back()" />
    </div>
  

<?php
}
?>




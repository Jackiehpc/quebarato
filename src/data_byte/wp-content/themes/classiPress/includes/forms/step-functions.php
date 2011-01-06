<?php

/**
 * This creates all the fields and assembles them
 * on the ad form page based on either custom forms
 * built by the admin or it just defaults to a
 * standard form which has been pre-defined.
 *
 * @global <type> $wpdb
 * @param <type> $results
 *
 * All custom options we want stored in WP and displayed on the ad detail page need to begin with cp_
 * All custom system options we want stored in WP and NOT displayed on the ad detail page need to begin with cp_sys_
 *
 */


// loops through the custom fields and builds the custom ad form
if (!function_exists('cp_formbuilder')) {
    function cp_formbuilder($results) {
        global $wpdb;

        foreach ($results as $result) {
        ?>

            <li>
                <label><?php if($result->field_tooltip) : ?><a href="#" tip="<?php echo $result->field_tooltip; ?>" tabindex="999"><div class="helpico"></div></a><?php endif; ?><?php echo $result->field_label; ?>: <?php if($result->field_req) echo '<span class="colour">*</span>' ?></label>
            <?php

            switch($result->field_type) {

            case 'text box':
            ?>

                <input name="<?php echo $result->field_name; ?>" id="<?php echo $result->field_name; ?>" type="text" minlength="2" value="<?php if(isset($_POST[$result->field_name])) echo $_POST[$result->field_name]; ?>" class="text <?php if($result->field_req) echo 'required' ?>" />
                <div class="clr"></div>

            <?php
            break;

            case 'drop-down':
            ?>

                <select name="<?php echo $result->field_name; ?>" id="<?php echo $result->field_name; ?>" class="dropdownlist <?php if($result->field_req) echo 'required' ?>">
                    <option value="">-- <?php _e('Select', 'appthemes') ?> --</option>
                    <?php
                    $options = explode(',', $result->field_values);

                    foreach ($options as $option) {
                    ?>

                        <option value="<?php echo $option; ?>"><?php echo $option; ?></option>

                    <?php
                    }
                    ?>
                </select>
                <div class="clr"></div>

            <?php
            break;

            case 'text area':

            ?>

                <textarea rows="8" cols="40" name="<?php echo $result->field_name; ?>" id="<?php echo $result->field_name;?>" class="<?php if($result->field_req) echo 'required' ?>"><?php if(isset($_POST[$result->field_name])) echo $_POST[$result->field_name]; ?></textarea>
                <div class="clr"></div>

                <?php if (get_option('cp_allow_html') == 'yes') : ?>
                    <script type="text/javascript"> <!--
                    tinyMCE.execCommand('mceAddControl', false, '<?php echo $result->field_name;?>');
                    --></script>
                <?php endif; ?>

            <?php
            break;

            case 'radio':

                $options = explode(',', $result->field_values);
                ?>

                <div class="radio-wrap">

                <?php if(!$result->field_req): ?>
                    <div class="radio-button">
                        <input type="radio" name="<?php echo $result->field_name; ?>" id="<?php echo $result->field_name; ?>" class="radiolist"  checked="checked">
                        <?php _e('None', 'appthemes'); ?>
                     </div> <!-- #radio-button -->

                <?php
                endif;

                $firstRadio = true;
                foreach ($options as $option) {
                ?>
                    <div class="radio-button">
                        <input type="radio" name="<?php echo $result->field_name; ?>" id="<?php echo $result->field_name; ?>" value="<?php echo $option; ?>"
        class="radiolist" <?php if($result->field_req && $firstRadio) { echo 'checked="checked"'; $firstRadio = false; } ?>>
                        <?php echo $option; ?>
                    </div> <!-- #radio-button -->

                <?php
                }
                ?>

                </div> <!-- #radio-wrap -->

                <div class="clr"></div>

            <?php
            break;

            case 'checkbox':

                $options = explode(',', $result->field_values);
                $optionCursor = 1;
                ?>

                <div class="checkbox-wrap">

                    <?php
                    foreach ($options as $option) {
                    ?>

                        <div class="checkbox">
                            <input type="checkbox" name="<?php echo $result->field_name; ?>" id="<?php echo $result->field_name; echo '_'.$optionCursor++; ?>"
    value="<?php echo $option; ?>" class="checkboxlist" onclick="addRemoveCheckboxValues(this, '<?php echo $result->field_name; ?>_value')">
                            <?php echo $option; ?>
                        </div> <!-- #checkbox -->

                    <?php
                    }
                    ?>

                    <input type="checkbox" name="<?php echo $result->field_name; ?>" id="<?php echo $result->field_name; ?>_value" value="" checked="checked" style="display:none;" />

                </div> <!-- #checkbox-wrap -->

                <div class="clr"></div>

            <?php
            break;

            }
            ?>

            </li>

        <?php
        }

    }
}



// loops through the custom fields and builds the step2 review page
function cp_formbuilder_review($results) {
    global $wpdb;
    ?>

        <li><label><strong><?php _e('Category','appthemes');?>:</strong></label>
        <div id="review"><?php echo $_POST['catname']; ?></div>
            <div class="clr"></div>
        </li>

    <?php
    foreach ($results as $result) {
    ?>

        <li>
            <label><strong><?php echo $result->field_label; ?>:</strong></label>
            <div id="review"><?php echo stripslashes(nl2br($_POST[$result->field_name])); ?></div>
            <div class="clr"></div>
        </li>

    <?php
    }

}

// calculates total number of image input upload boxes on create ad page
function cp_image_input_fields() {

    for($i=0; $i < get_option('cp_num_images');$i++) {
    ?>
        <li>
            <label><?php _e('Image','appthemes') ?>  <?php echo $i+1 ?>:</label>
            <input type="file" id="upload<?php echo $i+1; ?>" name="image[]" value="<?php if (isset($_POST['image'.$i.''])) echo $_POST['image'.$i.''] ?>" class="fileupload" onchange="enableNextImage(this, <?php echo $i+2; ?>);" <?php if($i>0) echo 'disabled="disabled"'; ?> >
            <div class="clr"></div>
        </li>
    <?php
    }
    ?>

    <p class="light"><?php echo get_option('cp_max_image_size') ?><?php _e('KB max file size per image','appthemes') ?></p>
    <div class="clr"></div>

<?php
}



// show the non-custom fields below the main form
function cp_other_fields() {
    global $wpdb;

    // are images on ads allowed
    if(get_option('cp_ad_images') == 'yes')
        echo cp_image_input_fields();

    // show the featured ad box if enabled
    if(get_option('cp_sys_feat_price')) {
    ?>

        <li class="withborder">
            <label><?php _e('Featured Listing','appthemes'); ?> <?php echo cp_pos_price(get_option('cp_sys_feat_price')); ?></label>
            <div class="clr"></div>
            <input name="featured_ad" value="1" type="checkbox" <?php if (isset($_POST['featured_ad']) == '1') echo 'CHECKED'; ?> />
            <?php _e('Your listing will appear in the featured slider section at the top of the front page.','appthemes'); ?>
            <div class="clr"></div>
        </li>

    <?php 
    }

    // show the payment method box if enabled
    if(get_option('cp_charge_ads') == 'yes') {
    ?>

        <?php if (get_option('cp_price_scheme') == 'single'): ?>

            <li>
                <label><?php _e('Ad Package','appthemes'); ?>:</label>

                    <?php
                    // go get all the active ad packs and create a drop-down of options
                    $sql = $wpdb->prepare("SELECT pack_id, pack_name "
                         . "FROM ". $wpdb->prefix . "cp_ad_packs "
                         . "WHERE pack_status = 'active' "
                         . "ORDER BY pack_id asc");

                    $results = $wpdb->get_results($sql);

                    if($results) {
                    ?>

                    <select name="ad_pack_id" class="dropdownlist required">

                    <?php foreach ($results as $result) { ?>
                            <option value="<?php echo $result->pack_id; ?>"><?php echo $result->pack_name; ?></option>
                    <?php } ?>

                    </select>

                    <?php 
                    } else { ?>

                        <?php _e('Error: no ad pack has been defined.', 'appthemes') ?>

              <?php } ?>
                
                <div class="clr"></div>
            </li>

		<?php endif; ?>

		<?php if (get_option('cp_enable_coupons') == 'yes'): ?>

			<li>
				<label><?php _e('Coupon Code','appthemes'); ?>:</label>
				<input type="text" class="text" value="" id="cp_coupon_code" name="cp_coupon_code">
				<div class="clr"></div>
			</li>

		<?php endif;

	
    }  // end charge for ads check

} // end cp_other_fields function




// queries the db for the custom ad form based on the cat id
if (!function_exists('cp_show_form')) {
    function cp_show_form($catid) {
        global $wpdb;
        $fid = '';

        // call tinymce init code if html is enabled
        if (get_option('cp_allow_html') == 'yes')
                appthemes_tinymce($width=540, $height=200);

        //$catid = '129'; // used for testing

        // get the category ids from all the form_cats fields.
        // they are stored in a serialized array which is why
        // we are doing a separate select. If the form is not
        // active, then don't return any cats.

        $sql = "SELECT ID, form_cats "
                 . "FROM ". $wpdb->prefix . "cp_ad_forms "
                 . "WHERE form_status = 'active'";

        $results = $wpdb->get_results($sql);

        if($results) {

            // now loop through the recordset
            foreach ($results as $result) {

                // put the form_cats into an array
                $catarray = unserialize($result->form_cats);

                // now search the array for the $catid which was passed in via the cat drop-down
                if (in_array($catid,$catarray)) {
                    // when there's a catid match, grab the form id
                    $fid = $result->ID;

                    // put the form id into the post array for step2
                    $_POST['fid'] = $fid;
                }

            }


            // now we should have the formid so show the form layout based on the category selected
            $sql = $wpdb->prepare("SELECT f.field_label, f.field_name, f.field_type, f.field_values, f.field_perm, f.field_tooltip, m.meta_id, m.field_pos, m.field_req, m.form_id "
                     . "FROM ". $wpdb->prefix . "cp_ad_fields f "
                     . "INNER JOIN ". $wpdb->prefix . "cp_ad_meta m "
                     . "ON f.field_id = m.field_id "
                     . "WHERE m.form_id = '$fid' "
                     . "ORDER BY m.field_pos asc");


            $results = $wpdb->get_results($sql);

            if($results) {

                // loop through the custom form fields and display them
                echo cp_formbuilder($results);

            } else {

                // display the default form since there isn't a custom form for this cat
                echo cp_show_default_form();

            }


        } else {

            // display the default form since there isn't a custom form for this cat
            echo cp_show_default_form();

        }

        // show the image, featured ad, payment type and other options
        echo cp_other_fields();

    }
}



// if no custom forms exist, just call the default form fields
if (!function_exists('cp_show_default_form')) {
    function cp_show_default_form() {
        global $wpdb;

        // now we should have the formid so show the form layout based on the category selected
        $sql = $wpdb->prepare("SELECT field_label, field_name, field_type, field_values, field_tooltip, field_req "
                 . "FROM ". $wpdb->prefix . "cp_ad_fields "
                 . "WHERE field_core = '1' "
                 . "ORDER BY field_id asc");

        $results = $wpdb->get_results($sql);

        if($results) {

            // loop through the custom form fields and display them
            echo cp_formbuilder($results);

        } else {

            echo appthemes_nl2br(__('ERROR: no results found for the default ad form.', 'appthemes') . "\n\n");
        }

    }
}



// show the step 2 review page and query for the fields
// based on the cat they selected. This is an extra step
// but is much more secure and prevents fake forms from
// being submitted with malicious data
function cp_show_review($postvals) {
    global $wpdb;


    // if there's no form id it must mean the default form is being used so let's go grab those fields
    if(!($postvals['fid'])) {
        // use this if there's no custom form being used and give us the default form
        $sql = $wpdb->prepare("SELECT field_label, field_name, field_type, field_values, field_req "
             . "FROM ". $wpdb->prefix . "cp_ad_fields "
             . "WHERE field_core = '1' "
             . "ORDER BY field_id asc");

    } else {
        // now we should have the formid so show the form layout based on the category selected
        $sql = $wpdb->prepare("SELECT f.field_label,f.field_name,f.field_type,f.field_values,f.field_perm,m.meta_id,m.field_pos,m.field_req,m.form_id "
             . "FROM ". $wpdb->prefix . "cp_ad_fields f "
             . "INNER JOIN ". $wpdb->prefix . "cp_ad_meta m "
             . "ON f.field_id = m.field_id "
             . "WHERE m.form_id = '". $postvals['fid'] ."'"
             . "ORDER BY m.field_pos asc");
    }


    $results = $wpdb->get_results($sql);

    if($results) {

        // loop through the custom form fields and display them
        echo cp_formbuilder_review($results);

    } else {

        echo sprintf(__('ERROR: The form template for form ID %s does not exist or the session variable is empty.', 'appthemes'), $postvals['fid'] . "\n\n");
    }
    ?>

    <hr class="bevel" />
    <div class="clr"></div>


    <?php // if a payment method has been posted AND the total is not equal to zero
          if(isset($_POST['cp_payment_method']) && $postvals['cp_sys_total_ad_cost'] != 0) : ?>
        <li>
            <label><?php _e('Payment Method','appthemes');?>:</label>
            <div id="review"><?php echo ucfirst($_POST['cp_payment_method']); ?></div>
            <div class="clr"></div>
        </li>
    <?php endif; ?>

    <li>
        <label><?php _e('Ad Listing Fee','appthemes');?>:</label>
        <div id="review"><?php if (get_option('cp_charge_ads') == 'yes') { echo cp_pos_price(number_format($postvals['cp_sys_ad_listing_fee'], 2)); } else { echo __('FREE', 'appthemes'); } ?></div>
        <div class="clr"></div>
    </li>

    <?php if(isset($_POST['featured_ad'])) : ?>
        <li>
            <label><?php _e('Featured Listing Fee','appthemes');?>:</label>
            <div id="review"><?php echo cp_pos_price(number_format($postvals['cp_sys_feat_price'], 2)); ?></div>
            <div class="clr"></div>
        </li>
    <?php endif; ?>
    <?php if(isset($postvals['cp_coupon_type'])) : ?>
        <li>
            <label><?php _e('Coupon','appthemes');?>:</label>
			<?php if($postvals['cp_coupon_type'] != '%') : ?>
            <div id="review"><?php echo $postvals['cp_coupon_type'] . $postvals['cp_coupon']; ?></div>
            <?php else : ?>
			<div id="review"><?php echo str_replace('.00','',$postvals['cp_coupon']) . $postvals['cp_coupon_type']; ?></div>
            <?php endif; ?>
			<div class="clr"></div>
        </li>
    <?php endif; ?>

    <hr class="bevel-double" />
    <div class="clr"></div>

    <li>
        <label><?php _e('Total Amount Due','appthemes');?>:</label>
        <div id="review"><strong>
            <?php
            // if it costs to post an ad OR its free and someone selected a featured ad price
            if (get_option('cp_charge_ads') == 'yes' || isset($postvals['featured_ad'])) echo cp_pos_price($postvals['cp_sys_total_ad_cost']); else echo __('--');
            ?>
        </strong></div>
        <div class="clr"></div>
    </li>

<?php
}

// display the total cost per listing on the 1st step page
function cp_cost_per_listing() {

    // make sure we are charging for ads
    if(get_option('cp_charge_ads') == 'yes') {

        // now figure out which pricing scheme is set
        switch(get_option('cp_price_scheme')) :

        case 'category':
            $cost_per_listing = __('Price depends on category', 'appthemes');
        break;

        case 'single':
            $cost_per_listing = __('Price depends on ad package selected', 'appthemes'); // cp_pos_price(get_option('cp_price_per_ad'));
        break;

        case 'percentage':
            $cost_per_listing = get_option('cp_percent_per_ad') . __('% of your ad listing price', 'appthemes');
        break;

        default:
            // pricing structure must be free
            $cost_per_listing = __('Free', 'appthemes');
        endswitch;

    } else {
        // if we aren't charging, then ads must be free
        $cost_per_listing = __('Free', 'appthemes');
    }

    echo $cost_per_listing;

}


// give us just the ad listing fee
function cp_ad_listing_fee($catid, $ad_pack_id, $cp_price) {
    global $wpdb;

     // make sure we are charging for ads
    if(get_option('cp_charge_ads') == 'yes') {

        // now figure out which pricing scheme is set
        switch(get_option('cp_price_scheme')) :

        case 'category':

            // then lookup the price for this catid
            $cat_price = get_option('cp_cat_price_'.$catid); // 0

            // if cat price is blank then assign it default price
            if (isset($cat_price))
                $adlistingfee = $cat_price;
            else
                // set the price to the default ad value
                $adlistingfee = get_option('cp_price_per_ad');

        break;

        case 'percentage':

            // grab the % and then put it into a workable number
            $ad_percentage = (get_option('cp_percent_per_ad') * 0.01);

            // calculate the ad cost. Ad listing price x percentage.
            $adlistingfee = (trim($cp_price) * trim($ad_percentage));

        break;

        default: // pricing model must be single ad packs

            // make sure we have something if ad_pack_id is empty so no db error
            if(empty($ad_pack_id))
                $ad_pack_id = 1;

            // go get all the active ad packs and create a drop-down of options
            $sql = "SELECT pack_price, pack_duration "
                 . "FROM ". $wpdb->prefix . "cp_ad_packs "
                 . "WHERE pack_id = '$ad_pack_id' "
                 . "LIMIT 1";

            $results = $wpdb->get_row($sql);

            // now return the price and put the duration variable into an array
            if($results) {
                $adlistingfee = $results->pack_price;
                // $postvals['pack_duration'] = $results->pack_duration;
            } else {
                sprintf( __('ERROR: no ad packs found for ID %s.', 'appthemes'), $ad_pack_id );
            }

            // then cost per ad must be set to a flat fee
            //$adlistingfee = get_option('cp_price_per_ad');

        endswitch;

    }

    // return the ad listing fee
    return $adlistingfee;

}


function cp_get_ad_pack_length($ad_pack_id) {
    global $wpdb;
    // make sure we have something if ad_pack_id is empty so no db error
    if(empty($ad_pack_id))
        $ad_pack_id = 1;

    // go get all the active ad packs
    $sql = "SELECT pack_duration "
         . "FROM ". $wpdb->prefix . "cp_ad_packs "
         . "WHERE pack_id = '$ad_pack_id' "
         . "LIMIT 1";

    $results = $wpdb->get_row($sql);

    // now return the length of ad pack
    if($results)
        $ad_pack_length = $results->pack_duration;

    return $ad_pack_length;
}



// figure out what the total ad cost will be
function cp_calc_ad_cost($catid, $ad_pack_id, $featuredprice, $cp_price, $cp_coupon) {
    $adlistingfee = '';
    $totalcost_out = '';

    // if we're charging for ads calculate the price
    if (get_option('cp_charge_ads') == 'yes')
        $adlistingfee = cp_ad_listing_fee($catid, $ad_pack_id, $cp_price);

    // calculate the total cost for the ad.
    $totalcost_out = $adlistingfee + $featuredprice;

    //discount for coupon amount if its set
    if(isset($cp_coupon->coupon_discount) && isset($cp_coupon->coupon_discount_type)) {
        if($cp_coupon->coupon_discount_type  != '%')
            $totalcost_out = $totalcost_out - (float)$cp_coupon->coupon_discount;
        else
            $totalcost_out = $totalcost_out - ($totalcost_out * (((float)($cp_coupon->coupon_discount))/100));
    }
	
    //set proper return format
    $totalcost_out = number_format($totalcost_out, 2);	

    return $totalcost_out;

}


// determine what the ad post status should be
if (!function_exists('cp_set_post_status')) {
    // determine what the ad post status should be
    function cp_set_post_status($advals) {
        global $wpdb;
        //by default we will return post status as pending unless rules allow live posting
        $post_status = 'pending';

        // if the post status option is NOT set to pending, and costs zero currency, then publish
        if ((get_option('cp_post_status') <> 'pending') && $advals['cp_sys_total_ad_cost'] == '0.00')
            $post_status = 'publish';

        return $post_status;
    }
}


// this is where the new ad gets created
function cp_add_new_listing($advals) {
    global $wpdb;
    $new_tags = '';
    $ad_length = '';
    $attach_id = '';
    $the_attachment = '';

    // tags are tricky and need to be put into an array before saving the ad
    if (!empty($advals['tags_input']))
        $new_tags = explode(',', $advals['tags_input']);


    // put all the new ad elements into an array
    // these are the minimum required fields for WP (except tags)
    $new_ad                   = array();
    $new_ad['post_title']     = appthemes_filter($advals['post_title']);
    $new_ad['post_content']   = trim($advals['post_content']);
    $new_ad['post_status']    = cp_set_post_status($advals);
    $new_ad['post_author']    = $advals['user_id'];
    $new_ad['post_type']      = 'ad_listing'; //array

    // make sure the WP sanitize_post function doesn't strip out embed & other html
    if (get_option('cp_allow_html') == 'yes')
        $new_ad['filter'] = true;

    //print_r($new_ad).' <- new ad array<br>';

    // insert the new ad
    $post_id = wp_insert_post($new_ad);
	
    //set the custom post type categories
    wp_set_post_terms( $post_id, appthemes_filter($advals['cat']), 'ad_cat', false);

    //set the custom post type tags
    wp_set_post_terms($post_id, $new_tags, 'ad_tag', false );
	
    // the unique order ID we created becomes the ad confirmation ID
    // we will use this for payment systems and for activating the ad
    // later if need be. it needs to start with cp_ otherwise it won't
    // be loaded in with the ad so let's give it a new name
    $advals['cp_sys_ad_conf_id'] = $advals['oid'];

    // get the ad duration and first see if ad packs are being used
    // if so, get the length of time in days otherwise use the default
    // prune period defined on the CP settings page
    if(isset($advals['pack_duration']))
        $ad_length = $advals['pack_duration'];
    else
        $ad_length = get_option('cp_prun_period');

    // set the ad listing expiration date and put into a session
    $ad_expire_date = date_i18n('m/d/Y H:i:s', strtotime('+' . $ad_length . ' days')); // don't localize the word 'days'
    $advals['cp_sys_expire_date'] = $ad_expire_date;
    $advals['cp_sys_ad_duration'] = $ad_length;


    // now add all the custom fields into WP post meta fields
    foreach($advals as $meta_key => $meta_value) {
        if (appthemes_str_starts_with($meta_key, 'cp_'))
            add_post_meta($post_id, $meta_key, $meta_value, true);
    }

    // if they checked the box for a featured ad, then make the post sticky
    if (isset($advals['featured_ad']))
        stick_post($post_id);

    if (isset($advals['attachment'])) {
        $the_attachment = $advals['attachment'];
        // associate the already uploaded images to the new ad and create multiple image sizes
        $attach_id = cp_associate_images($post_id, $the_attachment);
    }

    // set the thumbnail pic on the WP post
    //cp_set_ad_thumbnail($post_id, $attach_id);

    // kick back the post id in case we want to use it
    return $post_id;

}



?>
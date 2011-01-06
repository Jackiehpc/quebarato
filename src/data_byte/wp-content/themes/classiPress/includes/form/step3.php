<?php
/**
 * This is step 3 of 3 for the ad submission form
 * 
 * @package ClassiPress
 * @subpackage New Ad
 * @author AppThemes
 * @copyright 2010
 *
 *
 */


global $userdata;
global $wpdb;

// now get all the ad values which we stored in an associative array in the db
// first we do a check to make sure this session still exists and then we'll
// use this option array to create the new ad below
$advals = get_option('cp_'.$_POST['oid']);
$advals['cp_payment_method'] = $_POST['cp_payment_method'];

// check and make sure the form was submitted from step 2 and the hidden oid matches the oid in the db
// we don't want to create duplicate ad submissions if someone reloads their browser
if(isset($_POST['step2']) && (strcasecmp($_POST['oid'],$advals['oid']) == 0)) {
?>


   <div id="step3"></div>

   <h2 class="dotted">
   <?php if (get_option('cp_charge_ads') == 'yes') { echo __('Final Step','cp'); } else { echo __('Ad Listing Received','cp'); } ?>
   </h2>

   <img src="<?php bloginfo('template_url'); ?>/images/step3.gif" alt="" class="stepimg" />


    <div class="thankyou">


    <?php
    // insert the ad and get back the post id
    $post_id = cp_add_new_listing($advals);

    // call in the selected payment gateway as long as the price isn't zero
    if ((get_option('cp_charge_ads') == 'yes') && ($advals['cp_sys_total_ad_cost'] != 0)) {

        include_once (TEMPLATEPATH . '/includes/gateways/gateway.php');

    } else {

    // otherwise the ad was free and show the thank you page.
        // get the post status
        $the_post = get_post($post_id); 

        // check to see what the ad status is set to
        if ($the_post->post_status == 'pending') {

            // send ad owner an email
            cp_owner_new_ad_email($post_id);

        ?>

            <h3><?php _e('Thank you! Your ad listing has been submitted for review.','cp') ?></h3>
            <p><?php _e('You can check the status by viewing your dashboard.','cp') ?></p>

        <?php } else { ?>

            <h3><?php _e('Thank you! Your ad listing has been submitted and is now live.','cp') ?></h3>
            <p><?php _e('Visit your dashboard to make any changes to your ad listing or profile.','cp') ?></p>
            <a href="<?php echo get_permalink($post_id); ?>"><?php _e('View your new ad listing.','cp') ?></a>

        <?php } ?>


    </div> <!-- /thankyou -->

    <?php
    }


    // send new ad notification email to admin
    if (get_option('cp_new_ad_email') == 'yes')
        cp_new_ad_email($post_id);


    // remove the temp session option from the database
    delete_option('cp_'.$_POST['oid']);

    ?>



<?php

} else {

?>

    <h2 class="dotted"><?php _e('An Error Has Occurred','cp') ?></h2>

    <div class="thankyou">
        <p><?php _e('Your session has expired or you are trying to submit a duplicate ad. Please start over.','cp') ?></p>
    </div>

<?php

}

?>

    <div class="pad100"></div>


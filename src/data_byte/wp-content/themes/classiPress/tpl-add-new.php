<?php
/*
Template Name: Add New Listing
*/

// if not logged in, redirect to login page
auth_redirect_login();

// don't cache the headers
//nocache_headers();
// this is needed for IE to work with the go back button
header("Cache-control: private");

// grabs the user info and puts into vars
global $current_user;
get_currentuserinfo(); 


// needed for file uploading to work
if (defined('ABSPATH')) {
    require_once (ABSPATH . 'wp-admin/includes/file.php');
    require_once (ABSPATH . 'wp-admin/includes/image.php');
} else {
    require_once ('../wp-admin/includes/file.php');
    require_once ('../wp-admin/includes/image.php');
}

// load up the validate and tinymce scripts
add_action('wp_print_scripts', 'cp_load_form_scripts');

// include all the functions needed for this form
include_once (TEMPLATEPATH . '/includes/forms/step-functions.php');
?>


<?php get_header(); ?>

<script type='text/javascript'>
// <![CDATA[
jQuery(document).ready(function(){

	/* setup the form validation */
	jQuery("#mainform").validate({errorClass: "invalid"});

	/* setup the tooltip */
    jQuery("#mainform a").easyTooltip();

});


/* Form Checkboxes Values Function */
function addRemoveCheckboxValues($cbval, $cbGroupVals) {
    if($cbval.checked==true) {
        $a = document.getElementById($cbGroupVals);
        $a.value += ','+$cbval.value;
        $a.value = $a.value.replace(/^\,/,'');
    } else {
        $a = document.getElementById($cbGroupVals);
        $a.value = $a.value.replace($cbval.value+',','');
        $a.value = $a.value.replace($cbval.value,'');
        $a.value = $a.value.replace(/\,$/,'');
    }
}

/* General Trim Function Based on Fastest Executable Trim */
function trim (str) {
    var	str = str.replace(/^\s\s*/, ''),
            ws = /\s/,
            i = str.length;
    while (ws.test(str.charAt(--i)));
    return str.slice(0, i + 1);
}

/* Used for enabling the image for uploads */
function enableNextImage($a, $i) {
    jQuery('#upload'+$i).removeAttr("disabled");
}



// ]]>
</script>

<!-- CONTENT -->
  <div class="content">

    <div class="content_botbg">

      <div class="content_res">

        <!-- full block -->
        <div class="shadowblock_out">

          <div class="shadowblock">


            <?php

            // check and make sure the form was submitted from step1 and the session value exists
            if(isset($_POST['step1'])) {

                include_once(TEMPLATEPATH . '/includes/forms/step2.php');

            } elseif(isset($_POST['step2'])) {

                include_once(TEMPLATEPATH . '/includes/forms/step3.php');

            } else {

                // create a unique ID for this new ad order
                // uniqid requires a param for php 4.3 or earlier. added for 3.0.1
                $order_id  = uniqid(rand(10,1000), false);
                include_once(TEMPLATEPATH . '/includes/forms/step1.php');

            }
            ?>   
  

            </div><!-- /shadowblock -->

        </div><!-- /shadowblock_out -->

        <div class="clr"></div>

      </div><!-- /content_res -->

    </div><!-- /content_botbg -->

  </div><!-- /content -->
	
   
<?php get_footer(); ?>


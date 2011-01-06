<?php
/*
 * Payment gateway scripts
 *
 */

?> <?php if( 'banktransfer' != $advals['cp_payment_method']) { ?>

    <center>
        
	<h2><?php _e('Please wait while we redirect you to our payment page.', 'appthemes');?></h2>

        <div class="payment-loader"></div>

	<p class="small"><?php _e('(Click the button below if you are not automatically redirected within 5 seconds.)', 'appthemes');?></p>

    </center>

<?php } ?>

<?php

    // determine which payment gateway was selected and serve up the correct script
    switch($advals['cp_payment_method']) :

        case 'paypal':
            if (file_exists(TEMPLATEPATH . '/includes/gateways/paypal/paypal.php'))
                include_once (TEMPLATEPATH . '/includes/gateways/paypal/paypal.php');
                echo cp_dashboard_paypal_button($post_id, '');
        break;

        case 'banktransfer':
            if (file_exists(TEMPLATEPATH . '/includes/gateways/banktransfer/banktransfer.php'))
                include_once (TEMPLATEPATH . '/includes/gateways/banktransfer/banktransfer.php');
                echo cp_banktransfer($post_id, '');
        break;

        case 'gcheckout':
            if (file_exists(TEMPLATEPATH . '/includes/gateways/gcheckout/gcheckout.php'))
                include_once (TEMPLATEPATH . '/includes/gateways/gcheckout/gcheckout.php');
        break;

        case '2checkout':
            if (file_exists(TEMPLATEPATH . '/includes/gateways/2checkout/2checkout.php'))
                include_once (TEMPLATEPATH . '/includes/gateways/2checkout/2checkout.php');
        break;

        case 'authorize':
            if (file_exists(TEMPLATEPATH . '/includes/gateways/authorize/authorize.php'))
                include_once (TEMPLATEPATH . '/includes/gateways/authorize/authorize.php');
        break;

        case 'chronopay':
            if (file_exists(TEMPLATEPATH . '/includes/gateways/chronopay/chronopay.php'))
                include_once (TEMPLATEPATH . '/includes/gateways/chronopay/chronopay.php');
        break;

        case 'mbookers':
            if (file_exists(TEMPLATEPATH . '/includes/gateways/mbookers/mbookers.php'))
                include_once (TEMPLATEPATH . '/includes/gateways/mbookers/mbookers.php');
        break;

        default:
            echo __('Error: No payment gateway can be found or your session has timed out.', 'appthemes');
        break;

    endswitch;

?>


<div class="pad100"></div>
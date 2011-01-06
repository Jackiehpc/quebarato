<?php
/**
 * WordPress Registration Form
 * Function outputs the registration form
 *
 *
 * @author AppThemes
 * @package ClassiPress
 * @copyright 2010 all rights reserved
 *
 */

function app_register_form( $action = '' ) {	
    global $posted, $app_abbr;

    if ( get_option('users_can_register') ) :

        if (!$action) $action = site_url('wp-login.php?action=register');
?>
            
            <form action="<?php echo $action; ?>" method="post" class="loginform" name="registerform" id="registerform">

                <p>
                    <label><?php _e('Username:','appthemes') ?></label>
                    <input tabindex="1" type="text" class="text" name="your_username" id="your_username" value="<?php if (isset($posted['your_username'])) echo attribute_escape(stripslashes($posted['your_username'])); ?>" />
                </p>

                <p>
                    <label><?php _e('Email:','appthemes') ?></label>
                    <input tabindex="2" type="text" class="text" name="your_email" id="your_email" value="<?php if (isset($posted['your_email'])) echo attribute_escape(stripslashes($posted['your_email'])); ?>" />
                </p>

                <p>
                    <label><?php _e('Password:','appthemes') ?></label>
                    <input tabindex="3" type="password" class="text" name="your_password" id="your_password" value="" />
                </p>

                <p>
                    <label><?php _e('Password Again:','appthemes') ?></label>
                    <input tabindex="4" type="password" class="text" name="your_password_2" id="your_password_2" value="" />
                </p>
				
                <?php 
					// include the spam checker if enabled
					appthemes_recaptcha();
				?>

                <div id="checksave">

                    <p class="submit">
                        <input tabindex="6" class="btn_orange" type="submit" name="register" id="wp-submit" value="<?php _e('Create Account','appthemes'); ?>" />
                    </p>

                </div>

            </form>
	
<!-- autofocus the field -->
<script type="text/javascript">try{document.getElementById('your_username').focus();}catch(e){}</script>

<?php else : ?>

    <p><?php _e('** User registration is currently disabled. Please contact the site administrator. **', 'appthemes') ?></p>

<?php endif; ?>

<?php } ?>
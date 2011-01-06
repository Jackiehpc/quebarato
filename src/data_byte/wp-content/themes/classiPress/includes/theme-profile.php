<?php

/**
 * Add more profile fields to the user
 *
 * Easy to add new fields to the user profile by just
 * creating your new section below and adding a new
 * update_usermeta line
 *
 * @since 3.0.0
 * @uses show_user_profile & edit_user_profile WordPress functions
 *
 * @param int $user User Object
 * @return bool True on successful update, false on failure.
 *
 */
if (!function_exists('cp_profile_fields')) {
    function cp_profile_fields($user) {
    ?>
	
        <table class="form-table">

            <tr>
                <th><label for="twitter"><?php _e('Twitter:','appthemes')?></label></th>

                <td>
                    <input type="text" name="twitter_id" id="twitter_id" value="<?php echo esc_attr(get_the_author_meta('twitter_id', $user->ID)); ?>" class="regular-text" size="35" /><br />
                    <span class="description"><?php _e('Enter your Twitter username without the URL.','appthemes')?></span>
                </td>
            </tr>

            <tr>
                <th><label for="facebook"><?php _e('Facebook:','appthemes')?></label></th>

                <td>
                    <input type="text" name="facebook_id" id="facebook_id" value="<?php echo esc_attr(get_the_author_meta('facebook_id', $user->ID)); ?>" class="regular-text" /><br />
                    <span class="description"><?php printf(__("Enter your Facebook username without the URL. <br />Don't have one yet? <a target='_blank' href='%s'>Get a custom URL.</a>",'appthemes'), 'http://www.facebook.com/username/')?></span>
                </td>
            </tr>

        </table>


        <table class="form-table">

            <tr>
                <th><label for="paypal"><?php _e('PayPal Email:','appthemes')?></label></th>

                <td>
                    <input type="text" name="paypal_email" id="paypal_email" value="<?php echo esc_attr(get_the_author_meta('paypal_email', $user->ID)); ?>" class="regular-text" /><br />
                    <span class="description"><?php _e('Used for purchasing ads via PayPal (if enabled).','appthemes')?></span>
                </td>
            </tr>

        </table>

    <?php
    }
}//end cp_profile_fields

if (!function_exists('cp_profile_fields_save')) {
    function cp_profile_fields_save($user_id) {
        if (!current_user_can('edit_user', $user_id ))
            return false;

        /* Copy and paste this line for additional fields. Make sure to change 'twitter' to the field ID. */
        update_usermeta( $user_id, 'twitter_id', $_POST['twitter_id'] );
        update_usermeta( $user_id, 'facebook_id', $_POST['facebook_id'] );
        update_usermeta( $user_id, 'paypal_email', $_POST['paypal_email'] );
    }
}


// hook these new fields into the profile page with high priority
add_action('show_user_profile', 'cp_profile_fields', 0);
add_action('edit_user_profile', 'cp_profile_fields');

// save the updated profile field information
add_action('personal_options_update', 'cp_profile_fields_save');
add_action('edit_user_profile_update', 'cp_profile_fields_save');

?>
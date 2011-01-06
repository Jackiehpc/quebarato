<?php
/**
 * WordPress Register Process
 * Processes the registration forms and returns errors/redirects to a page
 *
 *
 * @version 1.0
 * @author AppThemes
 * @package ClassiPress
 * @copyright 2010 all rights reserved
 *
 */

function app_process_register_form( $success_redirect = '' ) {
	
	// if (!$success_redirect) $success_redirect = get_permalink(get_option('app_myjobs_page_id'));
	
	if ( get_option('users_can_register') ) :
		
		global $posted, $app_abbr;
		
		$posted = array();
		$errors = new WP_Error();
		
		if (isset($_POST['register']) && $_POST['register']) {
		
			// include the WP registration core
			require_once( ABSPATH . WPINC . '/registration.php');
			
			// process the reCaptcha request if it's been enabled	
			if (get_option($app_abbr.'_captcha_enable') == 'yes') {	
				require_once (TEMPLATEPATH . '/includes/lib/recaptchalib.php');
				$resp = null;
				$error = null;
				
				// check and make sure the reCaptcha values match
				$resp = recaptcha_check_answer(
					get_option($app_abbr.'_captcha_private_key'), 
					$_SERVER["REMOTE_ADDR"], 
					$_POST["recaptcha_challenge_field"], 
					$_POST["recaptcha_response_field"]
				);
			}
		
			// Get (and clean) data
			$fields = array(
				'your_username',
				'your_email',
				'your_password',
				'your_password_2'
			);
			foreach ($fields as $field) {
				$posted[$field] = stripslashes(trim($_POST[$field]));
			}
		
			$user_login = sanitize_user( $posted['your_username'] );
			$user_email = apply_filters( 'user_registration_email', $posted['your_email'] );
			
		
			// Check the username
			if ( $posted['your_username'] == '' )
				$errors->add('empty_username', __('<strong>ERROR</strong>: Please enter a username.', 'appthemes'));
			elseif ( !validate_username( $posted['your_username'] ) ) {
				$errors->add('invalid_username', __('<strong>ERROR</strong>: This username is invalid.  Please enter a valid username.', 'appthemes'));
				$posted['your_username'] = '';
			} elseif ( username_exists( $posted['your_username'] ) )
				$errors->add('username_exists', __('<strong>ERROR</strong>: This username is already registered, please choose another one.', 'appthemes'));
		
			// Check the e-mail address
			if ($posted['your_email'] == '') {
				$errors->add('empty_email', __('<strong>ERROR</strong>: Please enter an e-mail address.', 'appthemes'));
			} elseif ( !is_email( $posted['your_email'] ) ) {
				$errors->add('invalid_email', __('<strong>ERROR</strong>: The email address format is invalid.', 'appthemes'));
				$posted['your_email'] = '';
			} elseif ( email_exists( $posted['your_email'] ) )
				$errors->add('email_exists', __('<strong>ERROR</strong>: This email is already in use. Please choose another one.', 'appthemes'));
			
			// Check Passwords match
			if ($posted['your_password'] == '')	
				$errors->add('empty_password', __('<strong>ERROR</strong>: Please enter a password.', 'appthemes'));
			elseif ($posted['your_password_2'] == '')
				$errors->add('empty_password', __('<strong>ERROR</strong>: Please enter the password twice.', 'appthemes'));
			elseif ($posted['your_password'] !== $posted['your_password_2'])
				$errors->add('wrong_password', __('<strong>ERROR</strong>: Passwords do not match.', 'appthemes'));
				
			// display the reCaptcha error msg if it's been enabled	
			if (get_option('cp_captcha_enable') == 'yes') {		
				// Check reCaptcha  match
				if (!$resp->is_valid)
					$errors->add('invalid_captcha', __('<strong>ERROR</strong>: The reCaptcha anti-spam response was incorrect.', 'appthemes'));
					//$error = $resp->error;	
			}		
				
			
			do_action('register_post', $posted['your_username'], $posted['your_email'], $errors);
			$errors = apply_filters( 'registration_errors', $errors, $posted['your_username'], $posted['your_email'] );
		
			if ( !$errors->get_error_code() ) {			
				$user_pass = $posted['your_password'];
				$user_id = wp_create_user(  $posted['your_username'], $user_pass, $posted['your_email'] );
				if ( !$user_id ) {
					$errors->add('registerfail', sprintf(__('<strong>ERROR</strong>: Couldn&#8217;t register you... please contact the <a href="mailto:%s">webmaster</a> !', 'appthemes'), get_option('admin_email')));
					return array( 'errors' => $errors, 'posted' => $posted);
				}
				
				// Change role
				// wp_update_user( array ('ID' => $user_id, 'role' => 'contributor') ) ;

				// set the first login date/time
				appthemes_first_login($user_id);
			
				// send the user a confirmation and their login details
				app_new_user_notification($user_id, $user_pass);
				
				$secure_cookie = is_ssl() ? true : false;
					
				wp_set_auth_cookie($user_id, true, $secure_cookie);

				### Redirect
				wp_redirect($success_redirect);
				exit;
			} else {
				return array( 'errors' => $errors, 'posted' => $posted);
			}
		}
		
	endif;

}
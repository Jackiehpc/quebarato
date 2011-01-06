<?php

/**
 * Add footer elements via the wp_footer hook
 *
 * Anything you add to this file will be dynamically
 * inserted in the footer of your theme
 *
 * @since 3.0.0
 * @uses cp_footer_actions
 *
 */

// insert the google analytics tracking code in the footer
function cp_google_analytics_code() {

	// echo "\n\n" . '<!-- start wp_footer -->' . "\n\n";

	if (get_option('cp_google_analytics') <> '')
		echo stripslashes(get_option('cp_google_analytics'));
	?>
	<script type="text/javascript" >
		var ajaxurl = '<?php echo admin_url( 'admin-ajax.php' );  ?>';
	</script>
	<?php

	// echo "\n\n" . '<!-- end wp_footer -->' . "\n\n";

}
add_action('wp_footer', 'cp_google_analytics_code');


// enable the gravatar hovercards
function cp_gravatar_hovercards() {
	global $app_abbr;

    if (get_option($app_abbr.'_use_hovercards') == 'yes')
		wp_enqueue_script('gprofiles', get_bloginfo('template_directory').'/includes/js/gprofiles.js', array('jquery'), '1.0', true);

}

add_action('wp_enqueue_scripts','cp_gravatar_hovercards');


?>
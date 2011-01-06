<?php
/**
 * These are scripts used within the theme
 * To increase speed and performance, we only want to
 * load them when needed
 *
 * @package ClassiPress
 *
 */


// correctly load all the jquery scripts so they don't conflict with plugins
function cp_load_scripts() {
    global $app_abbr;

	// load google cdn hosted scripts if enabled
    if (get_option($app_abbr.'_google_jquery') == 'yes') {
		wp_deregister_script('jquery');
		wp_register_script('jquery', ('http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js'), false, '1.4.2');
		wp_deregister_script('jquery-ui');
		wp_register_script('jquery-ui', ('http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.6/jquery-ui.min.js'), false, '1.8.6');
		wp_enqueue_script('jquery-ui');
	} else {
		wp_enqueue_script('jquery-ui-core_latest', get_bloginfo('template_directory').'/includes/js/jquery-ui/ui.core.js', false, '1.8.5');
	}

	wp_enqueue_script('jquery'); // load no matter what
	wp_enqueue_script('autocomplete', get_bloginfo('template_directory').'/includes/js/ui.autocomplete.js', array('jquery'), '1.8.5');
    wp_enqueue_script('jqueryeasing', get_bloginfo('template_directory').'/includes/js/easing.js', array('jquery'), '1.3');
    wp_enqueue_script('jcarousellite', get_bloginfo('template_directory').'/includes/js/jcarousellite_1.0.1.js', array('jquery'), '1.0.1');
    wp_enqueue_script('theme-scripts', get_bloginfo('template_directory').'/includes/js/theme-scripts.js', array('jquery'), '3.1');
    wp_enqueue_script('superfish', get_bloginfo('template_directory').'/includes/js/superfish.js', array('jquery'), '1.4.8');

	// only load the general.js if it's been enabled
    if (get_option($app_abbr.'_general_js') == 'yes')
		wp_enqueue_script('general', get_bloginfo('template_directory').'/includes/js/general.js', array('jquery'), '1.0');

    // only load cufon if it's been enabled
    if (get_option($app_abbr.'_cufon_enable') == 'yes')
        wp_enqueue_script('cufon-yui', get_bloginfo('template_directory').'/includes/js/cufon-yui.js', array('jquery'), '1.0.9');

    if(is_singular('ad_listing')) // only load colorbox when we need it
        wp_enqueue_script('colorbox', get_bloginfo('template_directory').'/includes/js/colorbox/jquery.colorbox-min.js', array('jquery'), '1.3.9');
}


// this function is called when submitting a new ad listing in tpl-add-new.php
function cp_load_form_scripts() {
    global $app_abbr;

    // only load the tinymce editor when html is allowed
    if (get_option($app_abbr.'_allow_html') == 'yes') {
        wp_enqueue_script('tiny_mce', get_bloginfo('url').'/wp-includes/js/tinymce/tiny_mce.js', array('jquery'), '3.0');
        wp_enqueue_script('wp-langs-en', get_bloginfo('url').'/wp-includes/js/tinymce/langs/wp-langs-en.js', array('jquery'), '3241-1141');
    }
    wp_enqueue_script('validate', get_bloginfo('template_directory').'/includes/js/validate/jquery.validate.pack.js', array('jquery'), '1.6');
	wp_enqueue_script('easytooltip', get_bloginfo('template_directory').'/includes/js/easyTooltip.js', array('jquery'), '1.0');

    // add the language validation file if not english
    if (get_option($app_abbr.'_form_val_lang')) {
        $lang_code = strtolower(get_option($app_abbr.'_form_val_lang'));
        wp_enqueue_script('validate-lang', get_bloginfo('template_directory')."/includes/js/validate/localization/messages_$lang_code.js", array('jquery'), '1.6');
    }
}


// load the css files correctly
function cp_load_styles() {

    if(is_singular('ad_listing')) { // only load colorbox when we need it
        wp_register_style('colorbox', get_bloginfo('template_directory').'/includes/js/colorbox/colorbox.css', false, '3.0.1');
        wp_enqueue_style('colorbox');
    }
		wp_register_style('autocomplete', get_bloginfo('template_directory').'/includes/js/jquery-ui/jquery-ui-1.8.5.autocomplete.css', false, '1.8.5');
		wp_enqueue_style('autocomplete');

}

// to speed things up, don't load these scripts in the WP back-end (which is the default)
if(!is_admin()) {
    add_action('wp_print_scripts', 'cp_load_scripts');
    add_action('wp_print_styles', 'cp_load_styles');
}




?>

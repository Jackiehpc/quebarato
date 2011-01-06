<?php

/**
 * Add header elements via the wp_head hook
 *
 * Anything you add to this file will be dynamically
 * inserted in the header of your theme
 *
 * @since 3.0.0
 * @uses wp_head
 *
 */

global $wpdb;

// adds CP version number in the header for troubleshooting
function cp_version($app_version) {
    global $app_version;
    echo "\n\t" .'<meta name="version" content="ClassiPress '.$app_version.'" />' . "\n";
}


// changes the css file based on what is selected on the options page
if(!function_exists('cp_style_changer')) { function cp_style_changer() {

	echo "\t" . '<link href="'. get_bloginfo('stylesheet_url') .'" rel="stylesheet" type="text/css" />' . "\n";
	
    // turn off stylesheets if customers want to use child themes
    if (get_option('cp_disable_stylesheet') <> 'yes') {
        if (get_option('cp_stylesheet')) {
            echo "\t" . '<link href="' . get_bloginfo('template_directory') . '/styles/' . get_option('cp_stylesheet') . '" rel="stylesheet" type="text/css" />' . "\n";
        } else {
            echo "\t" . '<link href="' . get_bloginfo('template_directory') . '/styles/red.css" rel="stylesheet" type="text/css" />' . "\n";
        }
    }

	if (file_exists(TEMPLATEPATH . '/styles/custom.css'))
		echo "\t" . '<link href="'. get_bloginfo('template_directory') .'/styles/custom.css" rel="stylesheet" type="text/css" />' . "\n";

}}

// adds support for cufon font replacement
function cp_cufon_styles() {
?>

<!-- cufon fonts  -->
<script src="<?php echo get_bloginfo('template_directory') ?>/includes/fonts/Vegur_400-Vegur_700.font.js" type="text/javascript"></script>
<script src="<?php echo get_bloginfo('template_directory') ?>/includes/fonts/Liberation_Serif_400.font.js" type="text/javascript"></script>
<!-- end cufon fonts  -->

<!-- cufon font replacements --> 
	<script type="text/javascript">
		// <![CDATA[
		<?php echo stripslashes(get_option('cp_cufon_code')). "\n"; ?>
		// ]]>
    </script>            
<!-- end cufon font replacements -->

<?php 
}


// select the searched category from the drop-down list
function cp_select_search_cat() {
	global $wp_query;

	$catid = $wp_query->query_vars['cat'];

	if (!empty($catid)) :

	?>
		<script type="text/javascript">
			// <![CDATA[
			jQuery(document).ready(function(){
				jQuery("select#cat option[value='<?php echo $catid ?>']").attr("selected","selected");
			});
		   // ]]>
		</script>

	<?php

	endif;

}



add_action('wp_head', 'cp_version');
add_action('wp_head', 'cp_style_changer');

// only echo out the cufon .js if it's been enabled
if (get_option('cp_cufon_enable') <> 'no')
	add_action('wp_head', 'cp_cufon_styles');

// only add action if it's on the search results
if(isset($_GET['s']))
	add_action('wp_head', 'cp_select_search_cat');



?>
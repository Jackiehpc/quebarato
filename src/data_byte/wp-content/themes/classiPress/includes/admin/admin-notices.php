<?php

/**
 * These are the admin alert messages displayed
 * on the WordPress admin pages
 * 
 * 
 */


function cp_admin_info_box() {

    // reserved for future use    

}			

// display msg if permalinks aren't setup correctly
function cp_permalink_nag() {

    if (current_user_can('manage_options'))
        $msg = sprintf( __('You need to set your <a href="%1$s">permalink custom structure</a> to at least contain <b>/&#37;postname&#37;/</b> before ClassiPress will work properly.', 'appthemes'), 'options-permalink.php');

    echo "<div class='error fade'><p>$msg</p></div>";
}

if (!stristr(get_option('permalink_structure'), '%postname%'))
    add_action('admin_notices', 'cp_permalink_nag', 3);



// display msg if paypal sandbox mode is turned on
function cp_paypal_nag() {

    if (current_user_can('manage_options'))
        $msg = sprintf( __('ClassiPress is currently running in PayPal Sandbox mode. Remember to <a href="%1$s">uncheck the box</a> when you are ready to start selling ads again.', 'appthemes'), 'admin.php?page=gateways');

    echo "<div class='error fade'><p>$msg</p></div>";
}

if (get_option('cp_paypal_sandbox') == 'true')
    add_action('admin_notices', 'cp_paypal_nag', 3);



// display msg if googlecheckout sandbox mode is turned on
function cp_gcheckout_nag() {

    if (current_user_can('manage_options'))
        $msg = sprintf( __('ClassiPress is currently running in Google Sandbox mode. Remember to <a href="%1$s">uncheck the box</a> when you are ready to start selling ads again.', 'appthemes'), 'admin.php?page=gateways');

    echo "<div class='error fade'><p>$msg</p></div>";
}

if (get_option('cp_google_sandbox') == 'true')
    add_action('admin_notices', 'cp_gcheckout_nag', 3);


// display msg if instance needs to be upgraded
function cp_upgrade_nag() {
	global $wpdb;

    if (current_user_can('manage_options') && $_POST['submitted'] != 'convertToCustomPostType') :

		// get all the blog categories in a comma delimited string
		$incats = cp_get_blog_cat_ids();

		$sql = $wpdb->prepare("SELECT count(ID)
		FROM $wpdb->posts wposts
		LEFT JOIN $wpdb->term_relationships ON (wposts.ID = $wpdb->term_relationships.object_id)
		LEFT JOIN $wpdb->term_taxonomy ON ($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)
		WHERE $wpdb->term_taxonomy.taxonomy = 'category'
		AND $wpdb->term_taxonomy.term_id NOT IN($incats)
		AND post_type = 'post'");

		$ads_to_migrate = $wpdb->get_var($sql);

		// get the blog cats and nice names
		$blog_cats = get_categories("hide_empty=0&include=$incats");
		foreach ($blog_cats as $blog_cat)
			$the_cats .= $blog_cat->name . ', ';

		$the_cats = trim($the_cats, ', ');

		// get the total count of ad categories
		$ad_cats = get_categories("hide_empty=0&exclude=$incats");
		$cats_to_migrate = count($ad_cats);

		// get the total count of tags
		$all_tags = get_tags();
		$tags_to_migrate = count($all_tags);
?>

		<div id="message2" class="updated">

			<h3>Important Notice - ClassiPress Upgrade Required</h3>
			<p>Your ClassiPress Version: <strong><?php echo get_option('cp_version_old'); ?></strong>
			<br/>Latest ClassiPress Version: <strong style="color:#009900;"><?php echo get_option('cp_version'); ?></strong></p>
			<p>Your database needs to be updated before using this version of ClassiPress. It's important to first back-up your database <strong>BEFORE</strong> running this upgrade tool. We are not responsible for any lost or corrupt data. We recommend using the <a href='http://wordpress.org/extend/plugins/wp-db-backup/' target='_blank'>WP-DB plugin</a> to easily back-up your database. To install it directly from within WordPress, just go to your '<a href='plugin-install.php'>Add New</a>' plugins page and search for 'WP-DB-Backup'. For more instructions, see <a href='http://codex.wordpress.org/Backing_Up_Your_Database#Installation' target='_blank'>this page</a>.</p>

			<h3>What will this upgrade do?</h3>
			<p>Since ClassiPress now uses custom post types and taxonomies for ads, we need to move all your ads, ad categories, and copy your tags from 'posts' to 'ads'. See the new 'Ads' menu group in your left-hand sidebar? Yep, that's where we're going to move them.</p>
			<p>This script will take any ads NOT assigned to your blog categories (and blog sub-categories) which in your case are: <strong style="color:#009900;"><?php echo $the_cats ?></strong> and move them over. If this doesn't look correct or you wish to move ads out of these categories, please do so before running this script. These blog categories are determined by your "Blog Category ID" option on your settings page.</p>

			<p>This script will attempt to move your <strong style="color:#009900;"><?php echo number_format($ads_to_migrate); ?> ads, <?php echo number_format($cats_to_migrate); ?> ad categories, and <?php echo number_format($tags_to_migrate); ?> tags</strong> under the new 'Ads' menu group. <strong>NOTE</strong>: Only tags assigned to an ad will be moved over so less than the total tags found (<strong><?php echo number_format($tags_to_migrate); ?> tags</strong>) will likely be moved.</p>
			<p><strong>IMPORTANT:</strong> Once you click the update button below, there's no going back. Chances of anything going wrong are slim, and since you've already backed up your database, there's nothing to be worried about. :-) This may take a while depending on how many ads you have. Please only click the button once.</p>

			<form action="admin.php?page=settings" id="msgForm" method="post">
				<p class="submit btop">
					<input type="submit" value="Migrate My Ads" name="convert" onclick="return confirmUpdate();" />
				</p>
				<input type="hidden" value="convertToCustomPostType" name="submitted" />
			</form>

			<p><small><?php _e('Note: This message will not disappear until you have upgraded your database.', 'appthemes'); ?></small></p>

		</div>

	<script type="text/javascript">
        /* <![CDATA[ */
            function confirmUpdate() { return confirm("Are you sure you wish to run the ClassiPress upgrade script? Promise you already backed up your database? It's better to be safe than sorry! :-)"); }
        /* ]]> */
    </script>
<?php
	endif;
}

// only show this upgrade message if using older version
if (get_option('cp_version_old') < '3.0.5')
    add_action('admin_notices', 'cp_upgrade_nag', 3);

?>
<?php
/**
 * ClassiPress core theme functions
 * This file is the backbone and includes all the core functions
 * Modifying this will void your warranty and could cause
 * problems with your instance of CP. Proceed at your own risk!
 *
 *
 * @version 3.0.5.2
 * @author AppThemes
 * @package ClassiPress
 * @copyright 2010 all rights reserved
 *
 */

// global variables - DO NOT CHANGE
$app_theme = 'ClassiPress';
$app_abbr = 'cp';
$app_version = '3.0.5.2';
$app_edition = 'Professional Edition';

// legacy classipress path variables
$upload_dir = wp_upload_dir();
define('UPLOADS_FOLDER', trailingslashit('classipress'));
define('CP_UPLOAD_DIR', trailingslashit($upload_dir['basedir']) . UPLOADS_FOLDER);
define('TIMTHUMB', get_bloginfo('template_url').'/includes/timthumb.php');

// activate support for .mo localization files
load_theme_textdomain('appthemes');

// define rss feed urls
$app_rss_feed = 'http://feeds2.feedburner.com/appthemes';
$app_twitter_rss_feed = 'http://twitter.com/statuses/user_timeline/appthemes.rss';
$app_forum_rss_feed = 'http://appthemes.com/forum/external.php?type=RSS2';

// define the db tables we use
$app_db_tables = array($app_abbr.'_ad_forms', $app_abbr.'_ad_meta', $app_abbr.'_ad_fields', $app_abbr.'_ad_pop_daily', $app_abbr.'_ad_pop_total' , $app_abbr.'_ad_packs', $app_abbr.'_order_info');

// define the transients we use
$app_transients = array($app_abbr.'_cat_menu');


// set global path variables
define('CP_DASHBOARD_URL', get_bloginfo('url').'/'.get_option($app_abbr.'_dashboard_url').'/');
define('CP_PROFILE_URL', get_bloginfo('url').'/'.get_option($app_abbr.'_profile_url').'/');
define('CP_EDIT_URL', get_bloginfo('url').'/'.get_option($app_abbr.'_edit_item_url').'/');
define('CP_ADD_NEW_URL', get_bloginfo('url').'/'.get_option($app_abbr.'_add_new_url').'/');
define('CP_ADD_NEW_CONFIRM_URL', get_bloginfo('url').'/'.get_option($app_abbr.'_add_new_confirm_url').'/');
// define('CP_AUTHOR_PATH', get_bloginfo('url').'/'.get_option($app_abbr.'_author_url').'/'); // deprecated since 3.0.5
// define('CP_BLOG_URL', cp_detect_blog_path()); // deprecated since 3.0.5
define('FAVICON', get_bloginfo('template_directory').'/images/favicon.ico');
define('THE_POSITION', 3);

// include all the core files
include_once(TEMPLATEPATH . '/includes/theme-cron.php');
include_once(TEMPLATEPATH . '/includes/theme-enqueue.php');
include_once(TEMPLATEPATH . '/includes/appthemes-functions.php');
include_once(TEMPLATEPATH . '/includes/theme-widgets.php');
include_once(TEMPLATEPATH . '/includes/theme-sidebars.php');
include_once(TEMPLATEPATH . '/includes/theme-comments.php');
include_once(TEMPLATEPATH . '/includes/theme-profile.php');
include_once(TEMPLATEPATH . '/includes/theme-security.php');
include_once(TEMPLATEPATH . '/includes/theme-footer.php');
include_once(TEMPLATEPATH . '/includes/theme-header.php');
include_once(TEMPLATEPATH . '/includes/theme-emails.php');
include_once(TEMPLATEPATH . '/includes/theme-stats.php');

// include the new custom post type and taxonomy declarations.
// must be included on all pages to work with site functions
include_once(TEMPLATEPATH . '/includes/admin/admin-post-types.php');


// front-end includes
if (!is_admin()) :
    include_once(TEMPLATEPATH.'/includes/theme-login.php');
    include_once(TEMPLATEPATH.'/includes/forms/login/login-form.php');
    include_once(TEMPLATEPATH.'/includes/forms/login/login-process.php');
    include_once(TEMPLATEPATH.'/includes/forms/register/register-form.php');
    include_once(TEMPLATEPATH.'/includes/forms/register/register-process.php');
    include_once(TEMPLATEPATH.'/includes/forms/forgot-password/forgot-password-form.php');
endif;

// admin-only functions
if (is_admin()) :
    include_once(TEMPLATEPATH . '/includes/admin/admin-enqueue.php');
    include_once(TEMPLATEPATH . '/includes/admin/admin-options.php');
    include_once(TEMPLATEPATH . '/includes/admin/install-script.php');
endif;


if (file_exists(TEMPLATEPATH . '/includes/gateways/paypal/ipn.php'))
    require_once (TEMPLATEPATH . '/includes/gateways/paypal/ipn.php');

	
// add AJAX functions
add_action( 'wp_ajax_nopriv_ajax-tag-search-front', 'cp_suggest' );
add_action( 'wp_ajax_ajax-tag-search-front', 'cp_suggest' );

// display the login message in the header
if (!function_exists('cp_login_head')) {
    function cp_login_head() {
        if (is_user_logged_in()) :
			global $current_user;
			get_currentuserinfo();
			?>
			<?php _e('Welcome,','appthemes'); ?> <strong><?php echo $current_user->user_login; ?></strong> [ <a href="<?php echo CP_DASHBOARD_URL ?>"><?php _e('My Dashboard','appthemes'); ?></a> | <a href="<?php echo wp_logout_url(); ?>"><?php _e('Log out','appthemes'); ?></a> ]&nbsp;
		<?php else : ?>
			<?php _e('Welcome,','appthemes'); ?> <strong><?php _e('visitor!','appthemes'); ?></strong> [ <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?action=register"><?php _e('Register','appthemes'); ?></a> | <a href="<?php echo get_option('siteurl'); ?>/wp-login.php"><?php _e('Login','appthemes'); ?></a> ]&nbsp;
        <?php endif;

    }
}

// dont show any posts that are sub cats of the blog cat
function cp_post_in_desc_cat($cats, $_post = null) {
    foreach ((array) $cats as $cat) {
        // get_term_children() accepts integer ID only
        $descendants = get_term_children((int) $cat, 'category');
        if ($descendants && in_category($descendants, $_post))
            return true;
    }
    return false;
}

// get the blog category id
function cp_get_blog_catid() {
    $blogcatid = get_option('cp_blog_cat');

    // set to default cat id if option is blank
    if (empty($blogcatid))
        $blogcatid = 1;

    return $blogcatid;
}

// get the blog id and all blog sub cat ids so we can filter them out of ads
function cp_get_blog_cat_ids() {
    $catid = get_option('cp_blog_cat');
    $output = array();

    // make sure the blog cat id is set to something
    if(!($catid))
        $catid = 1;

    // put the catid into an array
    $output[] = $catid;

    // get all the sub cats of catid and also put them into the array
    $descendants = get_term_children((int) $catid, 'category');

    foreach($descendants as $key => $value) {
        $output[] = $value;
    }

    // spit out the array and separate each value with a comma
    $allcats = trim(join(',', $output));

    return $allcats;
}

// same function as above but give us the ids in an array. needed on home page for filtering out blog posts
function cp_get_blog_cat_ids_array() {
    $catid = get_option('cp_blog_cat');
    $output = array();

    // make sure the blog cat id is set to something
    if(!($catid))
        $catid = 1;

    // put the catid into an array
    $output[] = $catid;

    // get all the sub cats of catid and also put them into the array
    $descendants = get_term_children((int) $catid, 'category');

    foreach($descendants as $key => $value) {
        $output[] = $value;
    }

    return $output;
}


// assemble the blog path
// deprecated since 3.0.5
function cp_detect_blog_path() {
    $blogcatid = get_option('cp_blog_cat');

    if (!empty($blogcatid))
        $blogpath = get_category_link(get_option('cp_blog_cat'));
    else // since the cat id field is blank, we need to guess the path
        $blogpath = cp_cat_base().'/blog/';

    return $blogpath;
}


// find out if the category base has been set. If not, use the default of "category"
// deprecated since 3.0.5
function cp_cat_base() {
    if ((appthemes_clean(get_option('category_base')) == ''))
        $cat_base = trailingslashit(get_bloginfo('url')) . 'category';
    else
        $cat_base = trailingslashit(get_bloginfo('url')) . get_option('category_base');

   return $cat_base;
}


// processes the entire ad thumbnail logic within the loop
if (!function_exists('cp_ad_loop_thumbnail')) {
	function cp_ad_loop_thumbnail() {
		global $post;

		// go see if any images are associated with the ad
		$images = get_children( array('post_parent' => $post->ID, 'post_status' => 'inherit', 'numberposts' => 1, 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'ID') );

		if ($images) {

			// move over bacon
			$image = array_shift($images);

			// get 75x75 v3.0.5+ image size
			$adthumbarray = wp_get_attachment_image($image->ID, 'ad-thumb');

			// grab the large image for onhover preview
			$adlargearray = wp_get_attachment_image_src($image->ID, 'large');
			$img_large_url_raw = $adlargearray[0];

			// must be a v3.0.5+ created ad
			if($adthumbarray) {
				echo '<a href="'. get_permalink() .'" title="'. the_title_attribute('echo=0') .'" class="preview" rel="'.$img_large_url_raw.'">'.$adthumbarray.'</a>';

			// maybe a v3.0 legacy ad
			} else {
				$adthumblegarray = wp_get_attachment_image_src($image->ID, 'thumbnail');
				$img_thumbleg_url_raw = $adthumblegarray[0];
				echo '<a href="'. get_permalink() .'" title="'. the_title_attribute('echo=0') .'" class="preview" rel="'.$img_large_url_raw.'">'.$adthumblegarray.'</a>';
			}

		// wow, must be really old v2.9.3 or earlier so timthumb time
		} elseif(get_post_meta($post->ID, 'images', true)) {

			cp_ad_loop_thumbnail_legacy($post->ID, 75, 75);

		// no image so return the placeholder thumbnail
		} else {
			echo '<a href="'. get_permalink() .'" title="'. the_title_attribute('echo=0') .'"><img class="attachment-medium" alt="" title="" src="'. get_bloginfo('template_url') .'/images/no-thumb-75.jpg" /></a>';
		}

	}
}


// processes the entire ad thumbnail logic for featured ads
if (!function_exists('cp_ad_featured_thumbnail')) {
	function cp_ad_featured_thumbnail() {
		global $post;

		// go see if any images are associated with the ad
		$images = get_children( array('post_parent' => $post->ID, 'post_status' => 'inherit', 'numberposts' => 1, 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'ID') );

		if ($images) {

			// move over bacon
			$image = array_shift($images);

			// get 50x50 v3.0.5+ image size
			$adthumbarray = wp_get_attachment_image($image->ID, 'sidebar-thumbnail');

			// grab the large image for onhover preview
			$adlargearray = wp_get_attachment_image_src($image->ID, 'large');
			$img_large_url_raw = $adlargearray[0];

			// must be a v3.0.5+ created ad
			if($adthumbarray) {
				echo '<a href="'. get_permalink() .'" title="'. the_title_attribute('echo=0') .'" class="preview" rel="'.$img_large_url_raw.'">'.$adthumbarray.'</a>';

			// maybe a v3.0 legacy ad
			} else {
				$adthumblegarray = wp_get_attachment_image_src($image->ID, 'thumbnail');
				$img_thumbleg_url_raw = $adthumblegarray[0];
				echo '<a href="'. get_permalink() .'" title="'. the_title_attribute('echo=0') .'" class="preview" rel="'.$img_large_url_raw.'">'.$adthumblegarray.'</a>';
			}

		// wow, must be really old v2.9.3 or earlier so timthumb time
		} elseif(get_post_meta($post->ID, 'images', true)) {

			cp_ad_loop_thumbnail_legacy($post->ID, 50, 50, 'attachment-sidebar-thumbnail');
			//cp_single_image_legacy

		// no image so return the placeholder thumbnail
		} else {
			echo '<a href="'. get_permalink() .'" title="'. the_title_attribute('echo=0') .'"><img class="attachment-sidebar-thumbnail" alt="" title="" src="'. get_bloginfo('template_url') .'/images/no-thumb-sm.jpg" /></a>';
		}

	}
}



// display the stats counter within the loop
// @since 3.0.5.2
function appthemes_get_stats($post_id) {
	global $wpdb;

	$daily_views = get_post_meta($post_id, 'cp_daily_count', true);
	$total_views = get_post_meta($post_id, 'cp_total_count', true);

	if(!empty($total_views) && (!empty($daily_views)))
		echo number_format($total_views) . '&nbsp;' . __('total views', 'appthemes'). ',&nbsp;' . number_format($daily_views) . '&nbsp;' . __('today', 'appthemes');
	else
		echo __('no views yet', 'appthemes');
}


// display all the custom fields on the single ad page, by default they are placed in the list area
if (!function_exists('cp_get_ad_details')) {
    function cp_get_ad_details($postid, $catid, $locationOption = 'list') {
        global $wpdb;
        //$all_custom_fields = get_post_custom($post->ID);
        // see if there's a custom form first based on catid.
        $fid = cp_get_form_id($catid);

        // if there's no form id it must mean the default form is being used
        if(!($fid)) {

			// get all the custom field labels so we can match the field_name up against the post_meta keys
			$sql = $wpdb->prepare("SELECT field_label, field_name, field_type FROM ". $wpdb->prefix . "cp_ad_fields");

        } else {

            // now we should have the formid so show the form layout based on the category selected
            $sql = $wpdb->prepare("SELECT f.field_label, f.field_name, f.field_type, m.field_pos "
                     . "FROM ". $wpdb->prefix . "cp_ad_fields f "
                     . "INNER JOIN ". $wpdb->prefix . "cp_ad_meta m "
                     . "ON f.field_id = m.field_id "
                     . "WHERE m.form_id = '$fid' "
                     . "ORDER BY m.field_pos asc");

        }

        $results = $wpdb->get_results($sql);

        if($results) {
            if($locationOption == 'list') {
                    foreach ($results as $result) :
                        // now grab all ad fields and print out the field label and value
                        $post_meta_val = get_post_meta($postid, $result->field_name, true);
                        if (!empty($post_meta_val))
                            if($result->field_name != 'cp_price' && $result->field_type != "text area")
                                echo '<li id="'. $result->field_name .'"><span>' . $result->field_label . ':</span> ' . appthemes_make_clickable($post_meta_val) .'</li>'; // make_clickable is a WP function that auto hyperlinks urls

                    endforeach;
                }
                elseif($locationOption == 'content')
                {
                    foreach ($results as $result) :
                        // now grab all ad fields and print out the field label and value
                        $post_meta_val = get_post_meta($postid, $result->field_name, true);
                        if (!empty($post_meta_val))
                            if($result->field_name != 'cp_price' && $result->field_type == 'text area')
                                echo '<div id="'. $result->field_name .'" class="custom-text-area dotted"><h3>' . $result->field_label . '</h3>' . appthemes_make_clickable($post_meta_val) .'</div>'; // make_clickable is a WP function that auto hyperlinks urls

                    endforeach;
                }
                else
                {
                        // uncomment for debugging
                        // echo 'Location Option Set: ' . $locationOption;
                }

        } else {

          echo __('No ad details found.', 'appthemes');

        }
    }
}


// give us the custom form id based on category id passed in
// this is used on the single-default.php page to display the ad fields
function cp_get_form_id($catid) {
    global $wpdb;
    $fid = ''; // set to nothing to make WP notice happy

    // we first need to see if this ad is using a custom form
    // so lets search for a catid match and return the id if found
    $sql = "SELECT ID, form_cats FROM ". $wpdb->prefix . "cp_ad_forms WHERE form_status = 'active'";

    $results = $wpdb->get_results($sql);

    if($results) {

        foreach ($results as $result) :

            // put the form_cats into an array
            $catarray = unserialize($result->form_cats);

            // now search the array for the ad catid
            if (in_array($catid, $catarray))
                $fid = $result->ID; // when there's a catid match, grab the form id

        endforeach;

        // kick back the form id
        return $fid;

    }

}


// get the first medium image associated to the ad
// used on the home page, search, category, etc
// deprecated since 3.0.5.2
if (!function_exists('cp_get_image')) {
    function cp_get_image($post_id = '', $size = 'medium', $num = 1) {
        $images = get_posts(array('post_type' => 'attachment', 'numberposts' => $num, 'post_status' => null, 'post_parent' => $post_id, 'order' => 'ASC', 'orderby' => 'ID'));
        if ($images) {
            foreach ($images as $image) {
                $img_check = wp_get_attachment_image($image->ID, $size, $icon = false);
				// legacy since 3.0.5 which now includes image alt text editing
                //$post_title = get_the_title($post_id); // grab the post title so we can include in alt and title for SEO
                //$img_check = preg_replace('/title=\"(.*?)\"/','title="'.$post_title.'"', $img_check);
                //$img_check = preg_replace('/alt=\"(.*?)\"/','alt="'.$post_title.'"', $img_check);
            }
        } else {
           // show the placeholder image
           if(get_option('cp_ad_images') == 'yes') { $img_check = '<img class="attachment-medium" alt="" title="" src="'. get_bloginfo('template_url') .'/images/no-thumb-75.jpg" />'; }
        }
        echo $img_check;
    }
}


// get the main image associated to the ad used on the single page
if (!function_exists('cp_get_image_url')) {
	function cp_get_image_url() {
		global $post, $wpdb;

		// go see if any images are associated with the ad
		$images = get_children( array('post_parent' => $post->ID, 'post_status' => 'inherit', 'numberposts' => 1, 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'ID') );

		if ($images) {

			// move over bacon
			$image = array_shift($images);

			// see if this v3.0.5+ image size exists
			//$adthumbarray = wp_get_attachment_image($image->ID, 'medium');
			$adthumbarray = wp_get_attachment_image_src($image->ID, 'medium');
			$img_medium_url_raw = $adthumbarray[0];

			// grab the large image for onhover preview
			$adlargearray = wp_get_attachment_image_src($image->ID, 'large');
			$img_large_url_raw = $adlargearray[0];

			// must be a v3.0.5+ created ad
			if($adthumbarray)
				echo '<a href="'. $img_large_url_raw .'" class="img-main" rel="colorbox"><img src="'. $img_medium_url_raw .'" title="'. the_title_attribute('echo=0') .'" alt="'. the_title_attribute('echo=0') .'"  /></a>';


		// wow, must be really old v2.9.3 or earlier
		} elseif(get_post_meta($post->ID, 'images', true)) {

			cp_single_image_legacy($post->ID, get_option('medium_size_w'), get_option('medium_size_h'));

		// no image so return the placeholder thumbnail
		} else {
			echo '<img class="attachment-medium" alt="" title="" src="'. get_bloginfo('template_url') .'/images/no-thumb.jpg" />';
		}

	}
}


// get the image associated to the ad used in the loop-ad for hover previewing
if (!function_exists('cp_get_image_url_raw')) {
    function cp_get_image_url_raw($post_id = '', $size = 'medium', $class = '', $num = 1) {
        $images = get_posts(array('post_type' => 'attachment', 'numberposts' => $num, 'post_status' => null, 'post_parent' => $post_id, 'order' => 'ASC', 'orderby' => 'ID'));
        if ($images) {
            foreach ($images as $image) {
              $iarray = wp_get_attachment_image_src($image->ID, $size, $icon = false);
              $img_url_raw = $iarray[0];
            }
        } else {
            //if(get_option('cp_ad_images') == 'yes') {$img_url_raw = get_bloginfo('template_url') .'/images/no-thumb.jpg"'; }
        }
        return $img_url_raw;
    }
}


// get the image associated to the ad used on the home page
if (!function_exists('cp_get_image_url_feat')) {
    function cp_get_image_url_feat($post_id = '', $size = 'medium', $class = '', $num = 1) {
        $images = get_posts(array('post_type' => 'attachment', 'numberposts' => $num, 'post_status' => null, 'post_parent' => $post_id, 'order' => 'ASC', 'orderby' => 'ID'));
        if ($images) {
            foreach ($images as $image) {
				$alt = get_post_meta($image->ID, '_wp_attachment_image_alt', true);
                $iarray = wp_get_attachment_image_src($image->ID, $size, $icon = false);
                $img_check = '<img class="'.$class.'" src="'.$iarray[0].'" width="'.$iarray[1].'" height="'.$iarray[2].'" alt="'.$alt.'" title="'.$alt.'" />';
            }
        } else {
            if(get_option('cp_ad_images') == 'yes') { $img_check = '<img class="preview" alt="" title="" src="'. get_bloginfo('template_url') .'/images/no-thumb-sm.jpg" />'; }
            //cp_single_image_legacy($post_id, get_option('thumbnail_size_w'), get_option('thumbnail_size_h'), 'captify', true);
        }
        echo $img_check;
    }
}


// get all the small images for the ad and colorbox href
// important and used on the single page
if (!function_exists('cp_get_image_url_single')) {
    function cp_get_image_url_single($post_id = '', $size = 'medium', $title = '', $num = 1) {
        $images = get_posts(array('post_type' => 'attachment', 'numberposts' => $num, 'post_status' => null, 'post_parent' => $post_id, 'order' => 'ASC', 'orderby' => 'ID'));
        
		// remove the first image since it's already being shown as the main one
		$images = array_slice($images,1,2);

		if ($images) {
            $i=1;
            foreach ($images as $image) {
				$alt = get_post_meta($image->ID, '_wp_attachment_image_alt', true);
                $iarray = wp_get_attachment_image_src($image->ID, $size, $icon = false);
                $iarraylg = wp_get_attachment_image_src($image->ID, 'large', $icon = false);
                if(i==1) $mainpicID = 'id="mainthumb"'; else $mainpicID = '';
                echo '<a href="'.$iarraylg[0].'" id="thumb'.$i.'" class="ad-gallery" rel="colorbox" title="'.$title.' - '.__('Image ', 'appthemes').$i.'"><img src="'.$iarray[0].'" alt="'.$alt.'" title="'.$alt.'" width="'.$iarray[1].'" height="'.$iarray[2].'" /></a>';
                $i++;
            }
        }
    }
}


// sets the thumbnail pic on the WP admin post
function cp_set_ad_thumbnail($post_id, $thumbnail_id) {
    $thumbnail_html = wp_get_attachment_image($thumbnail_id, 'thumbnail');
    if (!empty($thumbnail_html)) {
        update_post_meta($post_id, '_thumbnail_id', $thumbnail_id);
        die( _wp_post_thumbnail_html($thumbnail_id));
    }
}


// deletes the thumbnail pic on the WP admin post
function cp_delete_ad_thumbnail($post_id) {
    delete_post_meta($post_id, '_thumbnail_id');
    die(_wp_post_thumbnail_html());
}


// gets just the first raw image url
function cp_get_image_url_OLD($postID, $num=1, $order='ASC', $orderby='menu_order', $mime='image') {
    $images = get_posts(array('post_type' => 'attachment','numberposts' => $num,'post_status' => null,'order' => $order,'orderby' => $orderby,'post_mime_type' => $mime,'post_parent' => $postID));
    if ($images) {
        foreach ($images as $image) {
            $single_url = wp_get_attachment_url($image->ID, false);
        }
    }
    echo $single_url;
}


// used for most image resizing for legacy 2.9.x
// soon to be deprecated
function cp_get_single_image($postID, $height=50, $width=50, $num=1, $order='ASC', $orderby='menu_order', $mime='image') {
    $attachments = get_posts(array('post_type' => 'attachment','numberposts' => $num,'post_status' => null,'order' => $order,'orderby' => $orderby,'post_mime_type' => $mime,'post_parent' => $postID));
    if ($attachments) {
        foreach ($attachments as $attachment) {
            $single_img = TIMTHUMB.'?src='.wp_get_attachment_url($attachment->ID, false).'&amp;h='.$height.'&amp;w='.$width.'&amp;zc=0"';
        }
    } else {
        $single_img = TIMTHUMB.'?src=/images/no-image.png&amp;h='.$height.'&amp;w='.$width.'&amp;zc=0"';
    }
    echo $single_img;
}



// used for getting the single ad image thumbnails
if (!function_exists('cp_get_image_thumbs')) {
    function cp_get_image_thumbs($postID, $height, $width, $lheight, $lwidth, $num=-1, $order='ASC', $orderby='menu_order', $mime='image') {
        $attachments = get_posts(array('post_type' => 'attachment','numberposts' => $num,'post_status' => null,'order' => $order,'orderby' => $orderby,'post_mime_type' => $mime,'post_parent' => $postID));
        $zc = get_option('cp_tim_thumb_zc'); // get the zoom/crop value
        $i = 1;

        if ($attachments) {
            foreach ($attachments as $attachment) {
                $single_img_thumbs .= '<span id="thumb-wrap"><a rel="group" href="#" title="image '.$i.'" tpath="'.wp_get_attachment_url($attachment->ID, false).'" lpath="'.TIMTHUMB.'?src='.wp_get_attachment_url($attachment->ID, false).'&amp;h='.$lheight.'&amp;w='.$lwidth.'&amp;zc='.$zc.'"><img src="'.TIMTHUMB.'?src='.wp_get_attachment_url($attachment->ID, false).'&amp;h='.$height.'&amp;w='.$width.'&amp;zc='.$zc.'" class="single-thumb" width="'.$width.'" height="'.$height.'" alt="'.get_the_title().' ('.__('image', 'appthemes').'&nbsp;'.$i.')" title="'.get_the_title().' ('.__('image', 'appthemes').'&nbsp;'.$i.')" /></a></span>'."\n";
                $i++;
            }
        }
        echo $single_img_thumbs;
    }
}


// legacy function used for ads created with CP 2.9.3 and earlier
// take first image from custom field and return it
function cp_ad_loop_thumbnail_legacy($postID, $width, $height, $class = 'attachment-medium', $blank = true) {
	global $post;

    $images = get_post_meta($postID, 'images', true); // grab the images from the post custom field
    $zc = get_option('cp_tim_thumb_zc'); // get the zoom/crop value
    $single_img = '';

    if (empty($images)) {
        if ($blank == true) // don't show the placeholder thumbnail if we pass in false
            $single_img = '<img class="'.$class.'" src="'.TIMTHUMB.'?src=/images/no-thumb-75.jpg&amp;h='.$height.'&amp;w='.$width.'&amp;zc='.$zc.'" border="0" width="'.$width.'" height="'.$height.'" alt="" />';
    } else {
        if (strstr($images, ',')) {
            $matches = explode(',', $images);
            $img_single = $matches[0]; // find the first image
            $single_img = '<a href="'. get_permalink() .'" title="'. the_title_attribute('echo=0') .'" class="preview" rel="'.$img_single.'"><img class="'.$class.'" src="'.TIMTHUMB.'?src='.$img_single.'&amp;h='.$height.'&amp;w='.$width.'&amp;zc='.$zc.'" border="0" width="'.$width.'" height="'.$height.'" alt="" /></a>';
        } else {
            if ($blank == true)
                $single_img = '<img class="'.$class.'" src="'.TIMTHUMB.'?src=/images/no-thumb-75.jpg&amp;h='.$height.'&amp;w='.$width.'&amp;zc='.$zc.'" border="0" width="'.$width.'" height="'.$height.'" alt="" />';
        }
    }
    echo $single_img; //return the results
}



// legacy function used for ads created with CP 2.9.3 and earlier
// take first image from custom field and return it
function cp_single_image_legacy($postID, $width, $height, $class = 'attachment-medium', $blank = true) {
	global $post;

    $images = get_post_meta($postID, 'images', true); // grab the images from the post custom field
    $zc = get_option('cp_tim_thumb_zc'); // get the zoom/crop value
    $single_img = '';

    if (empty($images)) {
        if ($blank == true) // don't show the placeholder thumbnail if we pass in false
            $single_img = '<img class="'.$class.'" src="'.TIMTHUMB.'?src=/images/no-thumb-75.jpg&amp;h='.$height.'&amp;w='.$width.'&amp;zc='.$zc.'" border="0" width="'.$width.'" height="'.$height.'" alt="" />';
    } else {
        if (strstr($images, ',')) {
            $matches = explode(',', $images);
            $img_single = $matches[0]; // find the first image
            $single_img = '<a href="'. $img_single .'" title="'. the_title_attribute('echo=0') .'" class="img-main" rel="colorbox"><img id="mainImageLink" class="'.$class.'" src="'.TIMTHUMB.'?src='.$img_single.'&amp;h='.$height.'&amp;w='.$width.'&amp;zc='.$zc.'" border="0" width="'.$width.'" height="'.$height.'" alt="" /></a>';
        } else {
            if ($blank == true)
                $single_img = '<img class="'.$class.'" src="'.TIMTHUMB.'?src=/images/no-thumb-75.jpg&amp;h='.$height.'&amp;w='.$width.'&amp;zc='.$zc.'" border="0" width="'.$width.'" height="'.$height.'" alt="" />';
        }
    }
    echo $single_img; //return the results
}


// legacy function used for ads created with CP 2.9.3 and earlier
// takes all images from custom field and returns them on the single ad page
function cp_get_image_thumbs_legacy($postID, $width, $height, $title = '') {
    $images = get_post_meta($postID, 'images', true); //grab the images from the post custom field
    $zc = get_option('cp_tim_thumb_zc'); // get the zoom/crop value
    
    if ($images) {
        $i=1;

        // remove any blank space and trim off the last comma
        $images = explode(',', substr(trim($images),0,-1));

		// remove the first image since it's already being shown as the main one
		$images = array_slice($images,1,2);
        
        foreach ($images as $image) {
          echo '<a href="'.$image.'" class="ad-gallery" rel="colorbox" title="'.$title.' - '.__('Image ', 'appthemes').$i.'"><img src="'.TIMTHUMB.'?src='.$image.'&amp;h='.$height.'&amp;w='.$width.'&amp;zc='.$zc.'" alt="" width="'.$width.'" height="'.$height.'" /></a>';
        $i++;
        }
    }
}


// legacy function used on CP 2.9.3 and earlier
// take first image from custom field and use for related ad function
function cp_single_image_raw_legacy($postID, $width, $height) {
    $images = get_post_meta($postID, 'images', true); //grab the images from the post custom field
    $zc = get_option('cp_tim_thumb_zc'); // get the zoom/crop value
    $single_img = '';

    if (empty($images)) {
        $single_img = TIMTHUMB.'?src=/images/no-thumb.jpg&amp;h='.$height.'&amp;w='.$width.'&amp;zc='.$zc.'';
    } else {
        if (strstr($images, ',')) {
            $matches = explode(',', $images);
            $img_single = $matches[0]; // find the first image
            $single_img = trailingslashit(get_bloginfo('url')).trailingslashit(get_option('upload_path')).UPLOADS_FOLDER.$img_single;
        } else {
            $single_img = trailingslashit(get_bloginfo('url')).trailingslashit(get_option('upload_path')).UPLOADS_FOLDER.$images;
        }
        $single_img = TIMTHUMB.'?src='.$single_img.'&amp;h='.$height.'&amp;w='.$width.'&amp;zc='.$zc.'';
    }
    return $single_img; //return the results
}


// get the uploaded file extension and make sure it's an image
function cp_file_is_image($path) {
    $info = @getimagesize($path);
    if (empty($info))
        $result = false;
    elseif (!in_array($info[2], array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG)))
        $result = false;
    else
        $result = true;

    return apply_filters('cp_file_is_image', $result, $path);
}


// legacy function used on CP 2.9.3 and earlier
// get the ad price and position the currency symbol
function cp_get_price_legacy($postid) {

    if(get_post_meta($postid, 'price', true)) {
        $price_out = get_post_meta($postid, 'price', true);

        // uncomment the line below to change price format
        //$price_out = number_format($price_out, 2, ',', '.');

        if(get_option('cp_curr_symbol_pos') == 'right')
            $price_out = $price_out . get_option('cp_curr_symbol');
        else
            $price_out = get_option('cp_curr_symbol') . $price_out;
    } else {	
        $price_out = '&nbsp;';		
    }
	
    echo $price_out;

}


// get the ad price and position the currency symbol
if (!function_exists('cp_get_price')) {
    function cp_get_price($postid) {

        if(get_post_meta($postid, 'cp_price', true)) {
            $price_out = get_post_meta($postid, 'cp_price', true);

            // uncomment the line below to change price format
            //$price_out = number_format($price_out, 2, '.', ',');

            $price_out = cp_pos_currency($price_out);

        } else {
            if( get_option('cp_force_zeroprice') == 'yes' )
                $price_out = cp_pos_currency(0);
            else
                $price_out = '&nbsp;';
        }

        echo $price_out;
    }
}


// pass in the price and get the position of the currency symbol
function cp_pos_price($numout) {
    $numout = cp_pos_currency($numout);	
    echo $numout;
}

// figure out the position of the currency symbol and return it with the price
function cp_pos_currency($price_out) {
    if (get_option('cp_curr_symbol_pos') == 'left')
        $price_out = get_option('cp_curr_symbol') . $price_out;
    elseif (get_option('cp_curr_symbol_pos') == 'left_space')
        $price_out = get_option('cp_curr_symbol') . '&nbsp;' . $price_out;
    elseif (get_option('cp_curr_symbol_pos') == 'right')
        $price_out = $price_out . get_option('cp_curr_symbol');
    else
        $price_out = $price_out . '&nbsp;' . get_option('cp_curr_symbol');

    return $price_out;	
}


// on ad submission form, check images for valid file size and type
function cp_validate_image() {
    $error_msg  = array();
    $max_size = (get_option('cp_max_image_size') * 1024); // 1024 K = 1 MB. convert into bytes so we can compare file size to max size. 1048576 bytes = 1MB.

    while(list($key,$value) = each($_FILES['image']['name'])) {
        $value = strtolower($value); // added for 3.0.1 to force image names to lowercase. some systems throw an error otherwise
        if(!empty($value)) {
            if ($max_size < $_FILES['image']['size'][$key]) {
                $size_diff = number_format(($_FILES['image']['size'][$key] - $max_size)/1024);
                $max_size_fmt = number_format(get_option('cp_max_image_size'));
                $error_msg[] = '<strong>'.$_FILES['image']['name'][$key].'</strong> '. sprintf( __('exceeds the %s KB limit by %s KB. Please go back and upload a smaller image.', 'appthemes'), $max_size_fmt, $size_diff );
            }
            elseif (!cp_file_is_image($_FILES['image']['tmp_name'][$key])) {
                $error_msg[] = '<strong>'.$_FILES['image']['name'][$key].'</strong> '. __('is not a valid image type (.gif, .jpg, .png). Please go back and upload a different image.', 'appthemes');
            }
        }
    }
    return $error_msg;
}


// process each image that's being uploaded
function cp_process_new_image() {
    global $wpdb;
    $postvals = '';

    for($i=0; $i < count($_FILES['image']['tmp_name']);$i++) {
        if (!empty($_FILES['image']['tmp_name'][$i])) {
            // rename the image to a random number to prevent junk image names from coming in
            $renamed = mt_rand(1000,1000000).".".appthemes_find_ext($_FILES['image']['name'][$i]);

            //Hack since WP can't handle multiple uploads as of 2.8.5
            $upload = array( 'name' => $renamed,'type' => $_FILES['image']['type'][$i],'tmp_name' => $_FILES['image']['tmp_name'][$i],'error' => $_FILES['image']['error'][$i],'size' => $_FILES['image']['size'][$i] );

            // need to set this in order to send to WP media
            $overrides = array('test_form' => false);

            // check and make sure the image has a valid extension and then upload it
            $file = cp_image_upload($upload);

            if ($file) // put all these keys into an array and session so we can associate the image to the post after generating the post id
                $postvals['attachment'][$i] = array( 'post_title' => $renamed,'post_content' => '','post_excerpt' => '','post_mime_type' => $file['type'],'guid' => $file['url'], 'file' => $file['file'] );
        }
    }
    return $postvals;
}


// this ties the uploaded files to the correct ad post and creates the multiple image sizes.
function cp_associate_images($post_id,$file) {
    for($i=0; $i < count($file);$i++) {
        $attachment = array( 'post_title' => $file[$i]['post_title'],'post_content' => $file[$i]['post_content'],'post_excerpt' => $file[$i]['post_excerpt'],'post_mime_type' => $file[$i]['post_mime_type'],'guid' => $file[$i]['guid'] );
        $attach_id = wp_insert_attachment($attachment, $file[$i]['file'], $post_id);

        // create multiple sizes of the uploaded image via WP controls
        wp_update_attachment_metadata($attach_id, wp_generate_attachment_metadata($attach_id, $file[$i]['file']));

        // this only does a specific resize.
        // image_make_intermediate_size($file, $width, $height, $crop=false)
        // $crop Optional, default is false. Whether to crop image to specified height and width or resize.
        //wp_update_attachment_metadata($attach_id, image_make_intermediate_size($file[$i]['file'], 50, 50, true));
        //wp_update_attachment_metadata($attach_id, image_make_intermediate_size($file[$i]['file'], 25, 25, true));
    }
}


// get all the images associated to the ad and display the
// thumbnail with checkboxes for deleting them
// used on the ad edit page
if (!function_exists('cp_get_ad_images')) {
    function cp_get_ad_images($ad_id) {
        $args = array('post_type' => 'attachment', 'numberposts' => -1, 'post_status' => null, 'post_parent' => $ad_id, 'order' => 'ASC', 'orderby' => 'ID');

        // get all the images associated to this ad
        $images = get_posts($args);

        // print_r($images); // for debugging

        // get the total number of images already on this ad
        // we need it to figure out how many upload fields to show
        $imagecount = count($images);

        // make sure we have images associated to the ad
        if ($images) :

            $i = 1;
            foreach ($images as $image) :

				// go get the width and height fields since they are stored in meta data
				$meta = wp_get_attachment_metadata( $image->ID );
				if (is_array($meta) && array_key_exists('width', $meta) && array_key_exists('height', $meta))
					$media_dims = "<span id='media-dims-$post->ID'>{$meta['width']}&nbsp;&times;&nbsp;{$meta['height']}</span> ";
            ?>
				<li class="images">
					<label><?php _e('Image', 'appthemes'); ?> <?php echo $i ?>:</label>

					<div class="thumb-wrap-edit">
						<?php echo cp_get_attachment_link($image->ID); ?>
					</div>

					<div class="image-meta">
						<p class="image-delete"><input class="checkbox" type="checkbox" name="image[]" value="<?php echo $image->ID; ?>">&nbsp;<?php _e('Delete Image', 'appthemes') ?></p>
						<p class="image-meta"><strong><?php _e('Upload Date:', 'appthemes') ?></strong> <?php echo mysql2date( get_option('date_format'), $image->post_date); ?></p>
						<p class="image-meta"><strong><?php _e('File Info:', 'appthemes') ?></strong> <?php echo $media_dims ?> <?php echo $image->post_mime_type; ?></p>
					</div>
					
					<div class="clr"></div>

					<?php // get the alt text and print out the field
						 $alt = get_post_meta($image->ID, '_wp_attachment_image_alt', true); ?>
					<p class="alt-text">
						<label><?php _e('Alt Text:','appthemes') ?></label>
						<input type="text" class="text" name="attachments[<?php echo $image->ID; ?>][image_alt]" id="image_alt" value="<?php if(count($alt)) echo attribute_escape(stripslashes($alt)); ?>" />
					</p>

					<div class="clr"></div>
				</li>
            <?php
            $i++;
			endforeach;

        endif;

        // returns a count of array keys so we know how many images currently
        // are being used with this ad. this value is needed for cp_ad_edit_image_input_fields()
        return $imagecount;
    }
}


// gets the image link for each ad. used in the edit-ads page template
function cp_get_attachment_link($id = 0, $size = 'thumbnail', $permalink = false, $icon = false, $text = false) {
	$id = intval($id);
	$_post = & get_post( $id );

	// print_r($_post);

	if ( ('attachment' != $_post->post_type) || !$url = wp_get_attachment_url($_post->ID) )
		return __('Missing Attachment', 'appthemes');

	if ( $permalink )
		$url = get_attachment_link($_post->ID);

	$post_title = esc_attr($_post->post_title);

	if ( $text ) {
		$link_text = esc_attr($text);
	} elseif ( ( is_int($size) && $size != 0 ) or ( is_string($size) && $size != 'none' ) or $size != false ) {
		$link_text = wp_get_attachment_image($id, $size, $icon);
	} else {
		$link_text = '';
	}

	if( trim($link_text) == '' )
		$link_text = $_post->post_title;

	return apply_filters( 'cp_get_attachment_link', "<a target='_blank' href='$url' alt='' class='ad-gallery' rel='colorbox' title='$post_title'>$link_text</a>", $id, $size, $permalink, $icon, $text, $alt );
}


// gives us a count of how many images are associated to an ad
function cp_count_ad_images($ad_id) {
    $args = array('post_type' => 'attachment', 'numberposts' => -1, 'post_status' => null, 'post_parent' => $ad_id, 'order' => 'ASC', 'orderby' => 'ID');

    // get all the images associated to this ad
    $images = get_posts($args);

    // get the total number of images already on this ad
    // we need it to figure out how many upload fields to show
    $imagecount = count($images);

    // returns a count of array keys so we know how many images currently
    // are being used with this ad.
    return $imagecount;
}


// calculates total number of image input upload boxes
// minus the number of existing images
function cp_ad_edit_image_input_fields($imagecount) {

    // get the max number of images allowed option
    $maximages = get_option('cp_num_images');

    // figure out how many image upload fields we need
    $imageboxes = ($maximages - $imagecount);

    // now loop through and print out the upload fields
    for($i=0; $i < $imageboxes;$i++) :
    ?>
        <li>
            <label><?php _e('Add Image','appthemes') ?>:</label>
            <input type="file" name="image[]" id="upload<?php echo $i; ?>" class="fileupload"  onchange="enableNextImage(this, <?php echo $i+1; ?>);" <?php if($i>0) echo 'disabled="disabled"' ?>'; >
            <div class="clr"></div>
        </li>
    <?php
    endfor;
    ?>
	
    <p class="small"><?php printf(__('You are allowed %s image(s) per ad.','appthemes'), $maximages) ?> <?php echo get_option('cp_max_image_size') ?><?php _e('KB max file size per image.','appthemes') ?> <?php _e('Check the box next to each image you wish to delete.','appthemes') ?></p>
    <div class="clr"></div>

<?php
}


// make sure it's an image file and then upload it
function cp_image_upload($upload) {
    if (cp_file_is_image($upload['tmp_name'])) {
        $overrides = array('test_form' => false);
        // move image to the WP defined upload directory and set correct permissions
        $file = wp_handle_upload($upload, $overrides);
    }
    return $file;
}


// delete the image from WordPress
function cp_delete_image() {
    foreach( (array) $_POST['image'] as $img_id_del ) {
        $img_del = & get_post($img_id_del);

        if ( $img_del->post_type == 'attachment' )
            if ( !wp_delete_attachment($img_id_del, true) )
                wp_die( __('Error in deleting the image.', 'appthemes') );
    }
}

// update the image alt and title text on edit ad page. since v3.0.5
function cp_update_alt_text() {
	foreach ($_POST['attachments'] as $attachment_id => $attachment) :
		if (isset($attachment['image_alt'])) {
			$image_alt = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);

			if ($image_alt != stripslashes($attachment['image_alt'])) {
				$image_alt = wp_strip_all_tags(stripslashes($attachment['image_alt']), true);				

				// update the image alt text for based on the id
				update_post_meta($attachment_id, '_wp_attachment_image_alt', addslashes($image_alt));

				// update the image title text. it's stored as a post title so it's different to update
				$post = array();
				$post['ID'] = $attachment_id;
				$post['post_title'] = $image_alt;
				wp_update_post($post);
			}
		}
	endforeach;	
}


// checks if a user is logged in, if not redirect them to the login page
function auth_redirect_login() {
    $user = wp_get_current_user();
    if ( $user->id == 0 ) {
        nocache_headers();
        wp_redirect(get_option('siteurl') . '/wp-login.php?redirect_to=' . urlencode($_SERVER['REQUEST_URI']));
        exit();
    }
}


// gets the ad tags
function cp_get_the_term_list( $id = 0, $taxonomy, $before = '', $sep = '', $after = '' ) {
    $terms = get_the_terms( $id, $taxonomy );

    if (is_wp_error($terms))
        return $terms;

    if (empty($terms))
        return false;

    foreach ($terms as $term) {
        $link = get_term_link($term, $taxonomy);
        if (is_wp_error($link))
            return $link;
        $term_links[] = $term->name . ', ';
    }

    $term_links = apply_filters( "term_links-$taxonomy", $term_links );

    return $before . join( $sep, $term_links ) . $after;
}


// change ad to draft if it's expired
function cp_has_ad_expired($post_id) {
    global $wpdb;

    // check to see if ad is legacy or not
    if(get_post_meta($post_id, 'expires', true))
        $expire_date = get_post_meta($post_id, 'expires', true);
    else
        $expire_date = get_post_meta($post_id, 'cp_sys_expire_date', true);

    // debugging variables
    // echo date_i18n('m/d/Y H:i:s') . ' <-- current date/time GMT<br/>';
    // echo $expire_date . ' <-- expires date/time<br/>';

    // if current date is past the expires date, change post status to draft
    if (strtotime(date('Y-m-d H:i:s')) > (strtotime($expire_date))) :
        $my_post = array();
        $my_post['ID'] = $post_id;
        $my_post['post_status'] = 'draft';
        wp_update_post($my_post);

        return true;
    endif;
}


// saves the ad on the tpl-edit-item.php page template
function cp_update_listing() {
    global $wpdb;

    // check to see if html is allowed
    if (get_option('cp_allow_html') != 'yes')
        $post_content = appthemes_filter($_POST['post_content']);
    else
        $post_content = $_POST['post_content'];

    // keep only numeric, commas or decimal values
    if (!empty($_POST['cp_price']))
        $_POST['cp_price'] = appthemes_clean_price($_POST['cp_price']);

    // keep only values and insert/strip commas if needed and put into an array
    if (!empty($_POST['tags_input']))
        $_POST['tags_input'] = appthemes_clean_tags($_POST['tags_input']);
        $new_tags = explode(',', $_POST['tags_input']);


    // put all the ad elements into an array
    // these are the minimum required fields for WP (except tags)
    $update_ad                      = array();
    $update_ad['ID']                = trim($_POST['ad_id']);
    $update_ad['post_title']        = appthemes_filter($_POST['post_title']);
    $update_ad['post_content']      = trim($post_content);
    $update_ad['tags_input']        = $new_tags; // array
    //$update_ad['post_category']   = array((int)appthemes_filter($_POST['cat'])); // maybe use later if we decide to let users change categories

    // make sure the WP sanitize_post function doesn't strip out embed & other html
    if (get_option('cp_allow_html') == 'yes')
        $update_ad['filter'] = true;

    //print_r($update_ad).' <- new ad array<br>'; // for debugging

    // update the ad and return the ad id
    $post_id = wp_update_post($update_ad);


    if($post_id) {

        // now update all the custom fields
        foreach($_POST as $meta_key => $meta_value) {
            if (appthemes_str_starts_with($meta_key, 'cp_'))
                //echo $meta_key . ' <--metakey <br/>' . $meta_value . ' <--metavalue<br/><br/>'; // for debugging
                update_post_meta($post_id, $meta_key, $meta_value);
        }

        $errmsg = '<div class="box-yellow"><b>' . __('Your ad has been successfully updated.','appthemes') . '</b> <a href="' . CP_DASHBOARD_URL . '">' . __('Return to my dashboard','appthemes') . '</a></div>';

    } else {
        // the ad wasn't updated so throw an error
        $errmsg = '<div class="box-red"><b>' . __('There was an error trying to update your ad.','appthemes') . '</b></div>';

    }

    return $errmsg;

}


// builds the edit ad form on the tpl-edit-item.php page template
function cp_edit_ad_formbuilder($results, $getad) {
    global $wpdb;

    foreach ($results as $result) :

        // get all the custom fields on the post and put into an array
        $custom_field_keys = get_post_custom_keys($getad->ID);

        if(!$custom_field_keys) continue;
            // wp_die('Error: There are no custom fields');

        // we only want key values that match the field_name in the custom field table or core WP fields.
        if (in_array($result->field_name, $custom_field_keys) || ($result->field_name == 'post_content') || ($result->field_name == 'post_title') || ($result->field_name == 'tags_input')) :

            // we found a match so go fetch the custom field value
            $post_meta_val = get_post_meta($getad->ID, $result->field_name, true);

            // now loop through the form builder and make the proper field and display the value
            switch($result->field_type) {

            case 'text box':
            ?>
                <li id="list_<?php echo $result->field_name; ?>">
                    <label><?php if($result->field_tooltip) : ?><a href="#" tip="<?php echo $result->field_tooltip; ?>" tabindex="999"><div class="helpico"></div></a><?php endif; ?><?php echo $result->field_label;?>: <?php if($result->field_req) echo '<span class="colour">*</span>' ?></label>
                    <input name="<?php echo $result->field_name; ?>" type="text" class="text<?php if ($result->field_req) echo ' required'; ?>" style="min-width:200px;" value="<?php if ($result->field_name == 'post_title') {echo $getad->post_title;} elseif ($result->field_name == 'tags_input') { echo rtrim(trim(cp_get_the_term_list($getad->ID,'ad_tag')), ',');} else { echo $post_meta_val; } ?>" />
                    <div class="clr"></div>
                </li>
            <?php
            break;

            case 'drop-down':
            ?>
                <li id="list_<?php echo $result->field_name; ?>">
                    <label><?php if($result->field_tooltip) : ?><a href="#" tip="<?php echo $result->field_tooltip; ?>" tabindex="999"><div class="helpico"></div></a><?php endif; ?><?php echo $result->field_label;?>: <?php if($result->field_req) echo '<span class="colour">*</span>' ?></label>
                    <select name="<?php echo $result->field_name; ?>" class="dropdownlist<?php if ($result->field_req) echo ' required'; ?>">
					<?php if (!$result->field_req) : ?><option value="">-- <?php _e('Select', 'appthemes') ?> --</option><?php endif; ?>
                    <?php
                    $options = explode(',', $result->field_values);

                    foreach ($options as $option)
                    {
                        ?>

                            <option style="min-width:177px" <?php if ($post_meta_val == trim($option)) { echo 'selected="yes"';} ?> value="<?php echo trim($option); ?>"><?php echo trim($option);?></option>

                        <?php
                    }
                    ?>

                    </select>
                    <div class="clr"></div>
                </li>

            <?php
            break;

            case 'text area':

            ?>
                <li id="list_<?php echo $result->field_name; ?>">
                    <label><?php if($result->field_tooltip) : ?><a href="#" tip="<?php echo $result->field_tooltip; ?>" tabindex="999"><div class="helpico"></div></a><?php endif; ?><?php echo $result->field_label;?>: <?php if($result->field_req) echo '<span class="colour">*</span>' ?></label>
                    <div class="clr"></div>
                    <textarea rows="4" cols="23" name="<?php echo $result->field_name; ?>" id="<?php echo $result->field_name;?>"><?php if ($result->field_name == 'post_content') { echo $getad->post_content; } else { echo $post_meta_val; } ?></textarea>
                    <div class="clr"></div>

            <?php if (get_option('cp_allow_html') == 'yes') : ?>            
			<script type="text/javascript"> <!--
			tinyMCE.execCommand('mceAddControl', false, '<?php echo $result->field_name;?>'); 
			--></script>
            <?php endif; ?>

                </li>
            <?php
            break;

			case 'radio':			
					$options = explode(',', $result->field_values);
					?>
				<li id="list_<?php echo $result->field_name; ?>">
					<label><?php if($result->field_tooltip) : ?><a href="#" tip="<?php echo $result->field_tooltip; ?>" tabindex="999"><div class="helpico"></div></a><?php endif; ?><?php echo $result->field_label;?>: <?php if($result->field_req) echo '<span class="colour">*</span>' ?></label>
                    
					<?php if(!$result->field_req): ?>
                    	<input type="radio" name="<?php echo $result->field_name; ?>" id="<?php echo $result->field_name; ?>" class="radiolist"  checked="checked">
                        <?php echo __('None'); ?>
					<?php
					endif;
					
					$firstRadio = true;
					foreach ($options as $option) {
					?>
						<input type="radio" name="<?php echo $result->field_name; ?>" id="<?php echo $result->field_name; ?>" value="<?php echo $option; ?>"
                        	class="radiolist" <?php if($result->field_req && $firstRadio) { echo 'checked="checked"'; $firstRadio = false; } ?>>
						<?php echo $option; ?>
	
					<?php
					}
					?>
				</li>
	
			<?php
			break;
			
			case 'checkbox':
					$options = explode(',', $result->field_values); ?>
				<li id="list_<?php echo $result->field_name; ?>">
                    <label><?php if($result->field_tooltip) : ?><a href="#" tip="<?php echo $result->field_tooltip; ?>" tabindex="999"><div class="helpico"></div></a><?php endif; ?><?php echo $result->field_label;?>: <?php if($result->field_req) echo '<span class="colour">*</span>' ?></label>	
                    
            <?php
					$optionCursor = 1;
					foreach ($options as $option) {
					?>
						<input type="checkbox" name="<?php echo $result->field_name; ?>" id="<?php echo $result->field_name; echo '_'.$optionCursor++; ?>" 
                        value="<?php echo $option; ?>" class="checkboxlist" onclick="addRemoveCheckboxValues(this, '<?php echo $result->field_name; ?>_value')">
						<?php echo $option; ?>
	
					<?php
					}
					?>
                    <input type="checkbox" name="<?php echo $result->field_name; ?>" id="<?php echo $result->field_name; ?>_value" value="" checked="checked" style="display:none;" />
				</li>
	
			<?php
			break;
			
            }

        endif;

    endforeach;

}

// shows how much time is left before the ad expires
function cp_timeleft($theTime) {
	$now = strtotime("now");
	$timeLeft = $theTime - $now;

    $days_label = __('days','appthemes');
    $day_label = __('day','appthemes');
    $hours_label = __('hours','appthemes');
    $hour_label = __('hour','appthemes');
    $mins_label = __('mins','appthemes');
    $min_label = __('min','appthemes');
    $secs_label = __('secs','appthemes');
    $r_label = __('remaining','appthemes');
    $expired_label = __('This ad has expired','appthemes');

    if($timeLeft > 0)
    {
    $days = floor($timeLeft/60/60/24);
    $hours = $timeLeft/60/60%24;
    $mins = $timeLeft/60%60;
    $secs = $timeLeft%60;

    if($days == 01) {$d_label=$day_label;} else {$d_label=$days_label;}
    if($hours == 01) {$h_label=$hour_label;} else {$h_label=$hours_label;}
    if($mins == 01) {$m_label=$min_label;} else {$m_label=$mins_label;}

    if($days){$theText = $days . " " . $d_label;
    if($hours){$theText .= ", " .$hours . " " . $h_label;}}
    elseif($hours){$theText = $hours . " " . $h_label;
    if($mins){$theText .= ", " .$mins . " " . $m_label;}}
    elseif($mins){$theText = $mins . " " . $m_label;
    if($secs){$theText .= ", " .$secs . " " . $secs_label;}}
    elseif($secs){$theText = $secs . " " . $secs_label;}}
    else{$theText = $expired_label;}
    return $theText;
}


// Breadcrumb for the top of pages
function cp_breadcrumb() {
	global $app_abbr;
 
  $delimiter = '&raquo;';
  $currentBefore = '<span class="current">';
  $currentAfter = '</span>';
  $term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );


  if ( !is_home() || !is_front_page() || is_paged() ) {

    echo '<div id="crumbs">';

    global $post;
    $home = get_bloginfo('url');
    echo '<a href="' . $home . '">' . __('Home', 'appthemes') . '</a> ' . $delimiter . ' ';

    if ( is_category() ) {
      global $wp_query;
      $cat_obj = $wp_query->get_queried_object();
      $thisCat = $cat_obj->term_id;
      $thisCat = get_category($thisCat);
      $parentCat = get_category($thisCat->parent);
      if ($thisCat->parent != 0) echo(get_category_parents($parentCat, TRUE, ' ' . $delimiter . ' '));
      echo $currentBefore;
      single_cat_title();
      echo $currentAfter;
    } elseif ( is_day() ) {
      echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
      echo '<a href="' . get_month_link(get_the_time('Y'),get_the_time('m')) . '">' . get_the_time('F') . '</a> ' . $delimiter . ' ';
      echo $currentBefore . get_the_time('d') . $currentAfter;

    } elseif ( is_month() ) {
      echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
      echo $currentBefore . get_the_time('F') . $currentAfter;

    } elseif ( is_year() ) {
      echo $currentBefore . get_the_time('Y') . $currentAfter;

    } elseif ( is_single() ) {
	  $cat = get_the_category(); $cat = $cat[0];

		  // check and see if it's a blog post or custom post type
		  if(get_the_term_list($post->ID, 'ad_cat')) {
			  echo get_the_term_list($post->ID, 'ad_cat', '', ' ' . $delimiter . ' ', ' ' . $delimiter . ' ');
		  } else {
			  $page_data = get_page(get_option($app_abbr.'_blog_page_id'));
			  echo '<a href="/'.$page_data->post_name.'/">'.$page_data->post_title.'</a> ' . $delimiter . ' ';
		  }
		  
	  echo $currentBefore;
	  the_title();
	  echo $currentAfter;

    } elseif ( is_page() && !$post->post_parent ) {
      echo $currentBefore;
      the_title();
      echo $currentAfter;

    } elseif ( is_page() && $post->post_parent ) {
      $parent_id  = $post->post_parent;
      $breadcrumbs = array();
      while ($parent_id) {
        $page = get_page($parent_id);
        $breadcrumbs[] = '<a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a>';
        $parent_id  = $page->post_parent;
      }
      $breadcrumbs = array_reverse($breadcrumbs);
      foreach ($breadcrumbs as $crumb) echo $crumb . ' ' . $delimiter . ' ';
      echo $currentBefore;
      the_title();
      echo $currentAfter;

    } elseif ( is_search() ) {
      echo $currentBefore . __('Search results for', 'appthemes') .' &#39;' . get_search_query() . '&#39;' . $currentAfter;

    } elseif ( is_tag() ) {
      echo $currentBefore . __('Posts tagged &#39;', 'appthemes');
      single_tag_title();
      echo '&#39;' . $currentAfter;

    } elseif ( is_author() ) {
       global $author;
      $userdata = get_userdata($author);
      echo $currentBefore . __('About', 'appthemes') .'&nbsp;' . $userdata->display_name . $currentAfter;

    } elseif ( is_404() ) {
      echo $currentBefore . __('Error 404', 'appthemes') . $currentAfter;
    } else { //its a custom taxonomy
		echo $currentBefore . $term->name . $currentAfter;
	}

    if ( get_query_var('paged') ) {
      if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ' (';
      echo __('Page', 'appthemes') . ' ' . get_query_var('paged');
      if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ')';
    }

    echo '</div>';

  }

}



// custom related posts function based on tags
// not being used in 3.0 yet
function cp_related_posts($postID, $width, $height) {
    global $wpdb, $post;
    $output = '';

    if (!get_option('cp_similar_items')) {

// if (!$post_id) { $post_id = $post->ID; }

        $q = "SELECT DISTINCT object_id, post_title, post_content ".
                "FROM $wpdb->term_relationships r, $wpdb->term_taxonomy t, $wpdb->posts p ".
                "WHERE t.term_id IN (".
                "SELECT t.term_id FROM $wpdb->term_relationships r, $wpdb->term_taxonomy t ".
                "WHERE r.term_taxonomy_id = t.term_taxonomy_id ".
                "AND t.taxonomy = 'category' ".
                "AND r.object_id = $postID".
                ") ".
                "AND r.term_taxonomy_id = t.term_taxonomy_id ".
                "AND p.post_status = 'publish' ".
                "AND p.ID = r.object_id ".
                "AND object_id <> $postID ".
                "AND p.post_type = 'ad_listing' ".
                "ORDER BY RAND() LIMIT 5";

        $entries = $wpdb->get_results($q);

//$output .= '<h3>'. __('Similar Items','appthemes') . '</h3>';
        $output .= '<div id="similar-items">';

        if ($entries) {

            $output .= '<ul>';

            foreach ($entries as $post) {
                $output .= '<li class="clearfix">';
                $output .= '<div class="list_ad_img"><img src="'.cp_single_image_raw($post->object_id, $width, $height).'" /></div>';
                $output .= '<span class="list_ad_wrap_wide"><a class="list_ad_link_wide" href="'.get_permalink($post->object_id).'">'. $post->post_title. '</a><br/>';
                $output .= substr(strip_tags($post->post_content), 0, 165).'...</span>';
                $output .= '</li>';
            }

        } else {
            $output .= '<p>' . __('No matches found', 'appthemes') . '</p>';
        }
        $output .= '</ul>';
        $output .= '</div>';

        return $output;
    }
}


// show category with price dropdown
if (!function_exists('cp_dropdown_categories_prices')) {
	function cp_dropdown_categories_prices( $args = '' ) {
		$defaults = array( 'show_option_all' => '', 'show_option_none' => '','orderby' => 'ID', 'order' => 'ASC','show_last_update' => 0, 'show_count' => 0,'hide_empty' => 1, 'child_of' => 0,'exclude' => '', 'echo' => 1,'selected' => 0, 'hierarchical' => 0,'name' => 'cat', 'class' => 'postform','depth' => 0, 'tab_index' => 0 );
		
		$defaults['selected'] = ( is_category() ) ? get_query_var( 'cat' ) : 0;
		$r = wp_parse_args( $args, $defaults );
		$r['include_last_update_time'] = $r['show_last_update'];
		extract( $r );
	
		$tab_index_attribute = '';
		if ( (int) $tab_index > 0 )
			$tab_index_attribute = " tabindex=\"$tab_index\"";
		$categories = get_categories( $r );
		$output = '';
		if ( ! empty( $categories ) ) {
			$output = "<select name='$name' id='$name' class='$class' $tab_index_attribute>\n";
	
			if ( $show_option_all ) {
				$show_option_all = apply_filters( 'list_cats', $show_option_all );
				$selected = ( '0' === strval($r['selected']) ) ? " selected='selected'" : '';
				$output .= "\t<option value='0'$selected>$show_option_all</option>\n";
			}
	
			if ( $show_option_none ) {
				$show_option_none = apply_filters( 'list_cats', $show_option_none );
				$selected = ( '-1' === strval($r['selected']) ) ? " selected='selected'" : '';
				$output .= "\t<option value='-1'$selected>$show_option_none</option>\n";
			}
	
			if ( $hierarchical )
				$depth = $r['depth'];  // Walk the full depth.
			else
				$depth = -1; // Flat.
	
			$output .= cp_category_dropdown_tree( $categories, $depth, $r );
			$output .= "</select>\n";
		}
	
		$output = apply_filters( 'wp_dropdown_cats', $output );
	
		if ( $echo )
			echo $output;
	
		return $output;
	}
}

// needed for the cp_dropdown_categories_prices function
function cp_category_dropdown_tree() {
    $args = func_get_args();
    if ( empty($args[2]['walker']) || !is_a($args[2]['walker'], 'Walker') )
        $walker = new cp_CategoryDropdown;
    else
        $walker = $args[2]['walker'];
    return call_user_func_array(array( &$walker, 'walk' ), $args );
}

// needed for the cp_category_dropdown_tree function
class cp_CategoryDropdown extends Walker {
    var $tree_type = 'category';
    var $db_fields = array ('parent' => 'parent', 'id' => 'term_id');
    function start_el(&$output, $category, $depth, $args) {
        $pad = str_repeat('&nbsp;', $depth * 3);
        $cat_name = apply_filters('list_cats', $category->name, $category);
        $output .= "\t<option class=\"level-$depth\" value=\"".$category->term_id."\">";
        $output .= $pad.$cat_name;
        $output .= ' - ' . get_option('cp_curr_symbol') . get_option('cp_cat_price_'.$category->cat_ID) . '</option>'."\n";;
    }
}


// category menu drop-down display
if (!function_exists('cp_cat_menu_drop_down')) {
    function cp_cat_menu_drop_down($cols = 3, $subs = 0) {
		global $wpdb;

		// get any existing copy of our transient data
		if (false === ($cp_cat_menu = get_transient('cp_cat_menu'))) {

			// put all options into vars so we don't have to waste resources by calling them each time from within the loops
			$cp_cat_parent_count = get_option('cp_cat_parent_count');
			$cp_cat_child_count = get_option('cp_cat_child_count');
			$cp_cat_hide_empty = get_option('cp_cat_hide_empty');
			$cp_cat_orderby = get_option('cp_cat_orderby');

			// get all cats for the taxonomy ad_cat
			$cats = get_terms('ad_cat', 'hide_empty=0&hierarchical=0&pad_counts=1&show_count='.$cp_cat_parent_count.'&orderby='.$cp_cat_orderby.'&order=ASC');

			 //remove all sub cats from the array
			foreach ($cats as $key => $value)
				if ($value->parent != 0) unset($cats[$key]);


			$i = 0;
			$cat_cols = $cols; // change this to add/remove columns
			$total_main_cats = count($cats); // total number of parent cats
			$cats_per_col = round($total_main_cats / $cat_cols); // items per column

			// loop through all the sub
			foreach($cats as $cat) :

				if (($i == 0) || ($i == $cats_per_col) || ($i == ($cats_per_col * 2)) || ($i == ($cats_per_col * 3)) ) {
					if ($i == 0) $first = ' first';
					$cp_cat_menu .= '<div class="catcol '. $first .'">';
				}

				// only show the total count if option is set
				if($cp_cat_parent_count == 1)
					$show_count = '('. $cat->count .')';

				$cp_cat_menu .= '<ul>';
				$cp_cat_menu .= '<li class="maincat cat-item-'. $cat->term_id .'"><a href="'. get_term_link($cat, 'ad_cat') .'" title="'. esc_attr($cat->description) .'">'. $cat->name .'</a> '.$show_count.'</li>';


				// don't show any sub cats
				if($subs <> 999) :

					// now get all the sub cats based off the parent cat id
					$subcats = wp_list_categories('orderby='.$cp_cat_orderby.'&taxonomy=ad_cat&order=asc&show_count='.$cp_cat_child_count.'&hierarchical=0&pad_counts=1&use_desc_for_title=1&hide_empty='.$cp_cat_hide_empty.'&depth=1&title_li=&echo=0&number='.$subs.'&child_of='.$cat->term_id);

					// strip out the default wp title tag since the use_desc_for_title param doesn't seem to work since WP 2.9.2
					//$subcats = preg_replace('/title=\"(.*?)\"/','',$subcats); // deprecated since CP v3.0.5 since use_desc_for_title now works

					// if you want to strip out the no categories text, just uncomment the line below
					// $subcats = str_replace('<li>No categories</li>', '', $subcats);

					// print out all the subcats for the current parent cat
					$cp_cat_menu .= $subcats;

				endif;

				$cp_cat_menu .= '</ul>';

				if (($i == ($cats_per_col - 1)) || ($i == (($cats_per_col * 2) - 1)) || ($i == (($cats_per_col * 3) - 1)) || ($i == ($total_main_cats - 1)))
					$cp_cat_menu .= '</div><!-- /catcol -->';

				$i++;

			endforeach;

			return $cp_cat_menu;

			// set transient
			set_transient('cp_cat_menu', $cp_cat_menu, get_option('cp_cache_expires'));
			
		} else {

			// must already be transient data so use that
			return get_transient('cp_cat_menu');

		}

    }
}

// delete transient to refresh cat menu
function cp_edit_term_delete_transient() {
     delete_transient('cp_cat_menu');
}

// runs when categories/tags are edited
add_action('edit_term', 'cp_edit_term_delete_transient');



// directory home page category display
// deprecated since 3.0.5.2
if (!function_exists('cp_directory_cat_columns')) {
    function cp_directory_cat_columns($cols) {

        // get all cats except for the blog
        $cats = get_categories('hide_empty=0&hierarchical=0&pad_counts=1&show_count=1&orderby=name&order=ASC&taxonomy=ad_cat');

        // remove all sub cats from the array
        foreach ($cats as $key => $value){
            if ($value->category_parent != 0)
                unset($cats[$key]);
        }

        $i = 0;
        $cat_cols = $cols; // change this to add/remove columns
        $total_main_cats = count($cats); // total number of parent cats
        $cats_per_col = round($total_main_cats / $cat_cols); // items per column

        // loop through all the sub
        foreach($cats as $cat) {

            if (($i == 0) || ($i == $cats_per_col) || ($i == ($cats_per_col * 2))) {
            ?>

                    <div class="catcol">

            <?php
            }
            ?>

            <ul>
                    <li class="maincat"><a href="<?php echo get_category_link($cat->term_id); ?>"><?php echo $cat->name; ?></a> (<?php echo $cat->category_count; ?>)</li>

            <?php
            // now get all the sub cats based off the parent cat id
            $subcats = wp_list_categories('taxonomy=ad_cat&orderby=name&order=asc&hierarchical=0&show_count=1&pad_counts=1&use_desc_for_title=0&hide_empty=0&depth=1&number='.get_option('cp_dir_sub_num').'&title_li=&echo=0&child_of='.$cat->cat_ID);

            // strip out the default wp title tag since the use_desc_for_title param doesn't seem to work in WP 2.9.2
            $subcats = preg_replace('/title=\"(.*?)\"/','',$subcats);

            // if you want to strip out the no categories text, just uncomment the line below
            // $subcats = str_replace('<li>No categories</li>', '', $subcats);

            // print out all the subcats for the current parent cat
            echo $subcats;
            ?>

            </ul>

            <?php

            if (($i == ($cats_per_col - 1)) || ($i == (($cats_per_col * 2) - 1)) || ($i == ($total_main_cats - 1))) {
            ?>
                    </div><!-- /catcol -->

            <?php
            }

            $i++;
        }

    }
}




// If you want to automatically resize youtube videos uncomment the filter
function cp_resize_youtube($content) {
    return str_replace('width="640" height="385"></embed>', 'width="480" height="295"></embed>', $content);
}
//add_filter('the_content', 'cp_resize_youtube', 999);



//get a list of coupons, or details about a single coupon if an Coupon Code is passed
function cp_get_coupons($couponCode = '') {
    global $wpdb;
    $sql = "SELECT * "
    . "FROM " . $wpdb->prefix . "cp_coupons ";
    if($couponCode != '')
    $sql .= "WHERE coupon_code='$couponCode' ";
    $sql .= "ORDER BY coupon_id desc";

    $results = $wpdb->get_results($sql);
    return $results;
}

//check coupon code against coupons in the database and return the discount
function cp_check_coupon_discount($couponCode) {
	//stop if no coupon code is passed or passed empty
	if($couponCode == '') return false;

	//get the coupon
	$results = cp_get_coupons($couponCode);

	//stop if result is empty or inactive
	if(!$results) return false;
	if($results[0]->coupon_status != 'active') return false;

	//if coupon exists and is not inactive then return the discount
	return $results[0];
}

//function uses a coupon code by incrimenting its value in the database
function cp_use_coupon($couponCode) {
	global $wpdb;
        $update =   'UPDATE ' . $wpdb->prefix . 'cp_coupons ' .
                    "SET coupon_use_count = coupon_use_count + 1 " .
                    "WHERE coupon_code = '$couponCode'";
	$results = $wpdb->query($update);
}


// ajax auto-complete search 
function cp_suggest() {
    global $wpdb;
	
	$s = $_GET['term']; // is this slashed already?

    if ( isset($_GET['tax']) )
            $taxonomy = sanitize_title($_GET['tax']);
    else
            die('no taxonomy');

    if ( false !== strpos( $s, ',' ) ) {
        $s = explode( ',', $s );
        $s = $s[count( $s ) - 1];
    }
    $s = trim( $s );
    if ( strlen( $s ) < 2 )
        die('need at least two characters'); // require 2 chars for matching

	$terms = $wpdb->get_col( "
		SELECT t.slug FROM ".$wpdb->prefix."term_taxonomy AS tt INNER JOIN ".
		$wpdb->prefix."terms AS t ON tt.term_id = t.term_id ".
		"WHERE tt.taxonomy = '$taxonomy' ".
		"AND t.name LIKE (
			'%$s%'
		)" .
		"LIMIT 50"
		);
	if(empty($terms)){
		//$results[0] = {"name":"no results"};
		echo json_encode($terms);
		die;
	}else{
		$i = 0;
		foreach ($terms as $term) {
			$results[$i] = get_term_by( 'slug', $term, $taxonomy );
			$i++;
		}
		echo json_encode($results);
		die;
	}
}


/**
 * Custom ClassiPress search engine to search
 * and include custom fields
 * @global <type> $wpdb
 * @param <type> $join
 * @return <type>
 *
 *
 */



// exclude pages and blog entries from search results if option is set
// not using yet since still using custom where statement below
// since 3.0.5
function appthemes_exclude_search_types($query) {
    if ($query->is_search) {

    if (get_option('cp_search_ex_blog') == 'yes')
        $query->set('post_type', 'ad_listing');
    else
        $query->set( 'post_type', array( 'post', 'ad_listing' ) );

    }
return $query;
}

//if (get_option('cp_search_ex_pages') == 'yes')
//add_filter('pre_get_posts', 'appthemes_exclude_search_types');



// search only ads and not pages
function cp_is_type_page() {
    global $post;
    if ($post->post_type == 'page')
        return true;
    else
        return false;
}

// get all custom field names so we can use them for search
function cp_custom_search_fields() {
    global $wpdb;

    $sql = "SELECT field_name "
            . "FROM ". $wpdb->prefix . "cp_ad_fields p "
            . "WHERE p.field_name LIKE 'cp_%' ";

    $results = $wpdb->get_results($sql);

    if($results) {
        foreach ($results as $result) :
            // put the fields into an array
            $custom_fields[] = $result->field_name;
        endforeach;
    }

    return $custom_fields;
}


// search on custom fields
function custom_search_groupby($groupby) {
	global $wpdb, $wp_query;

	$groupby = "$wpdb->posts.ID";

    return $groupby;
}


// search on custom fields
function custom_search_join($join) {
	global $wpdb, $wp_query;

	if(is_search() && isset($_GET['s'])) {
        
		$join  = " INNER JOIN $wpdb->term_relationships AS r ON ($wpdb->posts.ID = r.object_id) ";
		$join .= " INNER JOIN $wpdb->term_taxonomy AS x ON (r.term_taxonomy_id = x.term_taxonomy_id) ";
		$join .= " AND (x.taxonomy = 'ad_tag' OR x.taxonomy = 'ad_cat') ";


		// if an ad category is selected, limit results to that cat only
		$catid = $wp_query->query_vars['cat'];
		
		if (!empty($catid)) :

			// put the catid into an array
			(array) $include_cats[] = $catid;

			// get all sub cats of catid and put them into the array
			$descendants = get_term_children((int) $catid, 'ad_cat');

			foreach($descendants as $key => $value)
				$include_cats[] = $value;

			// take catids out of the array and separate with commas
			$include_cats = "'" . implode("', '", $include_cats) . "'";

			// add the category filter to show anything within this cat or it's children
			$join .= " AND x.term_id IN ($include_cats) ";

		endif; // end category filter


		$join .= " INNER JOIN $wpdb->postmeta AS m ON ($wpdb->posts.ID = m.post_id) ";
		$join .= " INNER JOIN $wpdb->terms AS t ON x.term_id = t.term_id ";

    }
	
    return $join;
}


// search on custom fields
function custom_search_where($where) {
    global $wpdb, $wp_query;
    $old_where = $where; // intercept the old where statement
    if (is_search() && isset($_GET['s'])) {

        // get the custom fields to add to search
        $custom_fields = cp_custom_search_fields();
        // enter additional legacy custom fields and ad id field
        $custom_fields_more = array('name', 'price', 'phone', 'location', 'cp_sys_ad_conf_id');
        // now merge the two arrays together
        $customs = array_merge($custom_fields,$custom_fields_more);

        $query = '';
        $var_q = stripslashes($_GET['s']);
        if ($_GET['sentence']) {
            $search_terms = array($var_q);
        }
        else {
            preg_match_all('/".*?("|$)|((?<=[\\s",+])|^)[^\\s",+]+/', $var_q, $matches);
            $search_terms = array_map(create_function('$a', 'return trim($a, "\\"\'\\n\\r ");'), $matches[0]);
        }

        $n = ($_GET['exact']) ? '' : '%';
        $searchand = '';

        foreach((array)$search_terms as $term) {
            $term = addslashes_gpc($term);

            $query .= "{$searchand}(";
            $query .= "($wpdb->posts.post_title LIKE '{$n}{$term}{$n}')";
            $query .= " OR ($wpdb->posts.post_content LIKE '{$n}{$term}{$n}')";
			$query .= " OR ((t.name LIKE '{$n}{$term}{$n}')) OR ((t.slug LIKE '{$n}{$term}{$n}'))";

            foreach($customs as $custom) {
                $query .= " OR (";
                $query .= "(m.meta_key = '$custom')";
                $query .= " AND (m.meta_value  LIKE '{$n}{$term}{$n}')";
                $query .= ")";
            }

            $query .= ")";
            $searchand = ' AND ';
        }

        $term = $wpdb->escape($var_q);
        if (!$_GET['sentence'] && count($search_terms) > 1 && $search_terms[0] != $var_q) {
            $search .= " OR ($wpdb->posts.post_title LIKE '{$n}{$term}{$n}')";
            $search .= " OR ($wpdb->posts.post_content LIKE '{$n}{$term}{$n}')";
        }

        if (!empty($query)) {
            
            $where = " AND ({$query}) AND ($wpdb->posts.post_status = 'publish') ";

            // setup the array for post types
            $post_type_array = array();

            // always include the ads post type
            $post_type_array[] = 'ad_listing';

            // check to see if we include blog posts
            if (get_option('cp_search_ex_blog') == 'no')
                $post_type_array[] = 'post';

            // check to see if we include pages
            if (get_option('cp_search_ex_pages') == 'no')
                $post_type_array[] = 'page';

            // build the post type filter sql from the array values
            $post_type_filter = "'" . implode("','",$post_type_array). "'";

            // return the post type sql to complete the where clause
            $where .= " AND ($wpdb->posts.post_type IN ($post_type_filter)) ";

        }
    }
    
    return($where);
}

// add search filters
add_filter('posts_join', 'custom_search_join');
add_filter('posts_where', 'custom_search_where');
add_filter('posts_groupby', 'custom_search_groupby');



// if an ad is created and doesn't have an expiration date,
// make sure to insert one based on the Ad Listing Period option.
// all ads need an expiration date otherwise they will automatically
// expire. this is common when customers manually create an ad through
// the WP admin new post or when using an automated scrapper script
function cp_check_expire_date($post_id) {
	global $wpdb;

	// we don't want to add the expires date to blog posts
	if (get_post_type() != 'ad_listing')  {

		// do nothing

	} else {

		// add default expiration date if the expired custom field is blank or empty
		if (!get_post_meta($post_id, 'cp_sys_expire_date', true) || (get_post_meta($post_id, 'cp_sys_expire_date', true) == '')) :
			$ad_length = get_option('cp_prun_period');
			if(!$ad_length) $ad_length = '365'; // if the prune days is empty, set it to one year
			$ad_expire_date = date_i18n('m/d/Y H:i:s', strtotime('+' . $ad_length . ' days')); // don't localize the word 'days'
			add_post_meta($post_id, 'cp_sys_expire_date', $ad_expire_date, true);
		endif;

	}

}

/**
 * RENEW AD LISTINGS : @SC - Allowing free ads to be relisted, call this 
 * function and send the ads post id. We will check to make sure its free
 * and relist the ad for the same duration it 
 */
if (!function_exists('cp_renew_ad_listing')) { function cp_renew_ad_listing ( $ad_id ) {
	$listfee = (float)get_post_meta($ad_id, 'cp_sys_total_ad_cost', true);
	
	// protect against false URL attempts to hack ads into free renewal
	if ($listfee == 0)	{
		$ad_length = get_post_meta($ad_id, 'cp_sys_ad_duration', true);
		if(isset($ad_length))
			$ad_length = $ad_length;
		else
			$ad_length = get_option('cp_prun_period');

		// set the ad listing expiration date
		$ad_expire_date = date_i18n('m/d/Y H:i:s', strtotime('+' . $ad_length . ' days')); // don't localize the word 'days'

		//now update the expiration date on the ad
		update_post_meta($ad_id, 'cp_sys_expire_date', $ad_expire_date);
		return true;
	}

	//attempt to relist a paid ad
	else {	return false;	}
}}//END cp_renew_ad_listing


// runs when a post is published, or is edited and status is "published"
add_filter('publish_post', 'cp_check_expire_date', 9, 3);




// activate theme support items
if (function_exists('add_theme_support')) { // added in 2.9

	// this theme uses post thumbnails
	add_theme_support('post-thumbnails', array('post', 'page'));
	set_post_thumbnail_size(100, 100); // normal post thumbnails

	// add default posts and comments RSS feed links to head
	add_theme_support( 'automatic-feed-links' );
}

// setup different image sizes
if ( function_exists( 'add_image_size' ) ) {
	add_image_size('blog-thumbnail', 150, 150); // blog post thumbnail size, box crop mode
	add_image_size('sidebar-thumbnail', 50, 50, true); // sidebar blog thumbnail size, box crop mode
	
	// create special sizes for the ads
	add_image_size('ad-thumb', 75, 75, true);
	add_image_size('ad-small', 100, 100, true);
	add_image_size('ad-medium', 250, 250, true);
	//add_image_size('ad-large', 500, 500);
}

// Set the content width based on the theme's design and stylesheet.
// Used to set the width of images and content. Should be equal to the width the theme
// is designed for, generally via the style.css stylesheet.
if (!isset($content_width))
	$content_width = 500;


// This theme supports native menu options, and uses wp_nav_menu() in one location for top navigation.
function appthemes_register_menus() {
	register_nav_menus(array('primary' => __( 'Primary Navigation', 'appthemes')));
}
add_action( 'init', 'appthemes_register_menus' );

//default navigation menu to display if custom menu is not defined
function appthemes_default_menu () {
	$excludePages = get_option('cp_excluded_pages');
	if(get_option($app_abbr.'_enable_blog') == 'no')
		$excludePages .= ','.get_option($app_abbr.'_blog_page_id');
	wp_list_pages('sort_column=menu_order&depth=0&title_li=0&exclude='.$excludePages); 
}

?>

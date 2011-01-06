ClassiPress - Premium WordPress Theme


You can now find the entire installation guide online.
http://appthemes.com/support/docs/

For help and support, please post your questions in our forum.
http://appthemes.com/forum/



Enjoy your new premium theme!


Your AppThemes Team
http://appthemes.com

** Please read the changelog.txt file for information on version modifications. **





/*******************************************************************************************/
/* We are beginning to allow some of our functions to use overrides from the child themes. */
/* To make it easier for you to create your functions.php file, we will list the functions */
/* here as we allow you to override them.                                                  */
/*******************************************************************************************/

/* includes/theme-functions.php functions that can be child theme modded */
// display the login message in the header
function cp_login_head()

// get the date/time ad was posted
function appthemes_date_posted($m_time) 

// display all the custom fields on the single ad page
function cp_get_ad_details($postid, $catid)

// get the first medium image associated to the ad
// used on the home page, search, category, etc
function cp_get_image($post_id = '', $size = 'medium', $num = 1)

// get the image associated to the ad used on the single page
function cp_get_image_url($post_id = '', $size = 'medium', $class = '', $num = 1)

// get the image associated to the ad used on the home page
function cp_get_image_url_feat($post_id = '', $size = 'medium', $class = '', $num = 1)

// get all the small images for the ad and lightbox href
// important and used on the single page
function cp_get_image_url_single($post_id = '', $size = 'medium', $title = '', $num = 1)

// used for getting the single ad image thumbnails
function cp_get_image_thumbs($postID, $height, $width, $lheight, $lwidth, $num=-1, $order='ASC', $orderby='menu_order', $mime='image')

// get the ad price and position the currency symbol
function cp_get_price($postid)

// get all the images associated to the ad and display the
// thumbnail with checkboxes for deleting them
// used on the ad edit page
function cp_get_ad_images($ad_id)

// show category with price dropdown
function cp_dropdown_categories_prices( $args = '' )

// category menu drop-down display
function cp_cat_menu_drop_down($cols = 3, $subs = 0) 

// directory home page category display
function cp_directory_cat_columns($cols)


/* includes/forms/step-functions.php functions that can be child theme modded */

// loops through the custom fields and builds the custom ad form
function cp_formbuilder($results)

// queries the db for the custom ad form based on the cat id
function cp_show_form($catid) 

// if no custom forms exist, just call the default form fields
function cp_show_default_form() 

// determine what the ad post status should be
function cp_set_post_status($advals)


/* includes/theme-login.php functions that can be child theme modded */

// show the custom login page if on wp-login.php
function cp_show_login()

// show the new user registration page
function cp_show_register() 


/* includes/theme-profile.php functions that can be child theme modded */

function cp_profile_fields($user) 

function cp_profile_fields_save($user_id)


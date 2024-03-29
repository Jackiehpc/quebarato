<?php

/**
* Install script to create database
* tables and then insert default data.
* Only run if theme is being activated
* for the first time.
*
*/

// check if theme is activated by admin.
if ((is_admin() && isset($_GET['activated']) && $pagenow == 'themes.php')  || (is_admin() && isset($_GET['upgrade']) && $pagenow == 'admin.php' && $_GET['page'] == 'admin-options.php')) {
	global $wpdb, $app_abbr, $wp_rewrite;

        // run the table install script
        cp_tables_install();
        
        // insert the default values
        cp_default_values();

		// insert the default links
		cp_insert_links();

        // create pages and assign templates
        cp_create_pages();

        // create a category for the blog
        // cp_create_cats(); // legacy since 3.0.5

		//flush the rewrite rules so the new custom post types will automatically work
		$wp_rewrite->flush_rules();

}



// Create the ClassiPress db tables
function cp_tables_install() {
   global $wpdb, $app_abbr;

    $collate = '';
    if($wpdb->supports_collation()) {
        if(!empty($wpdb->charset)) $collate = "DEFAULT CHARACTER SET $wpdb->charset";
        if(!empty($wpdb->collate)) $collate .= " COLLATE $wpdb->collate";
    }


    $sql = "CREATE TABLE IF NOT EXISTS ". $wpdb->prefix . "cp_ad_forms" ." (
        `id` INT(10) NOT NULL auto_increment,
        `form_name` VARCHAR(255) NOT NULL,
        `form_label` VARCHAR(255) NOT NULL,
        `form_desc` LONGTEXT,
        `form_cats` LONGTEXT NOT NULL,
        `form_status` VARCHAR(255) DEFAULT NULL,
        `form_owner` VARCHAR(255) DEFAULT NULL,
        `form_created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
        `form_modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
        PRIMARY KEY id  (`id`)) $collate;";

    $wpdb->query($sql);


    $sql = "CREATE TABLE IF NOT EXISTS ". $wpdb->prefix . "cp_ad_meta" ." (
        `meta_id` INT(10) NOT NULL AUTO_INCREMENT,
        `form_id` INT(10) NOT NULL,
        `field_id` INT(10) NOT NULL,
        `field_req` VARCHAR(255) NOT NULL,
        `field_pos` INT(10) NOT NULL,
        PRIMARY KEY id  (`meta_id`)) $collate;";

    $wpdb->query($sql);


    $sql = "CREATE TABLE IF NOT EXISTS ". $wpdb->prefix . "cp_ad_fields" ." (
        `field_id` INT(10) NOT NULL AUTO_INCREMENT,
        `field_name` VARCHAR(255) NOT NULL,
        `field_label` VARCHAR(255) NOT NULL,
        `field_desc` LONGTEXT NULL,
        `field_type` VARCHAR(255) NOT NULL,
        `field_values` LONGTEXT NULL,
        `field_tooltip` LONGTEXT NULL,
        `field_search` VARCHAR(255) NULL,
        `field_perm` int(11) NOT NULL,
        `field_core` int(11) NOT NULL,
        `field_req` int(11) NOT NULL,
        `field_owner` VARCHAR(255) NOT NULL,
        `field_created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
        `field_modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
        PRIMARY KEY id  (`field_id`)) $collate;";

    $wpdb->query($sql);


    $sql = "CREATE TABLE IF NOT EXISTS ". $wpdb->prefix . "cp_ad_pop_daily" ." (
         `id` mediumint(9) NOT NULL AUTO_INCREMENT,
	 `time` date DEFAULT '0000-00-00' NOT NULL,
	 `postnum` int NOT NULL,
	 `postcount` int DEFAULT '0' NOT NULL,
	 UNIQUE KEY id (id)) $collate;";

    $wpdb->query($sql);


    $sql = "CREATE TABLE IF NOT EXISTS ". $wpdb->prefix . "cp_ad_pop_total" ." (
         `id` mediumint(9) NOT NULL AUTO_INCREMENT,
	 `postnum` int NOT NULL,
	 `postcount` int DEFAULT '0' NOT NULL,
	 UNIQUE KEY id (id)) $collate;";

    $wpdb->query($sql);


    $sql = "CREATE TABLE IF NOT EXISTS ". $wpdb->prefix . "cp_ad_packs" ." (
        `pack_id` INT(10) NOT NULL AUTO_INCREMENT,
        `pack_name` VARCHAR(255) NOT NULL,
        `pack_desc` LONGTEXT NULL,
        `pack_price` DECIMAL(10,2) UNSIGNED NOT NULL DEFAULT '0',
        `pack_duration` INT(5) NOT NULL,
        `pack_images` INT(5) UNSIGNED NOT NULL DEFAULT '0',
        `pack_status` VARCHAR(50) NOT NULL,
        `pack_owner` VARCHAR(255) NOT NULL,    
        `pack_created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
        `pack_modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
        PRIMARY KEY id (`pack_id`)) $collate;";

    $wpdb->query($sql);

	//fix previous installs that had BIGINT pack_price bug
	$sql = "ALTER TABLE ". $wpdb->prefix . "cp_ad_packs" ." MODIFY pack_price DECIMAL(10,2) UNSIGNED NOT NULL DEFAULT '0'";
	$wpdb->query($sql);
	
	$sql = "CREATE TABLE IF NOT EXISTS ". $wpdb->prefix . "cp_coupons" ." (
        `coupon_id` INT(10) NOT NULL AUTO_INCREMENT,
        `coupon_code` VARCHAR(100) NOT NULL,
        `coupon_desc` LONGTEXT NULL,
        `coupon_discount` DECIMAL(10,2) UNSIGNED NOT NULL DEFAULT '0',
		`coupon_discount_type` VARCHAR(50) NOT NULL,
        `coupon_start_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
		`coupon_expire_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
        `coupon_status` VARCHAR(50) NOT NULL,
		`coupon_use_count` int(11) NOT NULL DEFAULT '0',
		`coupon_max_use_count` int(11) NOT NULL DEFAULT '0',
        `coupon_owner` VARCHAR(255) NOT NULL,    
        `coupon_created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
        `coupon_modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
        PRIMARY KEY id (`coupon_id`),
		UNIQUE KEY `coupon_code` (`coupon_code`)) $collate;";

    $wpdb->query($sql);

    // create the paypal transaction table

    $sql = "CREATE TABLE IF NOT EXISTS ". $wpdb->prefix . "cp_order_info" ." (
        `id` mediumint(9) NOT NULL AUTO_INCREMENT,
        `ad_id` INT(10) NOT NULL,
        `first_name` varchar(100) NOT NULL DEFAULT '',
        `last_name` varchar(100) NOT NULL DEFAULT '',
        `payer_email` varchar(100) NOT NULL DEFAULT '',
        `street` varchar(255) NOT NULL DEFAULT '',
        `city` varchar(255) NOT NULL DEFAULT '',
        `state` varchar(255) NOT NULL DEFAULT '',
        `zipcode` varchar(100) NOT NULL DEFAULT '',
        `residence_country` varchar(255) NOT NULL DEFAULT '',
        `transaction_subject` varchar(255) NOT NULL DEFAULT '',
        `memo` varchar(255) DEFAULT NULL,
        `item_name` varchar(255) DEFAULT NULL,
        `item_number` varchar(255) DEFAULT NULL,
        `quantity` char(10) DEFAULT NULL,
        `payment_type` varchar(50) NOT NULL DEFAULT '',
        `payer_status` varchar(50) NOT NULL DEFAULT '',
        `payer_id` varchar(50) NOT NULL DEFAULT '',
        `receiver_id` varchar(50) NOT NULL DEFAULT '',
        `parent_txn_id` varchar(30) NOT NULL DEFAULT '',
        `txn_id` varchar(30) NOT NULL DEFAULT '',
        `txn_type` varchar(10) NOT NULL DEFAULT '',
        `payment_status` varchar(50) NOT NULL DEFAULT '',
        `pending_reason` varchar(50) DEFAULT NULL,
        `mc_gross` varchar(10) NOT NULL DEFAULT '',
        `mc_fee` varchar(10) NOT NULL DEFAULT '',
        `tax` varchar(10) DEFAULT NULL,
        `exchange_rate` varchar(25) DEFAULT NULL,
        `mc_currency` varchar(20) NOT NULL DEFAULT '',
        `reason_code` varchar(20) NOT NULL DEFAULT '',
        `custom` varchar(255) NOT NULL DEFAULT '',
        `test_ipn` varchar(20) NOT NULL DEFAULT '',
        `payment_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
        `create_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
        PRIMARY KEY id (`id`)) $collate;";

    $wpdb->query($sql);



    /**
    * Insert default data into tables
    *
    * Flag values for the cp_ad_fields table
    * =======================================
    * Field permissions (field name - field_perm) are 0,1,2 and are as follows:
    * 0 = rename label, remove from form layout, reorder, change values, delete
    * 1 = rename label, reorder
    * 2 = rename label, remove from form layout, reorder, change values
    *
    * please don't ask about the logic of the order. :-)
    *
    * field_core can be 1 or 0. 1 means it's a core field and will be included
    * in the default form if no custom form has been created
    *
    * field_req in this table is only used for the default form meaning if no
    * custom form has been created, use these fields with 1 meaning mandatory field
    *
    *
    */


    // Check to see if any rows already exist. If so, don't insert any data
    $sql = "SELECT field_id "
         . "FROM " . $wpdb->prefix . "cp_ad_fields LIMIT 1";

    $wpdb->get_results($sql);

    if($wpdb->num_rows == 0) {

        // DO NOT CHANGE THE ORDER OF THE FIRST 9 RECORDS!
        // admin-options.php cp_add_core_fields() depends on these fields
        // add more records after the post_content row insert statement
        $insert = "INSERT INTO " . $wpdb->prefix . "cp_ad_fields" .
            " (field_name, field_label, field_desc, field_type, field_values, field_search, field_perm, field_core, field_req, field_owner, field_created, field_modified) " .
            "VALUES ('"
              . $wpdb->escape('post_title'). "','"
              . $wpdb->escape('Title'). "','"
              . $wpdb->escape('This is the name of the ad and is mandatory on all forms. It is a core ClassiPress field and cannot be deleted.'). "','"
              . $wpdb->escape('text box') . "','"
              . $wpdb->escape('') . "','"
              . $wpdb->escape('') . "','"
              . $wpdb->escape('1') . "','"
              . $wpdb->escape('1') . "','"
              . $wpdb->escape('1') . "','"
              . $wpdb->escape('ClassiPress') . "','"
              . date_i18n("Y-m-d H:i:s") . "','"
              . date_i18n("Y-m-d H:i:s")
              . "'),"
              . "('"
              . $wpdb->escape($app_abbr.'_price'). "','"
              . $wpdb->escape('Price'). "','"
              . $wpdb->escape('This is the price field for the ad. It is a core ClassiPress field and cannot be deleted.'). "','"
              . $wpdb->escape('text box') . "','"
              . $wpdb->escape('') . "','"
              . $wpdb->escape('') . "','"
              . $wpdb->escape('2') . "','"
              . $wpdb->escape('1') . "','"
              . $wpdb->escape('1') . "','"
              . $wpdb->escape('ClassiPress') . "','"
              . date_i18n("Y-m-d H:i:s") . "','"
              . date_i18n("Y-m-d H:i:s")
              . "'),"
              . "('"
              . $wpdb->escape($app_abbr.'_street'). "','"
              . $wpdb->escape('Street'). "','"
              . $wpdb->escape('This is the street address text field. It is a core ClassiPress field and cannot be deleted. (Needed on your forms for Google maps to work best.)'). "','"
              . $wpdb->escape('text box') . "','"
              . $wpdb->escape('') . "','"
              . $wpdb->escape('') . "','"
              . $wpdb->escape('2') . "','"
              . $wpdb->escape('1') . "','"
              . $wpdb->escape('') . "','"
              . $wpdb->escape('ClassiPress') . "','"
              . date_i18n("Y-m-d H:i:s") . "','"
              . date_i18n("Y-m-d H:i:s")
              . "'),"
              . "('"
              . $wpdb->escape($app_abbr.'_city'). "','"
              . $wpdb->escape('City'). "','"
              . $wpdb->escape('This is the city field for the ad listing. It is a core ClassiPress field and cannot be deleted. (Needed on your forms for Google maps to work best.)'). "','"
              . $wpdb->escape('text box') . "','"
              . $wpdb->escape('') . "','"
              . $wpdb->escape('1') . "','"
              . $wpdb->escape('2') . "','"
              . $wpdb->escape('1') . "','"
              . $wpdb->escape('') . "','"
              . $wpdb->escape('ClassiPress') . "','"
              . date_i18n("Y-m-d H:i:s") . "','"
              . date_i18n("Y-m-d H:i:s")
              . "'),"
              . "('"
              . $wpdb->escape($app_abbr.'_state'). "','"
              . $wpdb->escape('State'). "','"
              . $wpdb->escape('This is the state/province drop-down select box for the ad. It is a core ClassiPress field and cannot be deleted. (Needed on your forms for Google maps to work best.)'). "','"
              . $wpdb->escape('drop-down') . "','"
              . $wpdb->escape('Alabama,Alaska,Arizona,Arkansas,California,Colorado,Connecticut,Delaware,District of Columbia,Florida,Georgia,Hawaii,Idaho,Illinois,Indiana,Iowa,Kansas,Kentucky,Louisiana,Maine,Maryland,Massachusetts,Michigan,Minnesota,Mississippi,Missouri,Montana,Nebraska,Nevada,New Hampshire,New Jersey,New Mexico,New York,North Carolina,North Dakota,Ohio,Oklahoma,Oregon,Pennsylvania,Rhode Island,South Carolina,South Dakota,Tennessee,Texas,Utah,Vermont,Virginia,Washington,West Virginia,Wisconsin,Wyoming') . "','"
              . $wpdb->escape('1') . "','"
              . $wpdb->escape('2') . "','"
              . $wpdb->escape('1') . "','"
              . $wpdb->escape('1') . "','"
              . $wpdb->escape('ClassiPress') . "','"
              . date_i18n("Y-m-d H:i:s") . "','"
              . date_i18n("Y-m-d H:i:s")
              . "'),"
              . "('"
              . $wpdb->escape($app_abbr.'_country'). "','"
              . $wpdb->escape('Country'). "','"
              . $wpdb->escape('This is the country drop-down select box for the ad. It is a core ClassiPress field and cannot be deleted.'). "','"
              . $wpdb->escape('drop-down') . "','"
              . $wpdb->escape('United States,United Kingdom,Afghanistan,Albania,Algeria,American Samoa,Angola,Anguilla,Antartica,Antigua and Barbuda,Argentina,Armenia,Aruba,Ashmore and Cartier Island,Australia,Austria,Azerbaijan,Bahamas,Bahrain,Bangladesh,Barbados,Belarus,Belgium,Belize,Benin,Bermuda,Bhutan,Bolivia,Bosnia and Herzegovina,Botswana,Brazil,British Virgin Islands,Brunei,Bulgaria,Burkina Faso,Burma,Burundi,Cambodia,Cameroon,Canada,Cape Verde,Cayman Islands,Central African Republic,Chad,Chile,China,Christmas Island,Colombia,Comoros,Congo,Cook Islands,Costa Rica,Cote dIvoire,Croatia,Cuba,Cyprus,Czeck Republic,Denmark,Djibouti,Dominica,Dominican Republic,Ecuador,Egypt,El Salvador,Equatorial Guinea,Eritrea,Estonia,Ethiopia,Europa Island,Falkland Islands,Faroe Islands,Fiji,Finland,France,French Guiana,French Polynesia,French Southern and Antarctic Lands,Gabon,Gambia,Gaza Strip,Georgia,Germany,Ghana,Gibraltar,Glorioso Islands,Greece,Greenland,Grenada,Guadeloupe,Guam,Guatemala,Guernsey,Guinea,Guinea-Bissau,Guyana,Haiti,Heard Island and McDonald Islands,Honduras,Hong Kong,Howland Island,Hungary,Iceland,India,Indonesia,Iran,Iraq,Ireland,Ireland Northern,Isle of Man,Israel,Italy,Jamaica,Jan Mayen,Japan,Jarvis Island,Jersey,Johnston Atoll,Jordan,Juan de Nova Island,Kazakhstan,Kenya,Kiribati,Korea North,Korea South,Kuwait,Kyrgyzstan,Laos,Latvia,Lebanon,Lesotho,Liberia,Libya,Liechtenstein,Lithuania,Luxembourg,Macau,Macedonia,Madagascar,Malawi,Malaysia,Maldives,Mali,Malta,Marshall Islands,Martinique,Mauritania,Mauritius,Mayotte,Mexico,Micronesia,Midway Islands,Moldova,Monaco,Mongolia,Montserrat,Morocco,Mozambique,Namibia,Nauru,Nepal,Netherlands,Netherlands Antilles,New Caledonia,New Zealand,Nicaragua,Niger,Nigeria,Niue,Norfolk Island,Northern Mariana Islands,Norway,Oman,Pakistan,Palau,Panama,Papua New Guinea,Paraguay,Peru,Philippines,Pitcaim Islands,Poland,Portugal,Puerto Rico,Qatar,Reunion,Romainia,Russia,Rwanda,Saint Helena,Saint Kitts and Nevis,Saint Lucia,Saint Pierre and Miquelon,Saint Vincent and the Grenadines,Samoa,San Marino,Sao Tome and Principe,Saudi Arabia,Scotland,Senegal,Seychelles,Sierra Leone,Singapore,Slovakia,Slovenia,Solomon Islands,Somalia,South Africa,South Georgia,Spain,Spratly Islands,Sri Lanka,Sudan,Suriname,Svalbard,Swaziland,Sweden,Switzerland,Syria,Taiwan,Tajikistan,Tanzania,Thailand,Tobago,Toga,Tokelau,Tonga,Trinidad,Tunisia,Turkey,Turkmenistan,Tuvalu,Uganda,Ukraine,United Arab Emirates,United Kingdom,United States,Uruguay,Uzbekistan,Vanuatu,Vatican City,Venezuela,Vietnam,Virgin Islands,Wales,Wallis and Futuna,West Bank,Western Sahara,Yemen,Yugoslavia,Zambia,Zimbabwe') . "','"
              . $wpdb->escape('1') . "','"
              . $wpdb->escape('2') . "','"
              . $wpdb->escape('1') . "','"
              . $wpdb->escape('1') . "','"
              . $wpdb->escape('ClassiPress') . "','"
              . date_i18n("Y-m-d H:i:s") . "','"
              . date_i18n("Y-m-d H:i:s")
              . "'),"
              . "('"
              . $wpdb->escape($app_abbr.'_zipcode'). "','"
              . $wpdb->escape('Zip/Postal Code'). "','"
              . $wpdb->escape('This is the zip/postal code text field. It is a core ClassiPress field and cannot be deleted. (Needed on your forms for Google maps to work best.)'). "','"
              . $wpdb->escape('text box') . "','"
              . $wpdb->escape('') . "','"
              . $wpdb->escape('') . "','"
              . $wpdb->escape('2') . "','"
              . $wpdb->escape('1') . "','"
              . $wpdb->escape('') . "','"
              . $wpdb->escape('ClassiPress') . "','"
              . date_i18n("Y-m-d H:i:s") . "','"
              . date_i18n("Y-m-d H:i:s")
              . "'),"
              . "('"
              . $wpdb->escape('tags_input'). "','"
              . $wpdb->escape('Tags'). "','"
              . $wpdb->escape('This is for inputting tags for the ad. It is a core ClassiPress field and cannot be deleted.'). "','"
              . $wpdb->escape('text box') . "','"
              . $wpdb->escape('') . "','"
              . $wpdb->escape('') . "','"
              . $wpdb->escape('2') . "','"
              . $wpdb->escape('1') . "','"
              . $wpdb->escape('') . "','"
              . $wpdb->escape('ClassiPress') . "','"
              . date_i18n("Y-m-d H:i:s") . "','"
              . date_i18n("Y-m-d H:i:s")
              . "'),"
              . "('"
              . $wpdb->escape('post_content'). "','"
              . $wpdb->escape('Description'). "','"
              . $wpdb->escape('This is the main description box for the ad. It is a core ClassiPress field and cannot be deleted.'). "','"
              . $wpdb->escape('text area') . "','"
              . $wpdb->escape('') . "','"
              . $wpdb->escape('') . "','"
              . $wpdb->escape('1') . "','"
              . $wpdb->escape('1') . "','"
              . $wpdb->escape('1') . "','"
              . $wpdb->escape('ClassiPress') . "','"
              . date_i18n("Y-m-d H:i:s") . "','"
              . date_i18n("Y-m-d H:i:s")
              . "'),"
              . "('"
              . $wpdb->escape($app_abbr.'_region'). "','"
              . $wpdb->escape('Region'). "','"
              . $wpdb->escape('This is the region drop-down select box for the ad.'). "','"
              . $wpdb->escape('drop-down') . "','"
              . $wpdb->escape('San Francisco Bay Area,Orange County,Central Valley,Northern CA,Southern CA') . "','"
              . $wpdb->escape('1') . "','"
              . $wpdb->escape('2') . "','"
              . $wpdb->escape('') . "','"
              . $wpdb->escape('') . "','"
              . $wpdb->escape('ClassiPress') . "','"
              . date_i18n("Y-m-d H:i:s") . "','"
              . date_i18n("Y-m-d H:i:s")
              . "'),"
              . "('"
              . $wpdb->escape($app_abbr.'_size'). "','"
              . $wpdb->escape('Size'). "','"
              . $wpdb->escape('This is an example of a custom drop-down field.'). "','"
              . $wpdb->escape('drop-down') . "','"
              . $wpdb->escape('XS,S,M,L,XL,XXL') . "','"
              . $wpdb->escape('') . "','"
              . $wpdb->escape('') . "','"
              . $wpdb->escape('') . "','"
              . $wpdb->escape('') . "','"
              . $wpdb->escape('ClassiPress') . "','"
              . date_i18n("Y-m-d H:i:s") . "','"
              . date_i18n("Y-m-d H:i:s")
              . "'),"
              . "('"
              . $wpdb->escape($app_abbr.'_feedback'). "','"
              . $wpdb->escape('Feedback'). "','"
              . $wpdb->escape('This is an example of a custom text area field.'). "','"
              . $wpdb->escape('text area') . "','"
              . $wpdb->escape('') . "','"
              . $wpdb->escape('') . "','"
              . $wpdb->escape('') . "','"
              . $wpdb->escape('') . "','"
              . $wpdb->escape('') . "','"  
              . $wpdb->escape('ClassiPress') . "','"
              . date_i18n("Y-m-d H:i:s") . "','"
              . date_i18n("Y-m-d H:i:s")
              . "')";

        $wpdb->query($insert);

    }


    
    // Check to see if any rows already exist. If so, don't insert any data
    $sql = "SELECT pack_id "
         . "FROM " . $wpdb->prefix . "cp_ad_packs LIMIT 1";

    $wpdb->get_results($sql);

    if($wpdb->num_rows == 0) {

        $insert = "INSERT INTO " . $wpdb->prefix . "cp_ad_packs" .
            " (pack_name, pack_desc, pack_price, pack_duration, pack_images, pack_status, pack_owner, pack_created, pack_modified) " .
            "VALUES ('"
              . $wpdb->escape('30 days for only $5'). "','"
              . $wpdb->escape('This is the default price per ad package created by ClassiPress.'). "','"
              . $wpdb->escape('5'). "','"
              . $wpdb->escape('30'). "','"
              . $wpdb->escape('3') . "','"
              . $wpdb->escape('active') . "','"
              . $wpdb->escape('ClassiPress') . "','"
              . date_i18n("Y-m-d H:i:s") . "','"
              . date_i18n("Y-m-d H:i:s")
              . "')";

        $wpdb->query($insert);

    }
 

 }


function cp_default_values() {
   global $wpdb, $app_version, $app_edition, $app_abbr;

	/////////////////////////////////////////////////////////
	// insert default values if they don't already exist.
	////////////////////////////////////////////////////////

        
		// check to see if there's an existing version. If not, then set to current version so the next step works
		if(get_option($app_abbr.'_version') == false) update_option($app_abbr.'_version', $app_version);

		// move existing version to another variable. used for upgrade tool
		update_option($app_abbr.'_version_old', get_option($app_abbr.'_version'));

		// if not using legacy ads, then make ad width 100% full with class
		if(get_option($app_abbr.'_version_old') < '3.0.5')
			if(get_option($app_abbr.'_ad_right_class') == false) update_option($app_abbr.'_ad_right_class', ''); // blank on purpose
		else
			if(get_option($app_abbr.'_ad_right_class') == false) update_option($app_abbr.'_ad_right_class', 'full');

		// set the version number & edition type
        update_option($app_abbr.'_version', $app_version);
        update_option($app_abbr.'_edition', $app_edition);

        // set the media image sizes
        // thumbnail size
        update_option('thumbnail_size_w', 50);
        update_option('thumbnail_size_h', 50);

        // uncheck the crop thumbnail image checkbox
        delete_option('thumbnail_crop');

        // medium size
        update_option('medium_size_w', 200);
        update_option('medium_size_h', 150);

        // large size
        update_option('large_size_w', 500);
        update_option('large_size_h', 500);
        
        // set the default new WP user role only if it's currently subscriber
        if(get_option('default_role') == 'subscriber') update_option('default_role', 'contributor');

        // check the "membership" box to enable wordpress registration
        if(get_option('users_can_register') == 0) update_option('users_can_register', 1);


        // SETTINGS PAGE OPTIONS
        // uncomment this if you want to automatically set permalinks for them
        // if(get_option('permalink_structure') == false) update_option('permalink_structure', '/%postname%/');

        // home page layout
        if(get_option($app_abbr.'_home_layout') == false) update_option($app_abbr.'_home_layout', 'directory');
        if(get_option($app_abbr.'_stylesheet') == false) update_option($app_abbr.'_stylesheet', 'red.css');

        // turn on the blog feature
        if(get_option($app_abbr.'_enable_blog') == false) update_option($app_abbr.'_enable_blog', 'yes');
        if(get_option($app_abbr.'_use_logo') == false) update_option($app_abbr.'_use_logo', 'yes');

		// turn on the coupon module
        if(get_option($app_abbr.'_enable_coupons') == false) update_option($app_abbr.'_enable_coupons', 'yes');

        if(get_option($app_abbr.'_gmaps_loc') == false) update_option($app_abbr.'_gmaps_loc', 'http://maps.google.com');
        if(get_option($app_abbr.'_gmaps_encode') == false) update_option($app_abbr.'_gmaps_encode', 'utf-8');

        // security settings
        if(get_option($app_abbr.'_admin_security') == false) update_option($app_abbr.'_admin_security', 'read');

        // search settings
        if(get_option($app_abbr.'_search_ex_pages') == false) update_option($app_abbr.'_search_ex_pages', 'yes');
        if(get_option($app_abbr.'_search_ex_blog') == false) update_option($app_abbr.'_search_ex_blog', 'yes');
		if(get_option($app_abbr.'_search_depth') == false) update_option($app_abbr.'_search_depth', 0);

		// category settings
		if(get_option($app_abbr.'_cat_parent_count') == false) update_option($app_abbr.'_cat_parent_count', 1);
		if(get_option($app_abbr.'_cat_child_count') == false) update_option($app_abbr.'_cat_child_count', 0);
		if(get_option($app_abbr.'_cat_hide_empty') == false) update_option($app_abbr.'_cat_hide_empty', 0);
		if(get_option($app_abbr.'_cat_hierarchy') == false) update_option($app_abbr.'_cat_hierarchy', 1);
		if(get_option($app_abbr.'_cat_orderby') == false) update_option($app_abbr.'_cat_orderby', 'name');

		// image upload settings
        if(get_option($app_abbr.'_ad_images') == false) update_option($app_abbr.'_ad_images', 'yes');
		if(get_option($app_abbr.'_ad_image_preview') == false) update_option($app_abbr.'_ad_image_preview', 'yes');
		if(get_option($app_abbr.'_num_images') == false) update_option($app_abbr.'_num_images', 3);
		if(get_option($app_abbr.'_max_image_size') == false) update_option($app_abbr.'_max_image_size', 500);
        if(get_option($app_abbr.'_tim_thumb_zc') == false) update_option($app_abbr.'_tim_thumb_zc', 0);

        // set the category drop-down options
        if(get_option($app_abbr.'_cat_menu_cols') == false) update_option($app_abbr.'_cat_menu_cols', 3);
        if(get_option($app_abbr.'_cat_menu_sub_num') == false) update_option($app_abbr.'_cat_menu_sub_num', 5);

        if(get_option($app_abbr.'_cat_dir_cols') == false) update_option($app_abbr.'_cat_dir_cols', 3);
        if(get_option($app_abbr.'_dir_sub_num') == false) update_option($app_abbr.'_dir_sub_num', 10);

        // allow poster to edit post after posting
        if(get_option($app_abbr.'_ad_edit') == false) update_option($app_abbr.'_ad_edit', 'yes');

        // don't require visitors to be logged in to ask a question
        if(get_option($app_abbr.'_ad_inquiry_form') == false) update_option($app_abbr.'_ad_inquiry_form', 'no');        

        // enable tinymce editor
        if(get_option($app_abbr.'_allow_html') == false) update_option($app_abbr.'_allow_html', 'no');
        
        // allow ads to be renewed
        if(get_option($app_abbr.'_allow_relist') == false) update_option($app_abbr.'_allow_relist', 'yes');

		// disable by default
		if(get_option($app_abbr.'_ad_stats_all') == false) update_option($app_abbr.'_ad_stats_all', 'no');

		// turn off debug mode
		if(get_option($app_abbr.'_debug_mode') == false) update_option($app_abbr.'_debug_mode', 'no');

		// cache flush rate
		if(get_option($app_abbr.'_cache_expires') == false) update_option($app_abbr.'_cache_expires', '3600');

		// turn off gravatar options
		if(get_option($app_abbr.'_use_hovercards') == false) update_option($app_abbr.'_use_hovercards', 'no');
		if(get_option($app_abbr.'_ad_gravatar_thumb') == false) update_option($app_abbr.'_ad_gravatar_thumb', 'no');

		// turn off Google CDN
		if(get_option($app_abbr.'_google_jquery') == false) update_option($app_abbr.'_google_jquery', 'no');

        // new ad status
        if(get_option($app_abbr.'_post_status') == false) update_option($app_abbr.'_post_status', 'pending');

        // set default prune status values
        if(get_option($app_abbr.'_post_prune') == false) update_option($app_abbr.'_post_prune', 'no');
        if(get_option($app_abbr.'_prun_period') == false) update_option($app_abbr.'_prun_period', 90);
        
        // enable new ad notification emails
        if(get_option($app_abbr.'_new_ad_email') == false) update_option($app_abbr.'_new_ad_email', 'yes');
        if(get_option($app_abbr.'_new_ad_email_owner') == false) update_option($app_abbr.'_new_ad_email_owner', 'yes');
        if(get_option($app_abbr.'_expired_ad_email_owner') == false) update_option($app_abbr.'_expired_ad_email_owner', 'yes');
        if(get_option($app_abbr.'_custom_email_header') == false) update_option($app_abbr.'_custom_email_header', 'yes');
        if(get_option($app_abbr.'_nu_admin_email') == false) update_option($app_abbr.'_nu_admin_email', 'yes');

        // set default new user registration email values
        if(get_option($app_abbr.'_nu_custom_email') == false) update_option($app_abbr.'_nu_custom_email', 'no');
        if(get_option($app_abbr.'_nu_from_name') == false) update_option($app_abbr.'_nu_from_name', wp_specialchars_decode(get_option('blogname'), ENT_QUOTES));
        if(get_option($app_abbr.'_nu_from_email') == false) update_option($app_abbr.'_nu_from_email', get_option('admin_email'));
        if(get_option($app_abbr.'_nu_email_subject') == false) update_option($app_abbr.'_nu_email_subject', 'Thank you for registering, %username%');
        if(get_option($app_abbr.'_nu_email_type') == false) update_option($app_abbr.'_nu_email_type', 'text/plain');

        if(get_option($app_abbr.'_nu_email_body') == false) update_option($app_abbr.'_nu_email_body', '
Hi %username%,

Welcome to %blogname%, your leader in online classified ad listings.

Below you will find your username and password which allows you to login to your user account and being posting classified ads.

--------------------------
Username: %username%
Password: %password%

%loginurl%
--------------------------

If you have any questions, please just let us know.

Best regards,


Your %blogname% Team
%siteurl%		
');


        // home page sidebar welcome default message
        if(get_option($app_abbr.'_ads_welcome_msg') == false) { update_option($app_abbr.'_ads_welcome_msg', '
<h2 class="colour_top">Welcome to our Web site!</h2>

<h1><span class="colour">List Your Classified Ads</span></h1>

<p>We are your #1 classified ad listing site. Become a free member and start listing your classified ads within minutes. Manage all ads from your personalized dashboard.</p>'
); }

        // load in the ad form default message
        if(get_option($app_abbr.'_ads_form_msg') == false) { update_option($app_abbr.'_ads_form_msg', '
<p>Please fill in the fields below to post your classified ad. Required fields are denoted by a *. You will be given the opportunity to review your ad before it is posted.</p>

<p>Neque porro quisquam est qui dolorem rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit asp.</p>

<p><em><span class="colour">Rui nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquaear diff, and optional ceramic brake rotors can now all be orchestrated al fresco.</span></em></p>

<p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunsciunt. Neque porro quisquam est, qui dolorem ipsum.</p>
'
        );}

        // load in the ad form terms of use message
        if(get_option($app_abbr.'_ads_tou_msg') == false) { update_option($app_abbr.'_ads_tou_msg', '
<h3>RULES AND GUIDELINES</h3>
By posting your classified ad here, you agree that it is in compliance with our guidelines listed below.<br/><br/>

We reserve the right to modify any ads in violation of our guidelines order to prevent abuse and keep the content appropriate for our general audience. This includes people of all ages, races, religions, and nationalities. Therefore, all ads that are in violation of our guidelines are subject to being removed immediately and without prior notice.<br/><br/>

By posting an ad on our site, you agree to the following statement:<br/><br/>

I agree that I will be solely responsible for the content of any classified ads that I post on this website. I will not hold the owner of this website responsible for any losses or damages to myself or to others that may result directly or indirectly from any ads that I post here.<br/><br/>

By posting an ad on our site, you further agree to the following guidelines:<br/><br/>

<ol>
   <li>No foul or otherwise inappropriate language will be tolerated. Ads in violation of this rule are subject to being removed immediately and without warning. If it was a paid ad, no refund will be issues.</li>
   <li>No racist, hateful, or otherwise offensive comments will be tolerated.</li>
   <li>No ad promoting activities that are illegal under the current laws of this state or country.</li>
   <li>Any ad that appears to be merely a test posting, a joke, or otherwise insincere or non-serious is subject to removal.</li>
   <li>We reserve the ultimate discretion as to which ads, if any, are in violation of these guidelines.</li>
</ol>
<br/><br/>
Thank you for your understanding.<br/><br/>'
        );}

        // set the default ad spots. Show the appthemes banner as the default
        if(get_option($app_abbr.'_adcode_468x60_enable') == false) update_option($app_abbr.'_adcode_468x60_enable', 'yes');
        if(get_option($app_abbr.'_adcode_336x280_enable') == false) update_option($app_abbr.'_adcode_336x280_enable', 'no');
        
        //PRUNE ADS SETTINGS (CRON JOB)
        if(get_option($app_abbr.'_prune_ads_email') == false) update_option($app_abbr.'_prune_ads_email', 'no');
        if(get_option($app_abbr.'_ad_expired_check_recurrance') == false) update_option($app_abbr.'_ad_expired_check_recurrance', 'daily');


        // PRICING SETTINGS PAGE

        if(get_option($app_abbr.'_charge_ads') == false) update_option($app_abbr.'_charge_ads', 'no');
        if(get_option($app_abbr.'_enable_featured') == false) update_option($app_abbr.'_enable_featured', 'yes');
        if(get_option($app_abbr.'_featured_trim') == false) update_option($app_abbr.'_featured_trim', 30);
        if(get_option($app_abbr.'_payment_email') == false) update_option($app_abbr.'_payment_email', 'yes');

        // set the default pricing and currency values
        if(get_option($app_abbr.'_curr_symbol') == false) update_option($app_abbr.'_curr_symbol', '$');
        if(get_option($app_abbr.'_curr_symbol_pos') == false) update_option($app_abbr.'_curr_symbol_pos', 'left');
        if(get_option($app_abbr.'_curr_pay_type') == false) update_option($app_abbr.'_curr_pay_type', 'USD');
        if(get_option($app_abbr.'_price_scheme') == false) update_option($app_abbr.'_price_scheme', 'single');
        if(get_option($app_abbr.'_price_per_ad') == false) update_option($app_abbr.'_price_per_ad', 5);

        // set the default price per ad equal to 5 dollars
        if(get_option($app_abbr.'_ad_value') == false) update_option($app_abbr.'_ad_value', 5);

		// advanced misc options
		if(get_option($app_abbr.'_search_field_width') == false) update_option($app_abbr.'_search_field_width', '450px');
        if(get_option($app_abbr.'_table_width') == false) update_option($app_abbr.'_table_width', '850px');
		

        // important default setting path variables
        if(get_option($app_abbr.'_dashboard_url') == false) update_option($app_abbr.'_dashboard_url', 'dashboard');
        if(get_option($app_abbr.'_profile_url') == false) update_option($app_abbr.'_profile_url', 'profile');
        if(get_option($app_abbr.'_edit_item_url') == false) update_option($app_abbr.'_edit_item_url', 'edit-item');
        if(get_option($app_abbr.'_add_new_url') == false) update_option($app_abbr.'_add_new_url', 'add-new');
        if(get_option($app_abbr.'_add_new_confirm_url') == false) update_option($app_abbr.'_add_new_confirm_url', 'add-new-confirm');
        if(get_option($app_abbr.'_author_url') == false) update_option($app_abbr.'_author_url', 'author');
        if(get_option($app_abbr.'_post_type_permalink') == false) update_option($app_abbr.'_post_type_permalink', 'ads');

        if(get_option($app_abbr.'_ad_cat_tax_permalink') == false) update_option($app_abbr.'_ad_cat_tax_permalink', 'ad-category');
        if(get_option($app_abbr.'_ad_tag_tax_permalink') == false) update_option($app_abbr.'_ad_tag_tax_permalink', 'ad-tag');


        
        
        // GATEWAYS SETTINGS PAGE

        // ad payment settings        
        if(get_option($app_abbr.'_enable_paypal') == false) update_option($app_abbr.'_enable_paypal', 'yes');
        if(get_option($app_abbr.'_paypal_email') == false) update_option($app_abbr.'_paypal_email', 'youremail@domain.com');
        if(get_option($app_abbr.'_enable_paypal_ipn') == false) update_option($app_abbr.'_enable_paypal_ipn', 'no');
		
        // try and set the correct permalink structure
        //    if(get_option('permalink_structure') == '')
        //    update_option('permalink_structure', '/%postname%/');
        //    $wp_rewrite->set_permalink_structure('/%postname%/');
		//	  add_action('admin_init', 'flush_rewrite_rules');  // useful for later
		
        // reCaptcha default values
        if(get_option($app_abbr.'_captcha_enable') == false) update_option($app_abbr.'_captcha_enable', 'no');
        if(get_option($app_abbr.'_captcha_theme') == false) update_option($app_abbr.'_captcha_theme', 'red');

        // cufon font replacement default values
        if(get_option($app_abbr.'_cufon_enable') == false) update_option($app_abbr.'_cufon_enable', 'no');
        if(get_option($app_abbr.'_cufon_code') == false) update_option($app_abbr.'_cufon_code', stripslashes("Cufon.replace('.content_right h2.dotted', { fontFamily: 'Liberation Serif', textShadow:'0 1px 0 #FFFFFF' });"));

		//user photo plugin default options to work with ClassiPress
        update_option('userphoto_maximum_dimension', '60');
		update_option('userphoto_thumb_dimension', '16');
}


// Create a default blogroll link
function cp_insert_links() {
	global $wpdb, $app_abbr;

	// first check and make sure this link doesn't already exist
	$wpdb->get_row("SELECT link_id FROM $wpdb->links WHERE link_url = 'http://appthemes.com/'");

    if($wpdb->num_rows == 0) {

		// get the correct link category id
		$results = $wpdb->get_row("SELECT term_taxonomy_id FROM $wpdb->term_taxonomy WHERE taxonomy = 'link_category' LIMIT 1");

		if($results) $blogroll_id = $results->term_taxonomy_id; else $blogroll_id = '2';

		$default_links = array();
		$default_links[] = array('link_url' => 'http://appthemes.com/','link_name' => 'WordPress Premium Themes','link_rss' => '','link_notes' =>'');

		foreach ($default_links as $link) :
			$wpdb->insert($wpdb->links, $link);
			$wpdb->insert($wpdb->term_relationships, array('term_taxonomy_id' => $blogroll_id, 'object_id' => $wpdb->insert_id));
		endforeach;

	}
}



// Create the ClassiPress pages and assign the templates to them
function cp_create_pages() {
    global $wpdb, $app_abbr;

    $out = array();

    // first check and make sure this page doesn't already exist
    $sql = "SELECT ID FROM " . $wpdb->posts . " WHERE post_name = 'dashboard' LIMIT 1";

    $wpdb->get_results($sql);

    if($wpdb->num_rows == 0) {

        // first create the dashboard page
        $my_page = array(
        'post_status' => 'publish',
        'post_type' => 'page',
        'post_author' => 1,
        'post_name' => 'dashboard',
        'post_title' => 'Dashboard'
        );

        // Insert the page into the database
        $page_id = wp_insert_post($my_page);

        // Assign the page template to the new page
        update_post_meta($page_id, '_wp_page_template', 'tpl-dashboard.php');

        $out[] = $page_id;

    }


    // first check and make sure this page doesn't already exist
    $sql = "SELECT ID FROM " . $wpdb->posts . " WHERE post_name = 'profile' LIMIT 1";

    $wpdb->get_results($sql);

    if($wpdb->num_rows == 0) {

        // next create the profile page
        $my_page = array(
        'post_status' => 'publish',
        'post_type' => 'page',
        'post_author' => 1,
        'post_name' => 'profile',
        'post_title' => 'Profile'
        );

        // Insert the page into the database
        $page_id = wp_insert_post($my_page);

        // Assign the page template to the new page
        update_post_meta($page_id, '_wp_page_template', 'tpl-profile.php');

        $out[] = $page_id;

    }



    // first check and make sure this page doesn't already exist
    $sql = "SELECT ID FROM " . $wpdb->posts . " WHERE post_name = 'edit-item' LIMIT 1";

    $wpdb->get_results($sql);

    if($wpdb->num_rows == 0) {

        // then create the edit item page
        $my_page = array(
        'post_status' => 'publish',
        'post_type' => 'page',
        'post_author' => 1,
        'post_name' => 'edit-item',
        'post_title' => 'Edit Item'
        );

        // Insert the page into the database
        $page_id = wp_insert_post($my_page);

        // Assign the page template to the new page
        update_post_meta($page_id, '_wp_page_template', 'tpl-edit-item.php');

        $out[] = $page_id;

    }



    // first check and make sure this page doesn't already exist
    $sql = "SELECT ID FROM " . $wpdb->posts . " WHERE post_name = 'add-new' LIMIT 1";

    $wpdb->get_results($sql);

    if($wpdb->num_rows == 0) {

        // then create the edit item page
        $my_page = array(
        'post_status' => 'publish',
        'post_type' => 'page',
        'post_author' => 1,
        'post_name' => 'add-new',
        'post_title' => 'Add New'
        );

        // Insert the page into the database
        $page_id = wp_insert_post($my_page);

        // Assign the page template to the new page
        update_post_meta($page_id, '_wp_page_template', 'tpl-add-new.php');

        $out[] = $page_id;

    }


    // first check and make sure this page doesn't already exist
    $sql = "SELECT ID FROM " . $wpdb->posts . " WHERE post_name = 'add-new-confirm' LIMIT 1";

    $wpdb->get_results($sql);

    if($wpdb->num_rows == 0) {

        // then create the edit item page
        $my_page = array(
        'post_status' => 'publish',
        'post_type' => 'page',
        'post_author' => 1,
        'post_name' => 'add-new-confirm',
        'post_title' => 'Add New Confirm'
        );

        // Insert the page into the database
        $page_id = wp_insert_post($my_page);

        // Assign the page template to the new page
        update_post_meta($page_id, '_wp_page_template', 'tpl-add-new-confirm.php');

        $out[] = $page_id;

    }


    // first check and make sure this page doesn't already exist
    $sql = "SELECT ID FROM " . $wpdb->posts . " WHERE post_name = 'blog' LIMIT 1";

    $wpdb->get_results($sql);

    if($wpdb->num_rows == 0) {

        // then create the edit item page
        $my_page = array(
        'post_status' => 'publish',
        'post_type' => 'page',
        'post_author' => 1,
        'post_name' => 'blog',
        'post_title' => 'Blog'
        );

        // Insert the page into the database
        $page_id = wp_insert_post($my_page);

        // Assign the page template to the new page
        update_post_meta($page_id, '_wp_page_template', 'tpl-blog.php');

		// the blog page is different since we don't want to exclude it.
		// instead we need to insert the blog page id into an option field
		// it's used for pulling values into the breadcrumb
		if(get_option($app_abbr.'_blog_page_id') == false) update_option($app_abbr.'_blog_page_id', $page_id);

    }


    // check to see if array of page ids is empty
    // if not, add them to the pages to be excluded
    // from the nav meta option.
    if (!empty($out)) {
        
        // take the array and put elements into a comma separated string
        $exclude_pages = implode(',', $out);

        // now insert the excluded pages meta option and the values if the option doesn't already exist
        if(get_option($app_abbr.'_excluded_pages') == false)
            update_option($app_abbr.'_excluded_pages', appthemes_clean($exclude_pages));

    }

}




// create the blog category if it doesn't already exist
//function cp_create_cats() {
//    global $wpdb, $app_abbr;
//
//    // first check and make sure this category doesn't already exist
//    $sql = "SELECT term_id FROM " . $wpdb->terms . " WHERE slug = 'blog' LIMIT 1";
//
//    $results = $wpdb->get_row($sql);
//
//    if($wpdb->num_rows == 0) {
//
//        $my_cat = array(
//            'alias_of' => '',
//            'description' => __('This category was automatically created by ClassiPress. All your blog posts should be assigned to this category or a sub-category under blog.','appthemes'),
//            'parent' => 0,
//            'slug' => 'blog'
//        );
//
//        // create the category
//        $my_cat_id = wp_insert_term('Blog', 'category', $my_cat);
//
//        // now insert the blog category into a CP meta option so we know where the blog lives
//        if(get_option($app_abbr.'_blog_cat') == false)
//            update_option($app_abbr.'_blog_cat', appthemes_clean($my_cat_id['term_id']));
//
//    } else {
//        // the blog category already exists so put the id into the cp field
//        update_option($app_abbr.'_blog_cat', $results->term_id);
//    }
//
//}



?>
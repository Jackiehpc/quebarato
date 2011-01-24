<?php

if (!is_admin()) {
   wp_register_script('custom_script',    get_stylesheet_directory_uri() . '/pagamentodigital.js',     array('jquery'),  '1.0' );
   wp_enqueue_script('custom_script');
}




function get_number_price($postid){

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

	$price = $price_out;
	
	
	// $price =  explode('', $price);
	$rt_price = "";
	for( $i = 0 ; $i  < strlen($price); $i++){
		$char = $price[$i];
		$char = $char == ',' ? '.'  : $char; 
		if(is_numeric($char) || $char == '.'){
			$rt_price.= $char;	
		}
	}

	return $rt_price;
};

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
                        
                        if(preg_match("/(pagamentodigital)/", $result->field_name))
							continue;
                        
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
                        if(preg_match("/(pagamentodigital)/", $result->field_name))
							continue;
                        
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
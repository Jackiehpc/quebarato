<?php

// include all the core admin ClassiPress files
require_once ('admin-values.php');
require_once ('admin-notices.php');
require_once ('admin-addons.php');
require_once ('admin-updates.php');


// update all the admin options on save
function cp_update_options($options) {

    if(isset($_POST['submitted']) && $_POST['submitted'] == 'yes') {

            foreach ($options as $value) {

                if(isset($_POST[$value['id']])) {
                    // echo $value['id'] . '<-- value ID | ' . $_POST[$value['id']] . '<-- $_POST value ID <br/><br/>'; // FOR DEBUGGING
                    update_option($value['id'], appthemes_clean($_POST[$value['id']]));
                } else {
                    @delete_option($value['id']);
                }
            }

            // do a separate update for price per cats since it's not in the $options array
            if(isset($_POST['catarray'])) {
                foreach ($_POST['catarray'] as $key => $value) {
                    // echo $key .'<-- key '. $value .'<-- value<br/>'; // FOR DEBUGGING
                    update_option($key, appthemes_clean($value));
                }
            }
			
			if(get_option('cp_tools_run_expiredcheck') == 'yes') {
					update_option('cp_tools_run_expiredcheck', 'no');
					cp_check_expired_cron();
					$toolsMessage .= __('Ads Expired Check was executed.');
			}

			// flush out the cache so changes can be visible
			cp_flush_all_cache();

            echo '<div id="message" class="updated fade"><p><strong>'.__('Your settings have been saved.','appthemes').'</strong> ' . $toolsMessage . '</p></div>';

    }
	
    elseif(isset($_POST['submitted']) && $_POST['submitted'] == 'convertToCustomPostType') {
		update_option('cp_tools_run_convertToCustomPostType', 'no');
		$toolsMessage .= cp_convert_posts2Ads();

		echo $toolsMessage;

	}
}

// creates the category checklist box
function cp_category_checklist($checkedcats, $exclude = '') {
	if (empty($walker) || !is_a($walker, 'Walker'))
		$walker = new Walker_Category_Checklist;

	$args = array();
        if (is_array( $checkedcats ))
            $args['selected_cats'] = $checkedcats;
	else
            $args['selected_cats'] = array();

        $args['popular_cats'] = array();
        $categories = get_categories(array('hide_empty' => 0,
										   'taxonomy' 	=> 'ad_cat',
										   'exclude' 	=> $exclude));

	return call_user_func_array(array(&$walker, 'walk'), array($categories, 0, $args));
}

// this grabs the cats that should be excluded
function cp_exclude_cats ($id = NULL) {
    global $wpdb;

    $output = array();

    if ($id) {
        $sql = "SELECT form_cats FROM ". $wpdb->prefix ."cp_ad_forms WHERE id != '". $id ."'";
    } else {
        $sql = "SELECT form_cats FROM ". $wpdb->prefix ."cp_ad_forms";
    }

    $records = $wpdb->get_results($sql);

    if($records) {

        foreach ($records as $record){

            $output[] = implode(',',unserialize($record->form_cats));

        }

     }

    $exclude = cp_unique_str(',', (join( ',', $output )));

    return $exclude;
}

// find a category match and then output it
function cp_match_cats($form_cats) {
    global $wpdb;

    $out = array();

    if($form_cats) {

        foreach($form_cats as $key => $value) {

            $sqlcats = "SELECT * FROM " . $wpdb->terms . " WHERE term_id=$value";
            $cats = $wpdb->get_row($sqlcats);

            $out[] = '<a href="edit-tags.php?action=edit&taxonomy=ad_cat&post_type=ad_listing&tag_ID='.$cats->term_id.'">'.$cats->name.'</a>';

        }

    }
        return join( ', ', $out );
}


function cp_unique_str($separator, $str) {

    $str_arr = explode($separator, $str);
    $result = array_unique($str_arr);
    $unique_str = implode(',', $result);

    return $unique_str;
}


/**
* Take field input label value and make custom name
* Strip out everything excepts chars & numbers
* Used for WP custom field name i.e. Middle Name = cp_middle_name
*/
function cp_make_custom_name($cname) {

	$cname = preg_replace('/[^a-zA-Z0-9\s]/', '', $cname);
	$cname = 'cp_' . str_replace(' ', '_', strtolower(substr(appthemes_clean($cname), 0, 30)));

	return $cname;
}

// delete the custom form and the meta custom field data
function cp_delete_form($form_id) {
    global $wpdb;

	$delete = "DELETE FROM " . $wpdb->prefix . "cp_ad_forms "
			. "WHERE id = '$form_id'";

	$wpdb->query($delete);

	$delete = "DELETE FROM " . $wpdb->prefix . "cp_ad_meta "
			. "WHERE form_id = '$form_id'";

	$wpdb->query($delete);
}


function cp_admin_formbuilder($results) {
	global $wpdb;

	foreach ($results as $result):
	?>

		<tr class="even" id="<?php echo $result->meta_id; ?>"><!-- id needed for jquery sortable to work -->
			<td style="min-width:100px;"><?php echo $result->field_label; ?></td>
			<td>

		<?php

		switch($result->field_type) {

		case 'text box':
		?>

			<input name="<?php echo $result->field_name; ?>" type="text" style="min-width:200px;" value="" disabled />

		<?php
		break;

		case 'text area':

		?>

			<textarea rows="4" cols="23" disabled></textarea>

		<?php
		break;

		default: // used for drop-downs, radio buttons, and checkboxes
		?>

			<select name="dropdown">

			<?php
			$options = explode(',', $result->field_values);

			foreach ($options as $option) {
			?>

				<option style="min-width:177px" value="<?php echo $option; ?>" disabled><?php echo $option; ?></option>

			<?php
			}
			?>

			</select>

		<?php

		} //end switch
		?>

			</td>

			<td style="text-align:center;">


				<input type="checkbox" name="<?php echo $result->meta_id; ?>[field_req]" id="" <?php if($result->field_req) echo 'checked="yes"' ?> <?php if($result->field_req) echo 'checked="yes"' ?> <?php if ($result->field_perm == 1) { echo 'disabled="disabled"'; } ?> value="1" style="" />
				<?php if ($result->field_perm == 1) { ?>
					<input type="hidden" name="<?php echo $result->meta_id; ?>[field_req]" checked="yes" value="1" />
				<?php } ?>

			</td>
			<!-- // since v3.1. using ajax now for ordering fields
				<td style="text-align:center;"><select name="<?php echo $result->meta_id; ?>[field_pos]" style="min-width:50px;">

					<?php
					$i = '1';

					while ( $i <= count($results) ) {
					?>

							<option value="<?php echo $i; ?>" <?php if ( $result->field_pos == $i) { ?> selected="selected" <?php } ?>><?php echo $i; ?></option>

					<?php
							$i++;

					}
					?>

					</select>
				</td>
			-->
			<td style="text-align:center;">

				<input type="hidden" name="id[]" value="<?php echo $result->meta_id; ?>" />
				<input type="hidden" name="<?php echo $result->meta_id; ?>[id]" value="<?php echo $result->meta_id; ?>" />

				<?php if ($result->field_perm == 1) { ?>
				<img src="<?php bloginfo('template_directory'); ?>/images/remove-row-gray.png" alt="<?php  _e('Cannot remove from layout','appthemes') ?>" title="<?php  _e('Cannot remove from layout','appthemes') ?>" />
				<?php } else { ?>
				<a onclick="return confirmBeforeRemove();" href="?page=layouts&amp;action=formbuilder&amp;id=<?php echo $result->form_id ?>&amp;del_id=<?php echo $result->meta_id ?>&amp;title=<?php echo urlencode($_GET['title']) ?>"><img src="<?php bloginfo('template_directory'); ?>/images/remove-row.png" alt="<?php  _e('Remove from layout','appthemes') ?>" title="<?php  _e('Remove from layout','appthemes') ?>" /></a>
				<?php } ?>

			</td>
		</tr>

	<?php
	endforeach;

}

// this creates the default fields when a form layout is created
function cp_add_core_fields($form_id) {
	global $wpdb;

    // Check to see if any rows already exist for this form. If so, don't insert any data
    $sql = "SELECT form_id "
         . "FROM " . $wpdb->prefix . "cp_ad_meta "
         . "WHERE form_id  = '" . $form_id . "'";

    $wpdb->get_results($sql);


    if($wpdb->num_rows == 0) {

        $insert = "INSERT INTO " . $wpdb->prefix . "cp_ad_meta" .
        " (form_id, field_id, field_req, field_pos) " .
        "VALUES ('"
          . $wpdb->escape($form_id). "','"
          . $wpdb->escape('1'). "','" // post_title
          . $wpdb->escape('1'). "','"
          . $wpdb->escape('1')
          . "'),"
          . "('"
          . $wpdb->escape($form_id). "','"
          . $wpdb->escape('2'). "','" // cp_price
          . $wpdb->escape('1'). "','"
          . $wpdb->escape('2')
          . "'),"
          . "('"
          . $wpdb->escape($form_id). "','"
          . $wpdb->escape('3'). "','" // cp_street
          . $wpdb->escape('1'). "','"
          . $wpdb->escape('3')
          . "'),"
          . "('"
          . $wpdb->escape($form_id). "','"
          . $wpdb->escape('4'). "','" // cp_city
          . $wpdb->escape('1'). "','"
          . $wpdb->escape('4')
          . "'),"
          . "('"
          . $wpdb->escape($form_id). "','"
          . $wpdb->escape('5'). "','" // cp_state
          . $wpdb->escape('1'). "','"
          . $wpdb->escape('5')
          . "'),"
          . "('"
          . $wpdb->escape($form_id). "','"
          . $wpdb->escape('6'). "','" // cp_country
          . $wpdb->escape('1'). "','"
          . $wpdb->escape('6')
          . "'),"
          . "('"
          . $wpdb->escape($form_id). "','"
          . $wpdb->escape('7'). "','" // cp_zipcode
          . $wpdb->escape('1'). "','"
          . $wpdb->escape('7')
          . "'),"
          . "('"
          . $wpdb->escape($form_id). "','"
          . $wpdb->escape('8'). "','" // tags_input
          . $wpdb->escape('1'). "','"
          . $wpdb->escape('8')
          . "'),"
          . "('"
          . $wpdb->escape($form_id). "','"
          . $wpdb->escape('9'). "','" // post_content
          . $wpdb->escape('1'). "','"
          . $wpdb->escape('9')
          . "')";

        $results = $wpdb->query($insert);

    }
}


function cp_admin_db_fields($options, $cp_table, $cp_id) {
    global $wpdb;

    $sql = "SELECT * FROM ". $wpdb->prefix . $cp_table
         . " WHERE ". $cp_id ." = '". $_GET['id'] ."'";

    $results = $wpdb->get_row($sql);
    ?>

    <table class="widefat fixed" id="tblspacer" style="width:850px;">

    <?php

    foreach ($options as $value) {

      if($results) {

          // foreach ($results as $result):
          
          // check to prevent "Notice: Undefined property: stdClass::" error when php strict warnings is turned on
          if(!isset($results->field_type)) $field_type = ''; else $field_type = $results->field_type;
          if(!isset($results->field_perm)) $field_perm = ''; else $field_perm = $results->field_perm;

          switch($value['type']) {

            case 'title':
            ?>

                <thead>
                    <tr>
                        <th scope="col" width="200px"><?php echo $value['name'] ?></th><th scope="col">&nbsp;</th>
                    </tr>
                </thead>

            <?php

            break;

            case 'text':

            ?>

				<tr <?php if ($value['vis'] == '0') { ?>id="drop-down" <?php } else { ?> id="<?php echo $value['id'] ?>_row"<?php } ?> 

                <?php if (($field_type == 'drop-down') && ($value['vis'] == '0')) { echo ' style="display:table-row;"'; } elseif (($field_type <> 'drop-down') && ($value['vis'] == '0')) { echo ' style="display:none;"'; }?>>
                    <td class="titledesc"><?php if ($value['tip']) { ?><a href="#" tip="<?php echo $value['tip'] ?>" tabindex="99"><div class="helpico"></div></a><?php } ?><?php echo $value['name'] ?>:</td>
                    <td class="forminp"><input name="<?php echo $value['id'] ?>" id="<?php echo $value['id'] ?>" type="<?php echo $value['type'] ?>" style="<?php echo $value['css'] ?>" value="<?php echo $results->$value['id'] ?>" class="<?php if ($value['req']) ?> <?php if($field_type == 'drop-down') echo 'required '; ?> <?php if ($value['altclass']) echo $value['altclass'] ?>"<?php if ($value['min']) ?> minlength="<?php echo $value['min'] ?>" <?php if($value['id'] == 'field_name') { ?>readonly="readonly"<?php } ?> /><br /><small><?php echo $value['desc'] ?></small></td>
                </tr>

            <?php

            break;

            case 'select':

            ?>
				
               <tr id="<?php echo $value['id'] ?>_row">
                   <td class="titledesc"><?php if ($value['tip']) { ?><a href="#" tip="<?php echo $value['tip'] ?>" tabindex="99"><div class="helpico"></div></a><?php } ?><?php echo $value['name'] ?>:</td>
                   <td class="forminp"><select <?php if ($value['js']) echo $value['js']; ?> <?php if(($field_perm == 1) || ($field_perm == 2)) { ?>DISABLED<?php } ?> name="<?php echo $value['id'] ?>" id="<?php echo $value['id'] ?>" style="<?php echo $value['css'] ?>">

                       <?php foreach ($value['options'] as $key => $val) { ?>

                             <option value="<?php echo $key ?>"<?php if ( $results->$value['id'] == $key) { ?> selected="selected" <?php $field_type_out = $field_type; } ?>><?php echo $val; ?></option>

                       <?php } ?>

                       </select><br />
                       <small><?php echo $value['desc'] ?></small>

                       <?php
                       // have to submit this field as a hidden value if perms are 1 or 2 since the DISABLED option won't pass anything into the $_POST
                       if(($field_perm == 1) || ($field_perm == 2)) { ?><input type="hidden" name="<?php echo $value['id'] ?>" value="<?php echo $field_type_out; ?>" /><?php } ?>

                   </td>
               </tr>

            <?php

            break;

            case 'textarea':

            ?>

               <tr id="<?php echo $value['id'] ?>_row"<?php if($value['id'] == 'field_values') { ?> style="display: none;" <?php } ?>>
                   <td class="titledesc"><?php if ($value['tip']) { ?><a href="#" tip="<?php echo $value['tip'] ?>" tabindex="99"><div class="helpico"></div></a><?php } ?><?php echo $value['name'] ?>:</td>
                   <td class="forminp"><textarea <?php if((($field_perm == 1) || ($field_perm == 2)) && ($value['id'] != 'field_tooltip') && $value['id'] != 'field_values') { ?>readonly="readonly"<?php } ?> name="<?php echo $value['id']?>" id="<?php echo $value['id'] ?>" style="<?php echo $value['css'] ?>"><?php echo $results->$value['id'] ?></textarea>
                       <br /><small><?php echo $value['desc'] ?></small></td>
               </tr>

            <?php

            break;

            case 'checkbox':
            ?>

                <tr id="<?php echo $value['id'] ?>_row">
                    <td class="titledesc"><?php if ($value['tip']) { ?><a href="#" tip="<?php echo $value['tip'] ?>" tabindex="99"><div class="helpico"></div></a><?php } ?><?php echo $value['name'] ?>:</td>
                    <td class="forminp"><input type="checkbox" name="<?php echo $value['id'] ?>" id="<?php echo $value['id'] ?>" value="1" style="<?php echo $value['css']?>" <?php if($results->$value['id']) { ?>checked="checked"<?php } ?> />
                        <br /><small><?php echo $value['desc'] ?></small>
                    </td>
                </tr>

            <?php
            break;

            case 'cat_checklist':

            ?>

               <tr id="<?php echo $value['id'] ?>_row">
                   <td class="titledesc"><?php if ($value['tip']) { ?><a href="#" tip="<?php echo $value['tip'] ?>" tabindex="99"><div class="helpico"></div></a><?php } ?><?php echo $value['name'] ?>:</td>
                   <td class="forminp">
                       <div id="categorydiv">
                           <div class="tabs-panel" id="categories-all" style="<?php echo $value['css'] ?>">
                               <ul class="list:category categorychecklist form-no-clear" id="categorychecklist">

                                   <?php echo cp_category_checklist(unserialize($results->form_cats),(cp_exclude_cats($results->id))); ?>

                               </ul>
                           </div>
                       </div>
                       <br /><small><?php echo $value['desc'] ?></small>
                   </td>
               </tr>

            <?php

            break;

        } // end switch

      } // end $results

    } // endforeach

    ?>

    </table>

<?php
}


function cp_admin_fields($options) {
	global $shortname;
?>


<div id="tabs-wrap">


    <?php

    // first generate the page tabs
    $counter = 1;

    echo '<ul class="tabs">'. "\n";
    foreach ($options as $value) {

        if (in_array('tab', $value)) :
            echo '<li><a href="#'.$value['type'].$counter.'">'.$value['tabname'].'</a></li>'. "\n";
            $counter = $counter + 1;
        endif;

    }
    echo '</ul>'. "\n\n";


     // now loop through all the options
    $counter = 1;
    $table_width = get_option('cp_table_width');

    foreach ($options as $value) {

        switch($value['type']) {

            case 'tab':

                echo '<div id="'.$value['type'].$counter.'">'. "\n\n";
                echo '<table class="widefat fixed" style="width:'.$table_width.'; margin-bottom:20px;">'. "\n\n";

            break;

            case 'notab':

                echo '<table class="widefat fixed" style="width:'.$table_width.'; margin-bottom:20px;">'. "\n\n";

            break;

            case 'title':
            ?>

                <thead><tr><th scope="col" width="200px"><?php echo $value['name'] ?></th><th scope="col"><?php echo $value['desc'] ?>&nbsp;</th></tr></thead>

            <?php
            break;

            case 'text':
            ?>

            <?php if($value['id'] <> 'field_name') { // don't show the meta name field used by WP. This is automatically created by CP. ?>
                <tr <?php if ($value['vis'] == '0') { ?>id="<?php if ($value['visid']) { echo $value['visid']; } else { echo 'field_values'; } ?>" style="display:none;"<?php } else { ?>id="<?php echo $value['id'] ?>_row"<?php } ?>>
                    <td class="titledesc"><?php if ($value['tip']) { ?><a href="#" tip="<?php echo $value['tip'] ?>" tabindex="99"><div class="helpico"></div></a><?php } ?><?php echo $value['name'] ?>:</td>
                    <td class="forminp"><input name="<?php echo $value['id'] ?>" id="<?php echo $value['id'] ?>" type="<?php echo $value['type'] ?>" style="<?php echo $value['css'] ?>" value="<?php if (get_option( $value['id'])) echo get_option( $value['id'] ); else echo $value['std'] ?>"<?php if ($value['req']) { ?> class="required <?php if ($value['altclass']) echo $value['altclass'] ?>" <?php } ?> <?php if ($value['min']) { ?> minlength="<?php echo $value['min'] ?>"<?php } ?> /><br /><small><?php echo $value['desc'] ?></small></td>
                </tr>
            <?php } ?>

            <?php
            break;

            case 'select':
            ?>

                <tr id="<?php echo $value['id'] ?>_row">
                    <td class="titledesc"><?php if ($value['tip']) { ?><a href="#" tip="<?php echo $value['tip'] ?>" tabindex="99"><div class="helpico"></div></a><?php } ?><?php echo $value['name'] ?>:</td>
                    <td class="forminp"><select <?php if ($value['js']) echo $value['js']; ?> name="<?php echo $value['id'] ?>" id="<?php echo $value['id'] ?>" style="<?php echo $value['css'] ?>"<?php if ($value['req']) { ?> class="required"<?php } ?>>

                        <?php
                        foreach ($value['options'] as $key => $val) {
                        ?>

                            <option value="<?php echo $key ?>" <?php if (get_option($value['id']) == $key) { ?> selected="selected" <?php } ?>><?php echo ucfirst($val) ?></option>

                        <?php
                        }
                        ?>

                       </select><br /><small><?php echo $value['desc'] ?></small>
                    </td>
                </tr>

            <?php
            break;

            case 'checkbox':
            ?>

                <tr id="<?php echo $value['id'] ?>_row">
                    <td class="titledesc"><?php if ($value['tip']) { ?><a href="#" tip="<?php echo $value['tip'] ?>" tabindex="99"><div class="helpico"></div></a><?php } ?><?php echo $value['name'] ?>:</td>
                    <td class="forminp"><input type="checkbox" name="<?php echo $value['id'] ?>" id="<?php echo $value['id'] ?>" value="true" style="<?php echo $value['css']?>" <?php if(get_option($value['id'])) { ?>checked="checked"<?php } ?> />
                        <br /><small><?php echo $value['desc'] ?></small>
                    </td>
                </tr>

            <?php
            break;

            case 'textarea':
            ?>
                <tr id="<?php echo $value['id'] ?>_row"<?php if($value['id'] == 'field_values') { ?> style="display: none;" <?php } ?>>
                    <td class="titledesc"><?php if ($value['tip']) { ?><a href="#" tip="<?php echo $value['tip'] ?>" tabindex="99"><div class="helpico"></div></a><?php } ?><?php echo $value['name'] ?>:</td>
                    <td class="forminp">
                        <textarea name="<?php echo $value['id'] ?>" id="<?php echo $value['id'] ?>" style="<?php echo $value['css'] ?>" <?php if ($value['req']) { ?> class="required" <?php } ?><?php if ($value['min']) { ?> minlength="<?php echo $value['min'] ?>"<?php } ?>><?php if (get_option($value['id'])) echo stripslashes(get_option($value['id'])); else echo $value['std']; ?></textarea>
                        <br /><small><?php echo $value['desc'] ?></small>
                    </td>
                </tr>

            <?php
            break;

            case 'cat_checklist':
            ?>

                <tr id="<?php echo $value['id'] ?>_row">
                    <td class="titledesc"><?php if ($value['tip']) { ?><a href="#" tip="<?php echo $value['tip'] ?>" tabindex="99"><div class="helpico"></div></a><?php } ?><?php echo $value['name'] ?>:</td>
                    <td class="forminp">
                        <div id="categorydiv">
                            <div class="tabs-panel" id="categories-all" style="<?php echo $value['css'] ?>">
                                <ul class="list:category categorychecklist form-no-clear" id="categorychecklist">
                                <?php $catcheck = cp_category_checklist(0,cp_exclude_cats()); ?>
                                <?php if($catcheck) echo $catcheck; else wp_die( '<p style="color:red;">' .__('All your categories are currently being used. You must remove at least one category from another form layout before you can continue.','appthemes') .'</p>' ); ?>
                                </ul>
                            </div>
                        </div>
                        <br /><small><?php echo $value['desc'] ?></small>
                    </td>
                </tr>

            <?php
            break;

            case 'logo':
            ?>
                <tr id="<?php echo $value['id'] ?>_row">
                    <td class="titledesc"><?php echo $value['name'] ?></td>
                    <td class="forminp">&nbsp;</td>
                </tr>

            <?php
            break;

            case 'price_per_cat':
            ?>
                <tr id="<?php echo $value['id'] ?>_row"  class="cat-row">
                    <td class="titledesc"><?php if ($value['tip']) { ?><a href="#" tip="<?php echo $value['tip'] ?>" tabindex="99"><div class="helpico"></div></a><?php } ?><?php echo $value['name'] ?>:</td>

                    <td class="forminp">

                        <table style="width:100%;">

                        <?php

                        $categories = get_categories('orderby=name&order=asc&hide_empty=0&taxonomy=ad_cat');
                        $i = 0;

                        foreach ($categories as $cat) {

                            if (($i % 2) == 0) { ?>
                                <tr>
                            <?php
                            }

                            // if the category price is empty, put a zero in it so it doesn't error out
                            $cat_price = get_option('cp_cat_price_'.$cat->cat_ID);
                            if ($cat_price == '') {
                                $cat_price = '0';
                            }
                            ?>

                            <td nowrap style="padding-top:15px; text-align: right;"><?php echo $cat->cat_name; ?>:</td>
                            <td nowrap style="color:#bbb;"><input name="catarray[cp_cat_price_<?php echo $cat->cat_ID; ?>]" type="text" size="10" maxlength="100" value="<?php echo $cat_price ?>" />&nbsp;<?php echo get_option("cp_curr_pay_type") ?></td>
                            <td cellspan="2" width="100">&nbsp;</td>

                            <?php
                            if (($i % 2) != 0) { ?>
                                </tr>
                            <?php
                            }

                            $i++;

                        } // end foreach
                        ?>

                        </table>

                    </td>
                </tr>


            <?php
            break;

            case 'tabend':

                echo '</table>'. "\n\n";
                echo '</div> <!-- #tab'.$counter.' -->'. "\n\n";
                $counter = $counter + 1;

            break;

            case 'notabend':

                echo '</table>'. "\n\n";

            break;

        } // end switch

    } // end foreach
    ?>

   </div> <!-- #tabs-wrap -->

<?php
}


function cp_dashboard() {
   global $wpdb, $app_edition, $app_rss_feed;
   global $app_twitter_rss_feed, $app_forum_rss_feed, $options_dashboard;

 	$ad_count_live = $wpdb->get_var($wpdb->prepare("SELECT count(ID) FROM $wpdb->posts WHERE post_type = 'ad_listing' AND post_status = 'publish'"));
	$ad_count_pending = $wpdb->get_var($wpdb->prepare("SELECT count(ID) FROM $wpdb->posts WHERE post_type = 'ad_listing' AND post_status = 'pending'"));
	$ad_rev_total = $wpdb->get_var($wpdb->prepare("SELECT sum(mc_gross) FROM $wpdb->prefix" . 'cp_order_info'));
   ?>


        <div class="wrap">
        <div class="icon32" id="icon-themes"><br/></div>
        <h2><?php _e('ClassiPress Dashboard', 'appthemes') ?></h2>

        <?php cp_admin_info_box(); ?>

        <div class="dash-left metabox-holder">

		<div class="postbox">
			<div class="statsico"></div>
			<h3 class="hndle"><span><?php _e('ClassiPress Info', 'appthemes') ?></span></h3>

			<div class="preloader-container">
				<div class="insider" id="boxy">

				<ul>
					<li><?php _e('Total Live Ads', 'appthemes')?>: <a href="edit.php?post_status=publish&post_type=ad_listing"><strong><?php echo $ad_count_live; ?></strong></a></li>
					<li><?php _e('Total Pending Ads', 'appthemes')?>: <a href="edit.php?post_status=pending&post_type=ad_listing"><strong><?php echo $ad_count_pending; ?></strong></a></li>
					<li><?php _e('Total Revenue', 'appthemes')?>: <?php if (get_option('cp_edition') == 'Personal Edition') { ?> (<a href="http://appthemes.com/cp/member.php?tab=add_renew" target="_new"><?php _e('Upgrade Required', 'appthemes') ?></a>) <?php } else { ?><strong><?php echo cp_pos_price(number_format($ad_rev_total, 2)); ?></strong><?php } ?></li>
					<li><?php _e('Product Version', 'appthemes'); ?>: <strong><?php echo get_option('cp_version'); ?></strong> <?php global $app_version; if(get_option('cp_version') != $app_version) echo __('You upgraded to version ') . $app_version . '. <a href="/wp-admin/admin.php?page=admin-options.php&upgrade=yes">Click here to finish your upgrade.</a>';  if( isset($_GET['upgrade']) ) echo ' ' . __('Congratulations, you have completely upgraded ClassiPress to the latest version!'); ?></li>
					<li><?php _e('License Type', 'appthemes')?>: <strong><?php echo get_option('cp_edition'); ?></strong> <?php if (get_option('cp_edition') != 'Ultimate Edition') { ?> (<a href="http://appthemes.com/cp/member.php?tab=add_renew" target="_new"><?php _e('Upgrade', 'appthemes') ?></a>) <?php } ?></li>
					<li><?php _e('Product Support', 'appthemes')?>:  <a href="http://appthemes.com/forum/" target="_new"><?php _e('Forum','appthemes')?></a> | <a href="http://appthemes.com/support/docs/" target="_new"><?php _e('Documentation','appthemes')?></a></li>
				</ul>

				</div>
			</div>

		</div> <!-- postbox end -->



		<div class="postbox">
			<div class="newspaperico"></div><a target="_new" href="<?php echo $app_rss_feed ?>"><div class="rssico"></div></a>
			<h3 class="hndle" id="poststuff"><span><?php _e('Latest News', 'appthemes') ?></span></h3>

             <div class="preloader-container">

		<div class="insider" id="boxy">

			<?php appthemes_dashboard_appthemes(); ?>

		</div> <!-- inside end -->

             </div>


		</div> <!-- postbox end -->


	</div> <!-- dash-left end -->



	<div class="dash-right metabox-holder">



		<div class="postbox">
			<div class="twitterico"></div><a target="_new" href="<?php echo $app_twitter_rss_feed ?>"><div class="rssico"></div></a>
			<h3 class="hndle" id="poststuff"><span><?php _e('Latest Tweets', 'appthemes') ?></span></h3>

            <div class="preloader-container">
		<div class="insider" id="boxy">

			<?php appthemes_dashboard_twitter(); ?>

		</div> <!-- inside end -->
            </div>


		</div> <!-- postbox end -->


		<div class="postbox">
			<div class="forumico"></div><a target="_new" href="<?php echo $app_forum_rss_feed ?>"><div class="rssico"></div></a>
			<h3 class="hndle" id="poststuff"><span><?php _e('Support Forum', 'appthemes') ?></span></h3>

            <div class="preloader-container">
		<div class="insider" id="boxy">

			<?php appthemes_dashboard_forum(); ?>

		</div> <!-- inside end -->
            </div>


		</div> <!-- postbox end -->


	</div> <!-- dash-right end -->
</div> <!-- /wrap -->

<?php
}


function cp_settings() {
    global $options_settings;

    cp_update_options($options_settings);
    ?>

    <div class="wrap">
        <div class="icon32" id="icon-tools"><br/></div>
        <h2><?php _e('General Settings','appthemes') ?></h2>

        <?php cp_admin_info_box(); ?>

        <form method="post" id="mainform" action="">
            <p class="submit btop"><input name="save" type="submit" value="<?php _e('Save changes','appthemes') ?>" /></p>

            <?php cp_admin_fields($options_settings); ?>

            <p class="submit bbot"><input name="save" type="submit" value="<?php _e('Save changes','appthemes') ?>" /></p>
            <input name="submitted" type="hidden" value="yes" />
        </form>
    </div>

<?php

}


function cp_emails() {
    global $options_emails;

    cp_update_options($options_emails);
    ?>

    <div class="wrap">
        <div class="icon32" id="icon-tools"><br/></div>
        <h2><?php _e('Email Settings','appthemes') ?></h2>

        <?php cp_admin_info_box(); ?>

        <form method="post" id="mainform" action="">
            <p class="submit btop"><input name="save" type="submit" value="<?php _e('Save changes','appthemes') ?>" /></p>

            <?php cp_admin_fields($options_emails); ?>

            <p class="submit bbot"><input name="save" type="submit" value="<?php _e('Save changes','appthemes') ?>" /></p>
            <input name="submitted" type="hidden" value="yes" />
        </form>
    </div>

<?php

}



function cp_pricing() {
    global $options_pricing;

    cp_update_options($options_pricing);
    ?>

    <div class="wrap">
        <div class="icon32" id="icon-options-general"><br/></div>
        <h2><?php _e('Pricing Options','appthemes') ?></h2>

        <?php cp_admin_info_box(); ?>

        <form method="post" id="mainform" action="">
            <p class="submit btop"><input name="save" type="submit" value="<?php _e('Save changes','appthemes') ?>" /></p>

            <?php cp_admin_fields($options_pricing); ?>

            <p class="submit bbot"><input name="save" type="submit" value="<?php _e('Save changes','appthemes') ?>" /></p>
            <input name="submitted" type="hidden" value="yes" />
        </form>
    </div>

<?php
}



// show the ad packages admin page
function cp_ad_packs() {
    global $options_new_ad_pack, $wpdb, $current_user;

    get_currentuserinfo();

    // check to prevent php "notice: undefined index" msg
    if(isset($_GET['action'])) $theswitch = $_GET['action']; else $theswitch ='';

    switch($theswitch) {

    case 'addpack':
    ?>

        <div class="wrap">
            <div class="icon32" id="icon-themes"><br/></div>
            <h2><?php _e('New Ad Pack','appthemes') ?></h2>

            <?php cp_admin_info_box(); ?>

        <?php
        // check and make sure the form was submitted
        if(isset($_POST['submitted'])) {


            $insert = "INSERT INTO " . $wpdb->prefix . "cp_ad_packs" .
            " (pack_name, pack_desc, pack_price, pack_duration, pack_status, pack_owner, pack_modified) " .
            "VALUES ('" .
                    $wpdb->escape(appthemes_clean($_POST['pack_name'])) . "','" .
                    $wpdb->escape(appthemes_clean($_POST['pack_desc'])) . "','" .
                    $wpdb->escape(appthemes_clean($_POST['pack_price'])) . "','" .
                    $wpdb->escape(appthemes_clean($_POST['pack_duration'])) . "','" .
                    $wpdb->escape(appthemes_clean($_POST['pack_status'])) . "','" .
                    $wpdb->escape(appthemes_clean($_POST['pack_owner'])) . "','" .
                    gmdate('Y-m-d H:i:s') .
                "')";

            $results = $wpdb->query($insert);


            if ($results) :
            ?>

                <p style="text-align:center;padding-top:50px;font-size:22px;"><?php _e('Creating your ad package.....','appthemes') ?><br /><br /><img src="<?php echo bloginfo('template_directory') ?>/images/loading.gif" alt="" /></p>
                <meta http-equiv="refresh" content="0; URL=?page=adpacks">

            <?php
            endif;


        } else {
        ?>

			<form method="post" id="mainform" action="">

				<?php cp_admin_fields($options_new_ad_pack) ?>

				<p class="submit"><input class="btn button-primary" name="save" type="submit" value="<?php _e('Create New Ad Package','appthemes') ?>" />&nbsp;&nbsp;&nbsp;
					<input name="cancel" type="button" onClick="location.href='?page=adpacks'" value="<?php _e('Cancel','appthemes') ?>" /></p>
				<input name="submitted" type="hidden" value="yes" />
				<input name="pack_owner" type="hidden" value="<?php echo $current_user->user_login ?>" />

			</form>

        <?php
        }
        ?>

        </div><!-- end wrap -->

    <?php
    break;

    case 'editpack':
    ?>

        <div class="wrap">
            <div class="icon32" id="icon-themes"><br/></div>
            <h2><?php _e('Edit Ad Package','appthemes') ?></h2>

            <?php cp_admin_info_box(); ?>

        <?php
        if(isset($_POST['submitted']) && $_POST['submitted'] == 'yes') {

            $update = "UPDATE " . $wpdb->prefix . "cp_ad_packs SET" .
                    " pack_name = '" . $wpdb->escape(appthemes_clean($_POST['pack_name'])) . "'," .
                    " pack_desc = '" . $wpdb->escape(appthemes_clean($_POST['pack_desc'])) . "'," .
                    " pack_price = '" . $wpdb->escape(appthemes_clean($_POST['pack_price'])) . "'," .
                    " pack_duration = '" . $wpdb->escape(appthemes_clean($_POST['pack_duration'])) . "'," .
                    " pack_status = '" . $wpdb->escape(appthemes_clean($_POST['pack_status'])) . "'," .
                    " pack_owner = '" . $wpdb->escape(appthemes_clean($_POST['pack_owner'])) . "'," .
                    " pack_modified = '" . gmdate('Y-m-d H:i:s') . "'" .
                    " WHERE pack_id ='" . $_GET['id'] ."'";

            $results = $wpdb->get_row($update);
            ?>

            <p style="text-align:center;padding-top:50px;font-size:22px;"><?php _e('Saving your changes.....','appthemes') ?><br /><br /><img src="<?php echo bloginfo('template_directory') ?>/images/loading.gif" alt="" /></p>
            <meta http-equiv="refresh" content="0; URL=?page=adpacks">

        <?php
        } else {
        ?>


            <form method="post" id="mainform" action="">

            <?php cp_admin_db_fields($options_new_ad_pack, 'cp_ad_packs', 'pack_id') ?>

                <p class="submit">
                    <input class="btn button-primary" name="save" type="submit" value="<?php _e('Save changes','appthemes') ?>" />&nbsp;&nbsp;&nbsp;
                    <input name="cancel" type="button" onClick="location.href='?page=adpacks'" value="<?php _e('Cancel','appthemes') ?>" />
                    <input name="submitted" type="hidden" value="yes" />
                    <input name="pack_owner" type="hidden" value="<?php echo $current_user->user_login ?>" />
                </p>

            </form>

        <?php } ?>

        </div><!-- end wrap -->

    <?php
    break;

    case 'delete':

        $delete = "DELETE FROM " . $wpdb->prefix . "cp_ad_packs "
                . "WHERE pack_id = '". $_GET['id'] ."'";

        $wpdb->query($delete);
        ?>
        <p style="text-align:center;padding-top:50px;font-size:22px;"><?php _e('Deleting ad package.....','appthemes') ?><br /><br /><img src="<?php echo bloginfo('template_directory') ?>/images/loading.gif" alt="" /></p>
        <meta http-equiv="refresh" content="0; URL=?page=adpacks">

    <?php
    break;

    default:

        $sql = "SELECT * "
             . "FROM " . $wpdb->prefix . "cp_ad_packs "
             . "ORDER BY pack_id desc";

        $results = $wpdb->get_results($sql);

    ?>

        <div class="wrap">
        <div class="icon32" id="icon-themes"><br/></div>
        <h2><?php _e('Ad Packs','appthemes') ?>&nbsp;<a class="button add-new-h2" href="?page=adpacks&amp;action=addpack"><?php _e('Add New','appthemes') ?></a></h2>

        <?php cp_admin_info_box(); ?>


        <p class="admin-msg"><?php _e('Ad Packs allow you to create bundled listing options for your customers to choose from. For example, instead of only offering a set price for xx days (30 days for $5), you could also offer discounts for longer terms (60 days for $7). These only work if you are selling ads and using the "Fixed Price Per Ad" price model.','appthemes') ?></p>

        <table id="tblspacer" class="widefat fixed">

            <thead>
                <tr>
					<th scope="col" style="width:25px;">&nbsp;</th>
                    <th scope="col"><?php _e('Name','appthemes') ?></th>
                    <th scope="col"><?php _e('Description','appthemes') ?></th>
                    <th scope="col"><?php _e('Price Per Ad','appthemes') ?></th>
                    <th scope="col"><?php _e('Number of Days','appthemes') ?></th>
                    <th scope="col" style="width:150px;"><?php _e('Modified','appthemes') ?></th>
                    <th scope="col" style="width:75px;"><?php _e('Status','appthemes') ?></th>
                    <th scope="col" style="text-align:center;width:100px;"><?php _e('Actions','appthemes') ?></th>
                </tr>
            </thead>

            <?php
            if ($results) {
                $rowclass = '';
                $i=1;
            ?>

              <tbody id="list">

            <?php
                foreach( $results as $result ) {
                
                $rowclass = 'even' == $rowclass ? 'alt' : 'even';
              ?>

                <tr class="<?php echo $rowclass ?>">
                    <td style="padding-left:10px;"><?php echo $i ?>.</td>
                    <td><a href="?page=adpacks&amp;action=editpack&amp;id=<?php echo $result->pack_id ?>"><strong><?php echo $result->pack_name ?></strong></a></td>
                    <td><?php echo $result->pack_desc ?></td>
                    <td><?php echo cp_pos_price($result->pack_price) ?></td>
                    <td><?php echo $result->pack_duration ?></td>
                    <td><?php echo mysql2date(get_option('date_format') .' '. get_option('time_format'), $result->pack_modified) ?> <?php _e('by','appthemes') ?> <?php echo $result->pack_owner; ?></td>
                    <td><?php echo ucfirst($result->pack_status) ?></td>
                    <td style="text-align:center">
                        <a href="?page=adpacks&amp;action=editpack&amp;id=<?php echo $result->pack_id ?>"><img src="<?php echo bloginfo('template_directory') ?>/images/edit.png" alt="<?php echo  _e('Edit ad package','appthemes') ?>" title="<?php echo _e('Edit ad package','appthemes') ?>" /></a>&nbsp;&nbsp;&nbsp;
                        <a onclick="return confirmBeforeDelete();" href="?page=adpacks&amp;action=delete&amp;id=<?php echo $result->pack_id ?>"><img src="<?php echo bloginfo('template_directory') ?>/images/cross.png" alt="<?php echo _e('Delete ad package','appthemes') ?>" title="<?php echo _e('Delete ad package','appthemes') ?>" /></a>
                    </td>
                </tr>

              <?php

                $i++;

                } // end for each
              ?>

              </tbody>

            <?php

            } else {

            ?>

                <tr>
                    <td colspan="7"><?php _e('No ad packs found.','appthemes') ?></td>
                </tr>

            <?php
            } // end $results
            ?>

            </table>


        </div><!-- end wrap -->

    <?php
    } // end switch
    ?>
    <script type="text/javascript">
        /* <![CDATA[ */
            function confirmBeforeDelete() { return confirm("<?php _e('Are you sure you want to delete this ad package?', 'appthemes'); ?>"); }
        /* ]]> */
    </script>

<?php

}


// show the ad packages admin page
function cp_coupons() {
    global $options_new_coupon, $wpdb, $current_user, $app_version;

    get_currentuserinfo();

    // check to prevent php "notice: undefined index" msg
    if(isset($_GET['action'])) $theswitch = $_GET['action']; else $theswitch ='';

    switch($theswitch) {

    case 'addcoupon':
    ?>

        <div class="wrap">
            <div class="icon32" id="icon-themes"><br/></div>
            <h2><?php _e('New Coupon','appthemes') ?></h2>
            <?php
            //if your database is not at least version 3.1, you must upgrade first.
            if(get_option('cp_version') != $app_version) {
                echo '<div class="error">' . __('Error: Your ClassiPress database is not updated to match your version of ClassiPress.','appthemes') . '</div>';
                echo __('Product Version', 'appthemes') . ': <strong>' . get_option('cp_version') . '</strong> ';
                if(get_option('cp_version') != $app_version)
                        echo __('You upgraded to version ') . $app_version . '. <a href="/wp-admin/admin.php?page=admin-options.php&upgrade=yes">Click here to finish your upgrade.</a>';
                die();
            }
            ?>

            <?php cp_admin_info_box(); ?>

        <?php
        // check and make sure the form was submitted
        if(isset($_POST['submitted'])) {

		//echo $_POST['coupon_expire_date'] . '<-- expire date';

            $insert = "INSERT INTO " . $wpdb->prefix . "cp_coupons" .
            " (coupon_code, coupon_desc, coupon_discount, coupon_discount_type, coupon_start_date, coupon_expire_date, coupon_status, coupon_max_use_count, coupon_owner, coupon_created, coupon_modified) " .
            "VALUES ('" .
                    $wpdb->escape(appthemes_clean($_POST['coupon_code'])) . "','" .
                    $wpdb->escape(appthemes_clean($_POST['coupon_desc'])) . "','" .
                    $wpdb->escape(appthemes_clean($_POST['coupon_discount'])) . "','" .
                    $wpdb->escape(appthemes_clean($_POST['coupon_discount_type'])) . "','" .
                    $wpdb->escape(appthemes_clean($_POST['coupon_start_date'])) . "','" .
                    $wpdb->escape(appthemes_clean($_POST['coupon_expire_date'])) . "','" .
                    $wpdb->escape(appthemes_clean($_POST['coupon_status'])) . "','" .					
                    $wpdb->escape(appthemes_clean($_POST['coupon_max_use_count'])) . "','" .
                    $wpdb->escape(appthemes_clean($_POST['coupon_owner'])) . "','" .
                    gmdate('Y-m-d H:i:s') . "','" .
                    gmdate('Y-m-d H:i:s') .
                    "')";

            $results = $wpdb->query($insert);


            if ($results) :
            ?>

                <p style="text-align:center;padding-top:50px;font-size:22px;"><?php _e('Creating your coupon.....','appthemes') ?><br /><br /><img src="<?php echo bloginfo('template_directory') ?>/images/loading.gif" alt="" /></p>
                <meta http-equiv="refresh" content="0; URL=?page=coupons">

            <?php
            endif;


        } else {
        ?>

                <form method="post" id="mainform" action="">

                    <?php cp_admin_fields($options_new_coupon) ?>

                    <p class="submit"><input class="btn button-primary" name="save" type="submit" value="<?php _e('Create New Coupon','appthemes') ?>" />&nbsp;&nbsp;&nbsp;
                    <input name="cancel" type="button" onClick="location.href='?page=coupons'" value="<?php _e('Cancel','appthemes') ?>" /></p>
                    <input name="submitted" type="hidden" value="yes" />
                    <input name="coupon_owner" type="hidden" value="<?php echo $current_user->user_login ?>" />

                </form>

        <?php
        }
        ?>

        </div><!-- end wrap -->

    <?php
    break;

    case 'editcoupon':
    ?>

        <div class="wrap">
            <div class="icon32" id="icon-themes"><br/></div>
            <h2><?php _e('Edit Coupon','appthemes') ?></h2>

            <?php cp_admin_info_box(); ?>

        <?php
        if(isset($_POST['submitted']) && $_POST['submitted'] == 'yes') {

            $update = "UPDATE " . $wpdb->prefix . "cp_coupons SET" .
                    " coupon_code = '" . $wpdb->escape(appthemes_clean($_POST['coupon_code'])) . "'," .
                    " coupon_desc = '" . $wpdb->escape(appthemes_clean($_POST['coupon_desc'])) . "'," .
                    " coupon_discount = '" . $wpdb->escape(appthemes_clean($_POST['coupon_discount'])) . "'," .
                    " coupon_discount_type = '" . $wpdb->escape(appthemes_clean($_POST['coupon_discount_type'])) . "'," .
                    " coupon_start_date = '" . $wpdb->escape(appthemes_clean($_POST['coupon_start_date'])) . "'," .
                    " coupon_expire_date = '" . $wpdb->escape(appthemes_clean($_POST['coupon_expire_date'])) . "'," .
                    " coupon_status = '" . $wpdb->escape(appthemes_clean($_POST['coupon_status'])) . "'," .
                    " coupon_max_use_count = '" . $wpdb->escape(appthemes_clean($_POST['coupon_max_use_count'])) . "'," .
                    " coupon_owner = '" . $wpdb->escape(appthemes_clean($_POST['coupon_owner'])) . "'," .
                    " coupon_modified = '" . gmdate('Y-m-d H:i:s') . "'" .
                    " WHERE coupon_id ='" . $_GET['id'] ."'";

            $results = $wpdb->get_row($update);
            ?>

            <p style="text-align:center;padding-top:50px;font-size:22px;"><?php _e('Saving your changes.....','appthemes') ?><br /><br /><img src="<?php echo bloginfo('template_directory') ?>/images/loading.gif" alt="" /></p>
            <meta http-equiv="refresh" content="0; URL=?page=coupons">

        <?php
        } else {
        ?>


            <form method="post" id="mainform" action="">

            <?php cp_admin_db_fields($options_new_coupon, 'cp_coupons', 'coupon_id') ?>

                <p class="submit">
                    <input class="btn button-primary" name="save" type="submit" value="<?php _e('Save changes','appthemes') ?>" />&nbsp;&nbsp;&nbsp;
                    <input name="cancel" type="button" onClick="location.href='?page=coupons'" value="<?php _e('Cancel','appthemes') ?>" />
                    <input name="submitted" type="hidden" value="yes" />
                    <input name="coupon_owner" type="hidden" value="<?php echo $current_user->user_login ?>" />
                </p>

            </form>

        <?php } ?>

        </div><!-- end wrap -->

    <?php
    break;

    case 'delete':

        $delete = "DELETE FROM " . $wpdb->prefix . "cp_coupons "
                . "WHERE coupon_id = '". $_GET['id'] ."'";

        $wpdb->query($delete);
        ?>
        <p style="text-align:center;padding-top:50px;font-size:22px;"><?php _e('Deleting coupon.....','appthemes') ?><br /><br /><img src="<?php echo bloginfo('template_directory') ?>/images/loading.gif" alt="" /></p>
        <meta http-equiv="refresh" content="0; URL=?page=coupons">

    <?php
    break;

    default:

		$results = cp_get_coupons();

    ?>

        <div class="wrap">
        <div class="icon32" id="icon-edit-pages"><br/></div>
        <h2><?php _e('Coupons','appthemes') ?>&nbsp;<a class="button add-new-h2" href="?page=coupons&amp;action=addcoupon"><?php _e('Add New','appthemes') ?></a></h2>

        <?php cp_admin_info_box(); ?>


        <p class="admin-msg"><?php _e('Create coupons to offer special discounts to your customers.','appthemes') ?></p>

        <table id="tblspacer" class="widefat fixed">

            <thead>
                <tr>
                    <th scope="col" style="width:25px;">&nbsp;</th>
                    <th scope="col"><?php _e('Code','appthemes') ?></th>
                    <th scope="col"><?php _e('Description','appthemes') ?></th>
                    <th scope="col"><?php _e('Discount','appthemes') ?></th>
					<th scope="col"><?php _e('Usage','appthemes') ?></th>
					<th scope="col"><?php _e('Valid','appthemes') ?></th>
                    <th scope="col"><?php _e('Expires','appthemes') ?></th>
                    <th scope="col" style="width:150px;"><?php _e('Modified','appthemes') ?></th>
                    <th scope="col" style="width:75px;"><?php _e('Status','appthemes') ?></th>
                    <th scope="col" style="text-align:center;width:100px;"><?php _e('Actions','appthemes') ?></th>
                </tr>
            </thead>

            <?php
            if ($results) {
                $rowclass = '';
                $i=1;
            ?>

              <tbody id="list">

            <?php
                foreach( $results as $result ) {
                
                $rowclass = 'even' == $rowclass ? 'alt' : 'even';
              ?>

                <tr class="<?php echo $rowclass ?>">
                    <td style="padding-left:10px;"><?php echo $i ?>.</td>
                    <td><a href="?page=coupons&amp;action=editcoupon&amp;id=<?php echo $result->coupon_id ?>"><strong><?php echo $result->coupon_code ?></strong></a></td>
                    <td><?php echo $result->coupon_desc ?></td>
                    <td><?php if (($result->coupon_discount_type) == '%') echo number_format($result->coupon_discount,0) . '%'; else echo cp_pos_price($result->coupon_discount); ?></td>              
					<td><?php echo $result->coupon_use_count ?><?php if (($result->coupon_max_use_count) <> 0) echo '/' . $result->coupon_max_use_count ?></td>
					<td><?php echo mysql2date(get_option('date_format') .' '. get_option('time_format'), $result->coupon_start_date) ?></td>
					<td><?php echo mysql2date(get_option('date_format') .' '. get_option('time_format'), $result->coupon_expire_date) ?></td>
                    <td><?php echo mysql2date(get_option('date_format') .' '. get_option('time_format'), $result->coupon_modified) ?> <br /><?php _e('by','appthemes') ?> <?php echo $result->coupon_owner; ?></td>
                    <td><?php echo ucfirst($result->coupon_status) ?></td>
                    <td style="text-align:center">
                        <a href="?page=coupons&amp;action=editcoupon&amp;id=<?php echo $result->coupon_id ?>"><img src="<?php echo bloginfo('template_directory') ?>/images/edit.png" alt="<?php echo  _e('Edit coupon','appthemes') ?>" title="<?php echo _e('Edit coupon','appthemes') ?>" /></a>&nbsp;&nbsp;&nbsp;
                        <a onclick="return confirmBeforeDelete();" href="?page=coupons&amp;action=delete&amp;id=<?php echo $result->coupon_id ?>"><img src="<?php echo bloginfo('template_directory') ?>/images/cross.png" alt="<?php echo _e('Delete coupon','appthemes') ?>" title="<?php echo _e('Delete coupon','appthemes') ?>" /></a>
                    </td>
                </tr>

              <?php

                $i++;

                } // end for each
              ?>

              </tbody>

            <?php

            } else {

            ?>

                <tr>
                    <td colspan="7"><?php _e('No coupons found.','appthemes') ?></td>
                </tr>

            <?php
            } // end $results
            ?>

            </table>


        </div><!-- end wrap -->

    <?php
    } // end switch
    ?>
    <script type="text/javascript">
        /* <![CDATA[ */
            function confirmBeforeDelete() { return confirm("<?php _e('Are you sure you want to delete this coupon?', 'appthemes'); ?>"); }
        /* ]]> */
    </script>

<?php

}



function cp_gateways() {
    global $options_gateways;

    cp_update_options($options_gateways);
    ?>

    <div class="wrap">
        <div class="icon32" id="icon-options-general"><br/></div>
        <h2><?php _e('Payment Gateways','appthemes') ?></h2>

        <?php cp_admin_info_box(); ?>

        <form method="post" id="mainform" action="">
            <p class="submit btop"><input name="save" type="submit" value="<?php _e('Save changes','appthemes') ?>" /></p>

            <?php cp_admin_fields($options_gateways); ?>

            <p class="submit bbot"><input name="save" type="submit" value="<?php _e('Save changes','appthemes') ?>" /></p>
            <input name="submitted" type="hidden" value="yes" />
        </form>
    </div>

<?php
}


function cp_form_layouts() {
    global $options_new_form, $wpdb, $current_user;

    get_currentuserinfo();

    // check to prevent php "notice: undefined index" msg when php strict warnings is on
    if(isset($_GET['action'])) $theswitch = $_GET['action']; else $theswitch ='';

    switch($theswitch) {

    case 'addform':
    ?>

        <div class="wrap">
            <div class="icon32" id="icon-themes"><br/></div>
            <h2><?php _e('New Form Layout','appthemes') ?></h2>

            <?php cp_admin_info_box(); ?>

        <?php
        // check and make sure the form was submitted and the hidden fcheck id matches the cookie fcheck id
        if(isset($_POST['submitted'])) {

            if(!isset($_POST['post_category']))
                wp_die( '<p style="color:red;">' .__("Error: Please select at least one category. <a href='#' onclick='history.go(-1);return false;'>Go back</a>",'appthemes') .'</p>' );

            $insert = "INSERT INTO " . $wpdb->prefix . "cp_ad_forms" .
                    " (form_name, form_label, form_desc, form_cats, form_status, form_owner, form_created) " .
                    "VALUES ('" .
                        $wpdb->escape(appthemes_clean(cp_make_custom_name($_POST['form_label']))) . "','" .
                        $wpdb->escape(appthemes_clean($_POST['form_label'])) . "','" .
                        $wpdb->escape(appthemes_clean($_POST['form_desc'])) . "','" .
                        $wpdb->escape(serialize($_POST['post_category'])) . "','" .
                        $wpdb->escape(appthemes_clean($_POST['form_status'])) . "','" .
                        $wpdb->escape(appthemes_clean($_POST['form_owner'])) . "','" .
                        gmdate('Y-m-d H:i:s') .
                    "')";

            $results = $wpdb->query($insert);


            if ($results) {
                         ?>

                <p style="text-align:center;padding-top:50px;font-size:22px;"><?php _e('Creating your form.....','appthemes') ?><br /><br /><img src="<?php echo bloginfo('template_directory') ?>/images/loading.gif" alt="" /></p>
                <meta http-equiv="refresh" content="0; URL=?page=layouts">

            <?php
            } // end $results

        } else {
        ?>

            <form method="post" id="mainform" action="">

                <?php echo cp_admin_fields($options_new_form); ?>

                <p class="submit"><input class="btn button-primary" name="save" type="submit" value="<?php _e('Create New Form','appthemes') ?>" />&nbsp;&nbsp;&nbsp;
                <input name="cancel" type="button" onClick="location.href='?page=layouts'" value="<?php _e('Cancel','appthemes') ?>" /></p>
                <input name="submitted" type="hidden" value="yes" />
                <input name="form_owner" type="hidden" value="<?php echo $current_user->user_login ?>" />

            </form>

        <?php
        } // end isset $_POST
        ?>

        </div><!-- end wrap -->

    <?php
    break;


    case 'editform':
    ?>

        <div class="wrap">
        <div class="icon32" id="icon-themes"><br/></div>
        <h2><?php _e('Edit Form Layout','appthemes') ?></h2>

        <?php
        if(isset($_POST['submitted']) && $_POST['submitted'] == 'yes') {

            if(!isset($_POST['post_category']))
                wp_die( '<p style="color:red;">' .__("Error: Please select at least one category. <a href='#' onclick='history.go(-1);return false;'>Go back</a>",'appthemes') .'</p>' );


            $update = "UPDATE " . $wpdb->prefix . "cp_ad_forms SET" .
                    " form_label    = '" . $wpdb->escape(appthemes_clean($_POST['form_label'])) . "'," .
                    " form_desc     = '" . $wpdb->escape(appthemes_clean($_POST['form_desc'])) . "'," .
                    " form_cats     = '" . $wpdb->escape(serialize($_POST['post_category'])) . "'," .
                    " form_status   = '" . $wpdb->escape(appthemes_clean($_POST['form_status'])) . "'," .
                    " form_owner    = '" . $wpdb->escape(appthemes_clean($_POST['form_owner'])) . "'," .
                    " form_modified = '" . gmdate('Y-m-d H:i:s') . "'" .
                    " WHERE id      = '" . $_GET['id'] ."'";

            $results = $wpdb->get_row($update);

            ?>

            <p style="text-align:center;padding-top:50px;font-size:22px;"><?php _e('Saving your changes.....','appthemes') ?><br /><br /><img src="<?php echo bloginfo('template_directory') ?>/images/loading.gif" alt="" /></p>
            <meta http-equiv="refresh" content="0; URL=?page=layouts">

        <?php
        } else {
        ?>

            <form method="post" id="mainform" action="">

            <?php echo cp_admin_db_fields($options_new_form, 'cp_ad_forms', 'id'); ?>

                <p class="submit"><input class="btn button-primary" name="save" type="submit" value="<?php _e('Save changes','appthemes') ?>" />&nbsp;&nbsp;&nbsp;
                <input name="cancel" type="button" onClick="location.href='?page=layouts'" value="<?php _e('Cancel','appthemes') ?>" /></p>
                <input name="submitted" type="hidden" value="yes" />
                <input name="form_owner" type="hidden" value="<?php echo $current_user->user_login ?>" />

            </form>

        <?php
        } // end isset $_POST
        ?>

        </div><!-- end wrap -->

    <?php
    break;


    /**
    * Form Builder Page
    * Where fields are added to form layouts
    */

    case 'formbuilder':
    ?>

        <div class="wrap">
        <div class="icon32" id="icon-themes"><br/></div>
        <h2><?php _e('ClassiPress Form Builder','appthemes') ?></h2>

        <?php cp_admin_info_box(); ?>

        <?php
        // add fields to page layout on left side
        if(isset($_POST['field_id'])) {

            // take selected checkbox array and loop through ids
            foreach($_POST['field_id'] as $value) {

                $insert = "INSERT INTO " . $wpdb->prefix . "cp_ad_meta "
                        . "(form_id, field_id) "
                        . "VALUES ('" .
                            $wpdb->escape(appthemes_clean($_POST['form_id'])) . "','" .
                            $wpdb->escape(appthemes_clean($value)) . "'" .
                        ")";

                $results = $wpdb->query($insert);

            } // end foreach

        } // end $_POST



        // update form layout positions and required fields on left side.
        if(isset($_POST['formlayout'])) {

            // loop through the post array and update the required checkbox and field position
            foreach ($_POST as $key => $value) :

                // since there's some $_POST values we don't want to process, only give us the
                // numeric ones which means it contains a meta_id and we want to update it
                if(is_numeric($key)) {

                    // check to prevent php "notice: undefined index: field_req" msg when php strict warnings is on
                    if(!isset($value['field_req'])) $value['field_req'] = '';

                    $update = "UPDATE " . $wpdb->prefix . "cp_ad_meta SET "
                            . "field_req = '" . $wpdb->escape(appthemes_clean($value['field_req'])) . "' "
                            . "WHERE meta_id ='" . $key ."'";

                    $wpdb->query($update);

                } // end if_numeric

            endforeach; // end for each

        } // end isset $_POST


        // check to prevent php "notice: undefined index" msg when php strict warnings is on
        if(isset($_GET['del_id'])) $theswitch = $_GET['del_id']; else $theswitch ='';

        // Remove items from form layout
        if($theswitch) {
            $delete = "DELETE FROM " . $wpdb->prefix . "cp_ad_meta "
                    . "WHERE meta_id = '". $_GET['del_id'] ."'";

            $wpdb->query($delete);
        }
        ?>


        <table>
            <tr style="vertical-align:top;">
                <td style="width:800px;padding:0 20px 0 0;">

				
                <h3><?php _e('Form Layout','appthemes') ?> - <?php echo ucfirst(urldecode($_GET['title'])) ?>&nbsp;&nbsp;&nbsp;&nbsp;<span id="loading"></span></h3>

                <form method="post" id="mainform" action="">

                    <table class="widefat">
                        <thead>
                            <tr>
                                <th scope="col" colspan="2"><?php _e('Form Preview','appthemes') ?></th>
                                <th scope="col" style="width:75px;text-align:center;"><?php _e('Required','appthemes') ?></th>
                                <!-- <th scope="col" style="width:75px;text-align:center;"><?php _e('Order','appthemes') ?></th> -->
                                <th scope="col" style="width:75px;text-align:center;"><?php _e('Remove','appthemes') ?></th>
                            </tr>
                        </thead>



                        <tbody class="sortable">

                        <?php

                            // If this is the first time this form is being customized then auto
                            // create the core fields and put in cp_meta db table
                            echo cp_add_core_fields($_GET['id']);


                            // Then go back and select all the fields assigned to this
                            // table which now includes the added core fields.
                            $sql = "SELECT f.field_label,f.field_type,f.field_values,f.field_perm,m.meta_id,m.field_pos,m.field_req,m.form_id "
                                 . "FROM ". $wpdb->prefix . "cp_ad_fields f "
                                 . "INNER JOIN ". $wpdb->prefix . "cp_ad_meta m "
                                 . "ON f.field_id = m.field_id "
                                 . "WHERE m.form_id = '" . $_GET['id'] . "' "
                                 . "ORDER BY m.field_pos asc";

                            $results = $wpdb->get_results($sql);

                            if($results) {

                                echo cp_admin_formbuilder($results);

                            } else {

                        ?>

                        <tr>
                            <td colspan="5" style="text-align: center;"><p><br/><?php _e('No fields have been added to this form layout yet.','appthemes') ?><br/><br/></p></td>
                        </tr>

                        <?php
                            } // end $results
                            ?>

                        </tbody>

                    </table>

                    <p class="submit">
                        <input class="btn button-primary" name="save" type="submit" value="<?php _e('Save Changes','appthemes') ?>" />&nbsp;&nbsp;&nbsp;
                        <input name="cancel" type="button" onClick="location.href='?page=layouts'" value="<?php _e('Cancel','appthemes') ?>" />
                        <input name="formlayout" type="hidden" value="yes" />
                        <input name="form_owner" type="hidden" value="<?php $current_user->user_login ?>" />
                    </p>
                </form>

                </td>
                <td>

                <h3><?php _e('Available Fields','appthemes') ?></h3>

                <form method="post" id="mainform" action="">


                <div class="fields-panel">

                    <table class="widefat">
                        <thead>
                            <tr>
                                <th style="" class="manage-column column-cb check-column" id="cb" scope="col"><input type="checkbox"/></th>
                                <th scope="col"><?php _e('Field Name','appthemes') ?></th>
                                <th scope="col"><?php _e('Type','appthemes') ?></th>
                            </tr>
                        </thead>


                        <tbody>

                        <?php
                        // Select all available fields not currently on the form layout.
                        // Also exclude any core fields since they cannot be removed from the layout.
                        $sql = "SELECT f.field_id,f.field_label,f.field_type "
                             . "FROM ". $wpdb->prefix . "cp_ad_fields f "
                             . "WHERE f.field_id "
                             . "NOT IN (SELECT m.field_id "
                                     . "FROM ". $wpdb->prefix . "cp_ad_meta m "
                                     . "WHERE m.form_id =  '" . $_GET['id'] . "') "
                             . "AND f.field_perm <> '1'";

                        $results = $wpdb->get_results($sql);

                        if($results) {

                            foreach ($results as $result) {
                        ?>

                        <tr class="even">
                            <th class="check-column" scope="row"><input type="checkbox" value="<?php echo $result->field_id; ?>" name="field_id[]"/></th>
                            <td><?php echo $result->field_label; ?></td>
                            <td><?php echo $result->field_type; ?></td>
                        </tr>

                        <?php
                            } // end foreach

                        } else {
                        ?>

                        <tr>
                            <td colspan="4" style="text-align: center;"><p><br/><?php _e('No fields are available.','appthemes') ?><br/><br/></p></td>
                        </tr>

                        <?php
                        } // end $results
                        ?>

                        </tbody>

                    </table>

                </div>

                    <p class="submit"><input class="btn button-primary" name="save" type="submit" value="<?php _e('Add Fields to Form Layout','appthemes') ?>" /></p>
                        <input name="form_id" type="hidden" value="<?php echo $_GET['id']; ?>" />
                        <input name="submitted" type="hidden" value="yes" />


                </form>

                </td>
            </tr>
        </table>

    </div><!-- /wrap -->

    <?php

    break;



    case 'delete':

        // delete the form based on the form id
        cp_delete_form($_GET['id']);
        ?>
        <p style="text-align:center;padding-top:50px;font-size:22px;"><?php _e('Deleting form layout.....','appthemes') ?><br /><br /><img src="<?php echo bloginfo('template_directory') ?>/images/loading.gif" alt="" /></p>
        <meta http-equiv="refresh" content="0; URL=?page=layouts">

    <?php
    break;

    default:

        $sql = "SELECT * "
             . "FROM " . $wpdb->prefix . "cp_ad_forms "
             . "ORDER BY id desc";
        $results = $wpdb->get_results($sql);

    ?>

        <div class="wrap">
        <div class="icon32" id="icon-themes"><br/></div>
        <h2><?php _e('Form Layouts','appthemes') ?>&nbsp;<a class="button add-new-h2" href="?page=layouts&amp;action=addform"><?php _e('Add New','appthemes') ?></a></h2>

        <?php cp_admin_info_box(); ?>

        <p class="admin-msg"><?php _e('Form layouts allow you to create your own custom ad submission forms. Each form is essentially a container for your fields and can be applied to one or all of your categories. If you do not create any form layouts, the default one will be used. To change the default form, create a new form layout and apply it to all categories.','appthemes') ?></p>

        <table id="tblspacer" class="widefat fixed">

            <thead>
                <tr>
                    <th scope="col" style="width:25px;">&nbsp;</th>
                    <th scope="col"><?php _e('Name','appthemes') ?></th>
                    <th scope="col"><?php _e('Description','appthemes') ?></th>
                    <th scope="col"><?php _e('Categories','appthemes') ?></th>
                    <th scope="col" style="width:150px;"><?php _e('Modified','appthemes') ?></th>
                    <th scope="col" style="width:75px;"><?php _e('Status','appthemes') ?></th>
                    <th scope="col" style="text-align:center;width:100px;"><?php _e('Actions','appthemes') ?></th>
                </tr>
            </thead>

            <?php
            if ($results) {
              $rowclass = '';
              $i=1;
            ?>

              <tbody id="list">

            <?php
                foreach( $results as $result ) {

                $rowclass = 'even' == $rowclass ? 'alt' : 'even';
              ?>

                <tr class="<?php echo $rowclass ?>">
                    <td style="padding-left:10px;"><?php echo $i ?>.</td>
                    <td><a href="?page=layouts&amp;action=editform&amp;id=<?php echo $result->id ?>"><strong><?php echo $result->form_label ?></strong></a></td>
                    <td><?php echo $result->form_desc ?></td>
                    <td><?php echo cp_match_cats(unserialize($result->form_cats)) ?></td>
                    <td><?php echo mysql2date(get_option('date_format') .' '. get_option('time_format'), $result->form_modified) ?> <?php _e('by','appthemes') ?> <?php echo $result->form_owner; ?></td>
                    <td><?php echo ucfirst($result->form_status) ?></td>
                    <td style="text-align:center"><a href="?page=layouts&amp;action=formbuilder&amp;id=<?php echo $result->id ?>&amp;title=<?php echo urlencode($result->form_label) ?>"><img src="<?php echo bloginfo('template_directory') ?>/images/layout_add.png" alt="<?php echo _e('Edit form layout','appthemes') ?>" title="<?php echo _e('Edit form layout','appthemes') ?>" /></a>&nbsp;&nbsp;&nbsp;
                        <a href="?page=layouts&amp;action=editform&amp;id=<?php echo $result->id ?>"><img src="<?php echo bloginfo('template_directory') ?>/images/edit.png" alt="<?php echo  _e('Edit form layout','appthemes') ?>" title="<?php echo _e('Edit form layout','appthemes') ?>" /></a>&nbsp;&nbsp;&nbsp;
                        <a onclick="return confirmBeforeDelete();" href="?page=layouts&amp;action=delete&amp;id=<?php echo $result->id ?>"><img src="<?php echo bloginfo('template_directory') ?>/images/cross.png" alt="<?php echo _e('Delete form layout','appthemes') ?>" title="<?php echo _e('Delete form layout','appthemes') ?>" /></a></td>
                </tr>

              <?php

                $i++;

                } // end for each
              ?>

              </tbody>

            <?php

            } else {

            ?>

                <tr>
                    <td colspan="7"><?php _e('No form layouts found.','appthemes') ?></td>
                </tr>

            <?php
            } // end $results
            ?>

            </table>


        </div><!-- end wrap -->

    <?php
    } // end switch
    ?>
    <script type="text/javascript">
        /* <![CDATA[ */
            function confirmBeforeDelete() { return confirm("<?php _e('Are you sure you want to delete this?', 'appthemes'); ?>"); }
            function confirmBeforeRemove() { return confirm("<?php _e('Are you sure you want to remove this?', 'appthemes'); ?>"); }
        /* ]]> */
    </script>

<?php

} // end function


function cp_custom_fields() {
    global $options_new_field, $wpdb, $current_user;

    get_currentuserinfo();
    ?>

    <!-- show/hide the dropdown field values tr -->
    <script type="text/javascript">
        function show(o){
			var d=document.getElementById('field_values_row');
			switch(o.value){
				case 'drop-down': d.style.display='table-row'; break;
				case 'radio': d.style.display='table-row'; break;
				case 'checkbox': d.style.display='table-row'; break;
				default: d.style.display='none';
            }
        }
		//show/hide immediately on document load
		jQuery(document).ready(function() {
			show(document.getElementById('field_type'));
		});
		
    </script>

    <?php

    // check to prevent php "notice: undefined index" msg when php strict warnings is on
    if(isset($_GET['action'])) $theswitch = $_GET['action']; else $theswitch ='';

    switch($theswitch) {

    case 'addfield':
    ?>

        <div class="wrap">
            <div class="icon32" id="icon-themes"><br/></div>
            <h2><?php _e('New Custom Field','appthemes') ?></h2>

            <?php cp_admin_info_box(); ?>

        <?php
        // check and make sure the form was submitted
        if(isset($_POST['submitted'])) {


            $insert = "INSERT INTO " . $wpdb->prefix . "cp_ad_fields" .
                    " (field_name, field_label, field_desc, field_tooltip, field_type, field_values, field_search, field_owner, field_created) " .
                    "VALUES ('" .
                        $wpdb->escape(appthemes_clean(cp_make_custom_name($_POST['field_label']))) . "','" .
                        $wpdb->escape(appthemes_clean($_POST['field_label'])) . "','" .
                        $wpdb->escape(appthemes_clean($_POST['field_desc'])) . "','" .
                        $wpdb->escape(esc_attr(appthemes_clean($_POST['field_tooltip']))) . "','" .
                        $wpdb->escape(appthemes_clean($_POST['field_type'])) . "','" .
                        $wpdb->escape(appthemes_clean($_POST['field_values'])) . "','" .
                        $wpdb->escape(appthemes_clean($_POST['field_search'])) . "','" .
                        $wpdb->escape(appthemes_clean($_POST['field_owner'])) . "','" .
                        gmdate('Y-m-d H:i:s') .
                    "')";

            $results = $wpdb->query($insert);


            if ($results) :

                //$lastid = $wpdb->insert_id;
                //echo $lastid;
            ?>

                <p style="text-align:center;padding-top:50px;font-size:22px;"><?php _e('Creating your field.....','appthemes') ?><br /><br /><img src="<?php echo bloginfo('template_directory') ?>/images/loading.gif" alt="" /></p>
                <meta http-equiv="refresh" content="0; URL=?page=fields">

            <?php
            endif;

        } else {
        ?>

                <form method="post" id="mainform" action="">

                    <?php cp_admin_fields($options_new_field) ?>

                    <p class="submit"><input class="btn button-primary" name="save" type="submit" value="<?php _e('Create New Field','appthemes') ?>" />&nbsp;&nbsp;&nbsp;
                        <input name="cancel" type="button" onClick="location.href='?page=fields'" value="<?php _e('Cancel','appthemes') ?>" /></p>
                    <input name="submitted" type="hidden" value="yes" />
                    <input name="field_owner" type="hidden" value="<?php echo $current_user->user_login ?>" />

                </form>

        <?php
        }
        ?>

        </div><!-- end wrap -->

    <?php
    break;


    case 'editfield':
    ?>

        <div class="wrap">
            <div class="icon32" id="icon-themes"><br/></div>
            <h2><?php _e('Edit Custom Field','appthemes') ?></h2>

            <?php cp_admin_info_box(); ?>

        <?php
        if(isset($_POST['submitted']) && $_POST['submitted'] == 'yes') {

            $update = "UPDATE " . $wpdb->prefix . "cp_ad_fields SET" .
                    " field_name = '" . $wpdb->escape(appthemes_clean($_POST['field_name'])) . "'," .
                    " field_label = '" . $wpdb->escape(appthemes_clean($_POST['field_label'])) . "'," .
                    " field_desc = '" . $wpdb->escape(appthemes_clean($_POST['field_desc'])) . "'," .
                    " field_tooltip = '" . $wpdb->escape(esc_attr(appthemes_clean($_POST['field_tooltip']))) . "'," .
                    " field_type = '" . $wpdb->escape(appthemes_clean($_POST['field_type'])) . "'," .
                    " field_values = '" . $wpdb->escape(appthemes_clean($_POST['field_values'])) . "'," .
                    // " field_search = '" . $wpdb->escape(appthemes_clean($_POST['field_search'])) . "'," .
                    " field_owner = '" . $wpdb->escape(appthemes_clean($_POST['field_owner'])) . "'," .
                    " field_modified = '" . gmdate('Y-m-d H:i:s') . "'" .
                    " WHERE field_id ='" . $_GET['id'] ."'";

            $results = $wpdb->get_row($update);
            ?>

            <p style="text-align:center;padding-top:50px;font-size:22px;"><?php _e('Saving your changes.....','appthemes') ?><br /><br /><img src="<?php echo bloginfo('template_directory') ?>/images/loading.gif" alt="" /></p>
            <meta http-equiv="refresh" content="0; URL=?page=fields">

        <?php
        } else {
        ?>


            <form method="post" id="mainform" action="">

            <?php cp_admin_db_fields($options_new_field, 'cp_ad_fields', 'field_id') ?>

                <p class="submit">
                    <input class="btn button-primary" name="save" type="submit" value="<?php _e('Save changes','appthemes') ?>" />&nbsp;&nbsp;&nbsp;
                    <input name="cancel" type="button" onClick="location.href='?page=fields'" value="<?php _e('Cancel','appthemes') ?>" />
                    <input name="submitted" type="hidden" value="yes" />
                    <input name="field_owner" type="hidden" value="<?php echo $current_user->user_login ?>" />
                </p>

            </form>

        <?php } ?>

        </div><!-- end wrap -->

    <?php
    break;


    case 'delete':

        // check and make sure this fields perms allow deletion
        $sql = "SELECT field_perm "
             . "FROM " . $wpdb->prefix . "cp_ad_fields "
             . "WHERE field_id = '". $_GET['id'] ."' LIMIT 1";

        $results = $wpdb->get_row($sql);

        // if it's not greater than zero, then delete it
        if(!$results->field_perm > 0) {

            $delete = "DELETE FROM " . $wpdb->prefix . "cp_ad_fields "
                    . "WHERE field_id = '". $_GET['id'] ."'";

            $wpdb->query($delete);
        }
        ?>
        <p style="text-align:center;padding-top:50px;font-size:22px;"><?php _e('Deleting custom field.....','appthemes') ?><br /><br /><img src="<?php echo bloginfo('template_directory') ?>/images/loading.gif" alt="" /></p>
        <meta http-equiv="refresh" content="0; URL=?page=fields">

    <?php

    break;


    // cp_custom_fields() show the table of all custom fields
    default:

         $sql = "SELECT field_id, field_name, field_label, field_desc, field_tooltip, field_type, field_perm, field_owner, field_modified "
             . "FROM " . $wpdb->prefix . "cp_ad_fields "
             . "ORDER BY field_name desc";

        $results = $wpdb->get_results($sql);
        ?>

        <div class="wrap">
        <div class="icon32" id="icon-tools"><br/></div>
        <h2><?php _e('Custom Fields','appthemes') ?>&nbsp;<a class="button add-new-h2" href="?page=fields&amp;action=addfield"><?php _e('Add New','appthemes') ?></a></h2>

        <?php cp_admin_info_box(); ?>

        <p class="admin-msg"><?php _e('Custom fields allow you to customize your ad submission forms and collect more information. Each custom field needs to be added to a form layout in order to be visible on your website. You can create unlimited custom fields and each one can be used across multiple form layouts. It is highly recommended to NOT delete a custom field once it is being used on your ads because it could cause ad editing problems for your customers.','appthemes') ?></p>

        <table id="tblspacer" class="widefat fixed">

            <thead>
                <tr>
                    <th scope="col" style="width:25px;">&nbsp;</th>
                    <th scope="col"><?php _e('Name','appthemes') ?></th>
                    <th scope="col" style="width:100px;"><?php _e('Type','appthemes') ?></th>
                    <th scope="col"><?php _e('Description','appthemes') ?></th>
                    <th scope="col" style="width:150px;"><?php _e('Modified','appthemes') ?></th>
                    <th scope="col" style="text-align:center;width:100px;"><?php _e('Actions','appthemes') ?></th>
                </tr>
            </thead>

            <?php
            if ($results) {
            ?>

                <tbody id="list">

                  <?php
                  $rowclass = '';
                  $i=1;

                  foreach($results as $result) {

                    $rowclass = 'even' == $rowclass ? 'alt' : 'even';
                    ?>

                    <tr class="<?php echo $rowclass ?>">
                        <td style="padding-left:10px;"><?php echo $i ?>.</td>
                        <td><a href="?page=fields&amp;action=editfield&amp;id=<?php echo $result->field_id ?>"><strong><?php echo $result->field_label ?></strong></a></td>
                        <td><?php echo $result->field_type ?></td>
                        <td><?php echo $result->field_desc ?></td>
                        <td><?php echo mysql2date(get_option('date_format') .' '. get_option('time_format'), $result->field_modified) ?> <?php _e('by','appthemes') ?> <?php echo $result->field_owner; ?></td>
                        <td style="text-align:center">

                            <?php
                            // show the correct edit options based on perms
                            switch($result->field_perm) {

                                case '1': // core fields no editing
                                ?>

                                    <a href="?page=fields&amp;action=editfield&amp;id=<?php echo $result->field_id ?>"><img src="<?php echo bloginfo('template_directory') ?>/images/edit.png" alt="" /></a>&nbsp;&nbsp;&nbsp;
                                    <img src="<?php echo bloginfo('template_directory'); ?>/images/cross-grey.png" alt="" />

                                <?php
                                break;

                                case '2': // core fields some editing
                                ?>

                                    <a href="?page=fields&amp;action=editfield&amp;id=<?php echo $result->field_id ?>"><img src="<?php echo bloginfo('template_directory') ?>/images/edit.png" alt="" /></a>&nbsp;&nbsp;&nbsp;
                                    <img src="<?php echo bloginfo('template_directory') ?>/images/cross-grey.png" alt="" />

                                <?php
                                break;

                                default: // regular fields full editing
                                    // don't change these two lines to plain html/php. Get t_else error msg
                                    echo '<a href="?page=fields&amp;action=editfield&amp;id='. $result->field_id .'"><img src="'. get_bloginfo('template_directory') .'/images/edit.png" alt="" /></a>&nbsp;&nbsp;&nbsp;';
                                    echo '<a onclick="return confirmBeforeDelete();" href="?page=fields&amp;action=delete&amp;id='. $result->field_id .'"><img src="'. get_bloginfo('template_directory') .'/images/cross.png" alt="" /></a>';

                           } // endswitch
                           ?>

                        </td>
                    </tr>

                <?php
                    $i++;

                  } //end foreach;
                  //} // mystery bracket which makes it work
                  ?>

              </tbody>

            <?php
            } else {
            ?>

                <tr>
                    <td colspan="5"><?php _e('No custom fields found. This usually means your install script did not run correctly. Go back and try reactivating the theme again.','appthemes') ?></td>
                </tr>

            <?php
            } // end $results
            ?>

        </table>

        </div><!-- end wrap -->

    <?php
    } // endswitch
    ?>



    <script type="text/javascript">
        /* <![CDATA[ */
            function confirmBeforeDelete() { return confirm("<?php _e('WARNING: Deleting this field will prevent any existing ads currently using this field from displaying the field value. Deleting fields is NOT recommended unless you do not have any existing ads using this field. Are you sure you want to delete this field?? (This cannot be undone)', 'appthemes'); ?>"); }
        /* ]]> */
    </script>

<?php

} // end function


// deletes all the ClassiPress database tables
function cp_delete_db_tables() {
    global $wpdb, $app_db_tables;

    foreach ($app_db_tables as $key => $value) :

        $sql = "DROP TABLE IF EXISTS ". $wpdb->prefix . $value;
        $wpdb->query($sql);

        printf(__("Table '%s' has been deleted.", 'appthemes'), $value);
        echo '<br/>';

    endforeach;
}


// deletes all the ClassiPress database tables
function cp_delete_all_options() {
    global $wpdb;

    $sql = "DELETE FROM ". $wpdb->options
          ." WHERE option_name like 'cp_%'";
    $wpdb->query($sql);

    echo __("All ClassiPress options have been deleted.", 'appthemes');
}

// flushes the caches
function cp_flush_all_cache() {
	global $wpdb, $app_transients;

	foreach ($app_transients as $key => $value) :
		delete_transient($value);
		$output .= sprintf('<strong>'.__("ClassiPress '%s' cache has been flushed.", 'appthemes' . '</strong><br/>'), $value);
	endforeach;

	return $output;

}

// show all the order transactions
function cp_transactions() {
    global $wpdb;
?>

    <div class="wrap">
        <div class="icon32" id="icon-themes"><br/></div>
        <h2><?php _e('Order Transactions','appthemes') ?></h2>

        <?php cp_admin_info_box(); ?>

        <table id="tblspacer" class="widefat fixed">

            <thead>
                <tr>
                    <th scope="col" style="width:25px;">&nbsp;</th>
                    <th scope="col"><?php _e('Payer Name','appthemes') ?></th>
                    <th scope="col" style="text-align: center;"><?php _e('Payer Status','appthemes') ?></th>
                    <th scope="col"><?php _e('Ad Title','appthemes') ?></th>
                    <th scope="col"><?php _e('Item Description','appthemes') ?></th>
                    <th scope="col" style="width:125px;"><?php _e('Transaction ID','appthemes') ?></th>
                    <th scope="col"><?php _e('Payment Type','appthemes') ?></th>
                    <th scope="col"><?php _e('Payment Status','appthemes') ?></th>
                    <th scope="col"><?php _e('Total Amount','appthemes') ?></th>
                    <th scope="col" style="width:150px;"><?php _e('Date Paid','appthemes') ?></th>
                </tr>
            </thead>

    <?php if (get_option('cp_edition') == 'Personal Edition') { ?>
            <tbody id="list">
                <tr class="even"><td>&nbsp;</td><td colspan="9"><h4><?php printf(__("This feature is not available with your edition of ClassiPress.<br/> <a href='%s' target='_new'>Upgrade now</a> to enable this feature and get complete visibility into your ad listing transactions.",'appthemes'), 'http://appthemes.com/cp/member.php?tab=add_renew'); ?></h4></td></tr>
            </tbody>
        </table>

    <?php } else {

        // must be higher than personal edition so let's query the db
        $sql = "SELECT o.*, p.post_title "
             . "FROM " . $wpdb->prefix . "cp_order_info o, $wpdb->posts p "
             . "WHERE o.ad_id = p.id "
             . "ORDER BY o.id desc";

        $results = $wpdb->get_results($sql);

            if ($results) {
              $rowclass = '';
              $i=1;
            ?>

              <tbody id="list">

            <?php
                foreach( $results as $result ) {

                $rowclass = 'even' == $rowclass ? 'alt' : 'even';
              ?>

                <tr class="<?php echo $rowclass ?>">
                    <td style="padding-left:10px;"><?php echo $i ?>.</td>

                    <td><strong><?php echo $result->first_name ?> <?php echo $result->last_name ?></strong><br/><a href="mailto:<?php echo $result->payer_email ?>"><?php echo $result->payer_email ?></a></td>
                    <td style="text-align: center;">
                        <?php if ($result->payer_status == 'verified') { ?><img src="<?php bloginfo('template_directory'); ?>/images/paypal_verified.gif" alt="" title="" /><br/><?php } ?>
                        <?php echo ucfirst($result->payer_status) ?>
                    </td>
                    <td><a href="post.php?action=edit&post=<?php echo $result->ad_id ?>"><?php echo $result->post_title ?></a></td>
                    <td><?php echo $result->transaction_subject ?></td>
                    <td><?php echo $result->txn_id ?></td>
                    <td><?php echo ucfirst($result->payment_type) ?></td>
                    <td><?php echo ucfirst($result->payment_status) ?></td>
                    <td><?php echo $result->mc_gross ?> <?php echo $result->mc_currency ?></td>
                    <td><?php echo mysql2date(get_option('date_format') .' '. get_option('time_format'), $result->payment_date) ?></td>
                </tr>

              <?php

                $i++;

                } // end for each
              ?>

              </tbody>

            <?php

            } else {

            ?>

                <tr>
                    <td colspan="7"><?php _e('No transactions found.','appthemes') ?></td>
                </tr>

            <?php
            } // end $results
            ?>

            </table> <!-- this is ok -->


        </div><!-- end wrap -->
<?php
    } // end edition check
}


// system information page
function cp_system_info() {
    global $system_info, $wpdb;
?>

        <div class="wrap">
            <div class="icon32" id="icon-options-general"><br/></div>
            <h2><?php _e('ClassiPress System Info','appthemes') ?></h2>

            <?php cp_admin_info_box(); ?>

            <?php
            // delete all the db tables if the button has been pressed.
            if (isset($_POST['deletetables']))
                cp_delete_db_tables();

            // delete all the cp config options from the wp_options table if the button has been pressed.
            if (isset($_POST['deleteoptions']))
                cp_delete_all_options();

			// flush the cache if the button has been pressed.
			if (isset($_POST['flushcache']))
				echo cp_flush_all_cache();
            ?>


                <table class="widefat fixed" id="tblspacer" style="width:850px;">

                    <thead>
                        <tr>
                            <th scope="col" width="200px"><?php _e('Debug Info','appthemes')?></th>
                            <th scope="col">&nbsp;</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td class="titledesc"><?php _e('ClassiPress Version','appthemes')?></td>
                            <td class="forminp"><?php echo get_option('cp_version'); ?> (<?php echo get_option('cp_edition') ?>)</td>
                        </tr>

                        <tr>
                            <td class="titledesc"><?php _e('WordPress Version','appthemes')?></td>
                            <td class="forminp"><?php if (is_multisite()) echo 'WP Multisite'; else echo 'WP'; ?> <?php if(function_exists('bloginfo')) echo bloginfo('version'); ?></td>
                        </tr>

                        <tr>
                            <td class="titledesc"><?php _e('PHP Version','appthemes')?></td>
                            <td class="forminp"><?php if(function_exists('phpversion')) echo phpversion(); ?></td>
                        </tr>

                        <tr>
                            <td class="titledesc"><?php _e('Server Software','appthemes')?></td>
                            <td class="forminp"><?php echo $_SERVER['SERVER_SOFTWARE']; ?></td>
                        </tr>

                        <tr>
                            <td class="titledesc"><?php _e('UPLOAD_MAX_FILESIZE','appthemes')?></td>
                            <td class="forminp"><?php if(function_exists('phpversion')) echo ini_get('upload_max_filesize'); ?></td>
                        </tr>

                        <tr>
                            <td class="titledesc"><?php _e('DISPLAY_ERRORS','appthemes')?></td>
                            <td class="forminp"><?php if(function_exists('phpversion')) echo ini_get('display_errors'); ?></td>
                        </tr>
<!--
                        <tr>
                            <td class="titledesc"><?php //_e('Session GC_MAXLIFETIME','appthemes')?></td>
                            <td class="forminp"><?php //if(function_exists('phpversion')) echo (number_format(ini_get('session.gc_maxlifetime'))); ?> <?php //_e('seconds','appthemes')?>
                            (<?php //if(function_exists('phpversion')) echo (ini_get('session.gc_maxlifetime')/60); ?> <?php //_e('minutes','appthemes')?>)
                            </td>
                        </tr>

                        <tr>
                            <td class="titledesc"><?php _e('Session CACHE_EXPIRE','appthemes')?></td>
                            <td class="forminp"><?php //if(function_exists('phpversion')) echo (number_format(ini_get('session.cache_expire'))); ?> <?php //_e('seconds','appthemes')?>
                            (<?php //if(function_exists('phpversion')) echo (ini_get('session.cache_expire')/60); ?> <?php //_e('minutes','appthemes')?>)
                            </td>
                        </tr>
-->
                        <tr>
                            <td class="titledesc"><?php _e('FSOCKOPEN Check','appthemes')?></td>
                            <td class="forminp"><?php if(function_exists('fsockopen')) echo '<font color="green">' . __('Your server supports fsockopen so PayPal IPN should work. If not, make sure your server is SSL enabled and port 443 is open on the firewall.', 'appthemes'). '</font>'; else echo '<font color="red">' . __('Your server does not support fsockopen so PayPal IPN will not work.', 'appthemes'). '</font>'; ?></td>
                        </tr>

                        <tr>
                            <td class="titledesc"><?php _e('GD Library Check','appthemes')?></td>
                            <td class="forminp"><?php if (extension_loaded('gd') && function_exists('gd_info')) echo '<font color="green">' . __('It appears your server supports the GD Library which is required in order for the legacy image resizer script (TimThumb) to work.', 'appthemes'). '</font>'; else echo '<font color="red">' . __('Your server does not have the GD Library enabled so the legacy image resizer script (TimThumb) will not work. Most servers with PHP 4.3+ includes this by default.', 'appthemes'). '</font>'; ?></td>
                        </tr>

                        <tr>
                            <td class="titledesc"><?php _e('Theme Path','appthemes')?></td>
                            <td class="forminp"><?php if(function_exists('bloginfo')) { echo bloginfo('template_url'); } ?></td>
                        </tr>

                        <tr>
                            <td class="titledesc"><?php _e('Image Upload Path','appthemes')?></td>
                            <td class="forminp"><?php $uploads = wp_upload_dir(); echo $uploads['url'];?>
                            <?php if (!appthemes_is_wpmu()) printf( ' - <a href="%s">' . __('(change this)', 'appthemes') . '</a>', 'options-media.php' ); ?></td>
                        </tr>

                   <!--

                        <tr>
                            <td class="titledesc"><?php // _e('Image Dir Check','appthemes')?></td>
                            <td class="forminp">
                                <?php
                                // if (!is_dir(CP_UPLOAD_DIR)) {
                                //    printf( '<font color="red">' . __('Image upload directory DOES NOT exist. Create a classipress folder in your %s folder.', 'appthemes'), WP_UPLOAD_DIR ) . '</font>';
                                // } else {
                                //    echo '<font color="green">' . __('Image upload directory exists.','appthemes') . '</font>';
                                // }
                                ?>
                            </td>
                        </tr>

                        <tr>
                            <td class="titledesc"><?php // _e('Image Dir Writable','appthemes')?></td>
                            <td class="forminp">
                            <?php
                            // if (!is_writable(CP_UPLOAD_DIR)) {
                            //    printf( '<font color="red">' . __('Image upload directory is NOT writable. Make sure you have the correct permissions set (CHMOD 777) on your %s folder.', 'appthemes'), CP_UPLOAD_DIR ) . '</font>';
                            // } else {
                            //    echo '<font color="green">' . __('Image upload directory is writable.','appthemes') . '</font>';
                            // }
                            ?>
                            </td>
                        </tr>
                -->


                    </tbody>

                     <thead>
                        <tr>
                            <th scope="col" width="200px"><?php _e('Uninstall ClassiPress','appthemes')?></th>
                            <th scope="col">&nbsp;</th>
                        </tr>
                    </thead>

                <form method="post" id="mainform" action="">
                    <tr>
                        <td class="titledesc"><?php _e('Delete Database Tables','appthemes')?></td>
                        <td class="forminp">
                            <p class="submit"><input onclick="return confirmBeforeDeleteTbls();" name="save" type="submit" value="<?php _e('Delete ClassiPress Database Tables','appthemes') ?>" /><br />
                        <?php _e('Do you wish to completely delete all ClassiPress database tables? Once you do this you will lose any custom fields, forms, ad packs, etc that you have created.','appthemes')?>
                            </p>
                            <input name="deletetables" type="hidden" value="yes" />
                        </td>
                    </tr>
                </form>

                <form method="post" id="mainform" action="">
                    <tr>
                        <td class="titledesc"><?php _e('Delete Config Options','appthemes')?></td>
                        <td class="forminp">
                            <p class="submit"><input onclick="return confirmBeforeDeleteOptions();" name="save" type="submit" value="<?php _e('Delete ClassiPress Config Options','appthemes') ?>" /><br />
                        <?php _e('Do you wish to completely delete all ClassiPress configuration options? This will delete all values saved on the settings, pricing, gateways, etc admin pages from the wp_options database table.','appthemes')?>
                            </p>
                            <input name="deleteoptions" type="hidden" value="yes" />
                        </td>
                    </tr>
                </form>


				<thead>
					<tr>
						<th scope="col" width="200px"><?php _e('ClassiPress Cache','appthemes')?></th>
						<th scope="col">&nbsp;</th>
					</tr>
                    </thead>

					<form method="post" id="mainform" action="">
                    <tr>
                        <td class="titledesc"><?php _e('Flush ClassiPress Cache','appthemes')?></td>
                        <td class="forminp">
                            <p class="submit"><input name="save" type="submit" value="<?php _e('Flush Entire ClassiPress Cache','appthemes') ?>" /><br />
                        <?php _e("Sometimes you may have changed something and it hasn't been updated on your site. Flushing the cache will empty anything that ClassiPress has stored in the cache (i.e. category drop-down menu, home page directory structure, etc).",'appthemes')?>
                            </p>
                            <input name="flushcache" type="hidden" value="yes" />
                        </td>
                    </tr>
                </form>

                </table>


        </div>

        <script type="text/javascript">
        /* <![CDATA[ */
            function confirmBeforeDeleteTbls() { return confirm("<?php _e('WARNING: You are about to completely delete all ClassiPress database tables. Are you sure you want to proceed? (This cannot be undone)', 'appthemes'); ?>"); }
            function confirmBeforeDeleteOptions() { return confirm("<?php _e('WARNING: You are about to completely delete all ClassiPress configuration options from the wp_options database table. Are you sure you want to proceed? (This cannot be undone)', 'appthemes'); ?>"); }
        /* ]]> */
        </script>

<?php
}



// load and create all the CP admin pages
function cp_admin_options() {
	add_menu_page(__('ClassiPress'), __('ClassiPress','appthemes'), 8, basename(__FILE__), 'cp_dashboard', FAVICON, THE_POSITION);
	add_submenu_page(basename(__FILE__), __('Dashboard','appthemes'), __('Dashboard','appthemes'), 8, basename(__FILE__), 'cp_dashboard');
	add_submenu_page(basename(__FILE__), __('General Settings','appthemes'), __('Settings','appthemes'), 8, 'settings', 'cp_settings');
	add_submenu_page(basename(__FILE__), __('Emails','appthemes'), __('Emails','appthemes'), 8, 'emails', 'cp_emails');
	add_submenu_page(basename(__FILE__), __('Pricing Options','appthemes'), __('Pricing','appthemes'), 8, 'pricing', 'cp_pricing');
	add_submenu_page(basename(__FILE__), __('Ad Packs','appthemes'), __('Ad Packs','appthemes'), 8, 'adpacks', 'cp_ad_packs');
	add_submenu_page(basename(__FILE__), __('Coupons','appthemes'), __('Coupons','appthemes'), 8, 'coupons', 'cp_coupons');
	add_submenu_page(basename(__FILE__), __('Payment Gateway Options','appthemes'), __('Gateways','appthemes'), 8, 'gateways', 'cp_gateways');
	add_submenu_page(basename(__FILE__), __('Form Layouts','appthemes'), __('Form Layouts','appthemes'), 8, 'layouts', 'cp_form_layouts');
	add_submenu_page(basename(__FILE__), __('Custom Fields','appthemes'), __('Custom Fields','appthemes'), 8, 'fields', 'cp_custom_fields');
	add_submenu_page(basename(__FILE__), __('Transactions','appthemes'), __('Transactions','appthemes'), 8, 'transactions', 'cp_transactions');
	add_submenu_page(basename(__FILE__), __('System Info','appthemes'), __('System Info','appthemes'), 8, 'sysinfo', 'cp_system_info');
}

add_action('admin_menu', 'cp_admin_options');

?>
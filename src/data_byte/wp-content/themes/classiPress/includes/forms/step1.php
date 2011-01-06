<?php
/**
 * This is step 1 of 3 for the ad submission form
 *
 * @package ClassiPress
 * @subpackage New Ad
 * @author AppThemes
 * @copyright 2010
 *
 *
 */


global $userdata;
global $wpdb;
?>


  <div id="step1"></div>

      <h2 class="dotted"><?php _e('Submit Your Listing','appthemes');?></h2>

            <img src="<?php bloginfo('template_url'); ?>/images/step1.gif" alt="" class="stepimg" />


            <?php 
                // display the custom message
                echo get_option('cp_ads_form_msg');
            ?>

 
            <p class="dotted">&nbsp;</p>

        <?php
            // show the category dropdown when first arriving to this page.
            // Also show it if cat equals -1 which means the 'select one' option was submitted
            if(!isset($_POST['cat']) || ($_POST['cat'] == '-1'))  {

        ?>

                <form name="mainform" id="mainform" class="form_step" action="" method="post">

                    <ol>

                        <li>
                            <label><?php _e('Cost Per Listing','appthemes');?>:</label>
                            <?php cp_cost_per_listing(); ?> <?php // printf(__('for %s days', 'appthemes'), get_option('cp_prun_period')); ?>
                            <div class="clr"></div>
                        </li>

                        <li>
                        <label><?php _e('Select a Category:','appthemes');?></label>

                        <?php

                        if (get_option('cp_price_scheme') == 'category' && get_option('cp_enable_paypal') == 'yes') {

                            cp_dropdown_categories_prices('show_option_none='.__('Select one','appthemes').'&class=dropdownlist&orderby=name&order=ASC&hide_empty=0&hierarchical=1&taxonomy=ad_cat');

                        } else {

                           wp_dropdown_categories('show_option_none='.__('Select one','appthemes').'&class=dropdownlist&orderby=name&order=ASC&hide_empty=0&hierarchical=1&taxonomy=ad_cat');

                        }

                        ?>

                        &nbsp;&nbsp;&nbsp;<input type="submit" name="getcat" id="getcat" class="btn_orange" value="<?php _e('Go','appthemes'); ?>&rsaquo;&rsaquo;" />

                        </li>

                    </ol>

                </form>


            <?php } else {

  
                // show the form based on the category selected
                // get the cat nice name and put it into a variable
                $adCategory = get_term_by('id',$_POST['cat'],'ad_cat');
				$_POST['catname'] = $adCategory->name;
            ?>

                <form name="mainform" id="mainform" class="form_step" action="" method="post" enctype="multipart/form-data">

                    <ol>

                        <li>
                            <label><?php _e('Category','appthemes');?>:</label>
                            <strong><?php echo $_POST['catname']; ?></strong>&nbsp;&nbsp;<small><a href=""><?php _e('(change)', 'appthemes') ?></a></small>
                        </li>

                        <?php echo cp_show_form($_POST['cat']); ?>

                        <p class="btn1">
                            <input type="submit" name="step1" id="step1" class="btn_orange" value="<?php _e('Continue &rsaquo;&rsaquo;','appthemes'); ?>" />
                        </p>

                    </ol>

                        <input type="hidden" id="cat" name="cat" value="<?php echo $_POST['cat']; ?>" />
                        <input type="hidden" id="catname" name="catname" value="<?php echo $_POST['catname']; ?>" />
                        <input type="hidden" id="fid" name="fid" value="<?php if(isset($_POST['fid'])) echo $_POST['fid']; ?>" />
                        <input type="hidden" id="oid" name="oid" value="<?php echo $order_id; ?>" />

                </form>

            <?php } ?>



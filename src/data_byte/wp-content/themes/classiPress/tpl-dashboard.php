<?php
/*
 * Template Name: User Dashboard
 *
 * This template must be assigned to a page
 * in order for it to work correctly
 *
*/

auth_redirect_login(); // if not logged in, redirect to login page
nocache_headers();

global $userdata;
get_currentuserinfo(); // grabs the user info and puts into vars

// include the payment gateway code
include_once (TEMPLATEPATH . '/includes/gateways/paypal/paypal.php');


// check to see if we want to pause or restart the ad
if(!empty($_GET['action'])) :
    $d = trim($_GET['action']);
    $aid = trim($_GET['aid']);

    // make sure author matches ad. Prevents people from trying to hack other peoples ads
    $sql = $wpdb->prepare("SELECT wposts.post_author "
       . "FROM $wpdb->posts wposts "
       . "WHERE ID = $aid "
       . "AND post_author = $userdata->ID");

    $checkauthor = $wpdb->get_row($sql);

    if($checkauthor != null) { // author check is ok. now update ad status

        if ($d == 'pause') {
            $my_ad = array();
            $my_ad['ID'] = $aid;
            $my_ad['post_status'] = 'draft';
            wp_update_post($my_ad);

        } elseif ($d == 'restart') {
            $my_ad = array();
            $my_ad['ID'] = $aid;
            $my_ad['post_status'] = 'publish';
            wp_update_post($my_ad);
		} elseif ($d == 'freerenew') { cp_renew_ad_listing($aid);
		} elseif ($d == 'setSold') { update_post_meta($aid, 'cp_ad_sold', 'yes'); 
		} elseif ($d == 'unsetSold') { update_post_meta($aid, 'cp_ad_sold', 'no'); 
        } else { //echo "nothing here";
        }

    }

endif;

// retrieve all the ads for the current user and don't include blog posts
/////////////////////////////////////////////////
// remember to change the hardcoded cat ids!
/////////////////////////////////////////////////
$sql = "SELECT ID, post_title, post_name, post_status, post_date "
     . "FROM $wpdb->posts "
     . "WHERE post_author = $userdata->ID AND post_type = 'ad_listing' "
     . "AND (post_status = 'publish' OR post_status = 'pending' OR post_status = 'draft') "
     . "AND $wpdb->posts.ID "
     . "ORDER BY ID DESC";

$pageposts = $wpdb->get_results($sql);

$i = 1;
?>

<?php get_header(); ?>


<!-- CONTENT -->
  <div class="content">

    <div class="content_botbg">

      <div class="content_res">


        <!-- left block -->
        <div class="content_left">

            <div class="shadowblock_out">
            <div class="shadowblock">

                <h1 class="single dotted"><?php printf(__("%s's Dashboard", 'appthemes'), $userdata->user_login); ?></h1>

                <p><?php _e('Below you will find a listing of all your classified ads. Click on one of the options to perform a specific task. If you have any questions, please contact the site administrator.','appthemes');?></p>

                <table border="0" cellpadding="4" cellspacing="1" class="tblwide">
                    <thead>
                        <tr>
                            <th width="5px">&nbsp;</th>
                            <th class="text-left">&nbsp;<?php _e('Title','appthemes');?></th>
							<th width="40px"><?php _e('Views','appthemes');?></th>
                            <th width="80px"><?php _e('Status','appthemes');?></th>
                            <th width="90px"><div style="text-align: center;"><?php _e('Options','appthemes');?></div></th>
                        </tr>
                    </thead>
                    <tbody>

                    <?php if ($pageposts): ?>

                        <?php foreach ($pageposts as $post): ?>

                        <?php setup_postdata($post); ?>

                        <?php                     
                            // check to see if ad is legacy or not and then format date based on WP options
                            if(get_post_meta($post->ID, 'expires', true))
                                $expire_date = date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime(get_post_meta($post->ID, 'expires', true)));
                            else
                                $expire_date = date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime(get_post_meta($post->ID, 'cp_sys_expire_date', true)));

                            
                            // get the ad total cost and legacy check
                            if (get_post_meta($post->ID, 'cp_totalcost', true))
                                $total_cost = get_post_meta($post->ID, 'cp_totalcost', true);
                            else
                                $total_cost = get_post_meta($post->ID, 'cp_sys_total_ad_cost', true);

                            // get the prune period and legacy check
                            //  if (get_post_meta($post->ID, 'cp_sys_ad_duration', true))
                            //      $prun_period = get_post_meta($post->ID, 'cp_sys_ad_duration', true);
                            //  else
                            //      $prun_period = get_option('cp_prun_period');

							if (get_post_meta($post->ID, 'cp_total_count', true))
								$ad_views = number_format(get_post_meta($post->ID, 'cp_total_count', true));
							else
								$ad_views = '-';


                            // now let's figure out what the ad status and options should be
                            // it's a live and published ad
                            if ($post->post_status == 'publish') {

                                $poststatus = __('Live','appthemes');
								$poststatus .= ' ' . __('Until','appthemes') . '<br/><p class="small">(' . $expire_date . ')</p>';

                                $fontcolor = '#33CC33';
                                $postimage = 'pause.png';
                                $postalt =  __('pause ad','appthemes');
                                $postaction = 'pause';

                            // it's a pending ad which gives us several possibilities
                            } elseif ($post->post_status == 'pending') {


                                // ad is free and waiting to be approved
                                if ($total_cost == 0) {
                                    $poststatus = __('awaiting approval','appthemes');
                                    $fontcolor = '#C00202';
                                    $postimage = '';
                                    $postalt = '';
                                    $postaction = 'pending';

                                // ad hasn't been paid for yet
                                } else {
                                    $poststatus = __('awaiting payment','appthemes');
                                    $fontcolor = '#C00202';
                                    $postimage = '';
                                    $postalt = '';
                                    $postaction = 'pending';
                                }

                                

                            } elseif ($post->post_status == 'draft') {
							
							//handling issue where date format needs to be unified
                            if(get_post_meta($post->ID, 'expires', true))
                                $expire_date = get_post_meta($post->ID, 'expires', true);
                            else
                                $expire_date = get_post_meta($post->ID, 'cp_sys_expire_date', true);

                                // current date is past the expires date so mark ad ended
                                if (strtotime(date('Y-m-d H:i:s')) > (strtotime($expire_date))) {
                                    $poststatus = __('ended','appthemes') . '<br/><p class="small">(' . $expire_date . ')</p>';
                                    $fontcolor = '#666666';
                                    $postimage = '';
                                    $postalt = '';
                                    $postaction = 'ended';

                                // ad has been paused by ad owner
                                } else {
                                    $poststatus = __('offline','appthemes');
                                    $fontcolor = '#bbbbbb';
                                    $postimage = 'start-blue.png';
                                    $postalt = __('restart ad','appthemes');
                                    $postaction = 'restart';
                                }

                            } else {
                                    $poststatus = '&mdash;';
                            }
                        ?>


                        <tr class="even">
                            <td class="text-right"><?php echo $i; ?>.</td>
                            <td><h3>
                                <?php if ($post->post_status == 'pending' || $post->post_status == 'draft' || $poststatus == 'ended' || $poststatus == 'offline') { ?>

                                    <?php the_title(); ?>

                                <?php } else { ?>

                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>

                                <?php } ?>    
                                </h3>

                                <div class="meta"><span class="folder"><?php echo get_the_term_list(get_the_id(), 'ad_cat', '', ', ', ''); ?></span> | <span class="clock"><span><?php the_time(get_option('date_format'))?></span></span></div>

                            </td>

							<td class="text-center"><?php echo $ad_views; ?></td>

                            <td class="text-center"><span style="color:<?php echo $fontcolor ?>;"><?php echo ucfirst($poststatus) ?></span></td>

                            <td class="text-center">
                                <?php 

                                if ($post->post_status == 'pending' && $postaction != 'ended') {

                                    // show the paypal button if the ad has not been paid for yet
                                    if (($total_cost != 0) && (get_option('cp_enable_paypal') != 'no')) {
                                        echo cp_dashboard_paypal_button($post->ID, 'dashboard');
                                    } else {
                                        echo '&mdash;';
                                    }

                                } elseif ($post->post_status == 'draft' && $postaction == 'ended') {

                                    if (get_option('cp_allow_relist') == 'yes') {
                                        // show the paypal button so they can relist their ad only
                                        // if it's not a legacy ad and they originally paid to list
                                        if (($total_cost != 0) && get_post_meta($post->ID, 'cp_totalcost', true) == '') {
                                            if(get_option('cp_enable_paypal') != 'no') echo cp_dashboard_paypal_button($post->ID, 'dashboard');
											else _e('Contact us to ');
                                            echo __('Relist Ad', 'appthemes');
                                        } else {
                                            echo '<a href="' . CP_DASHBOARD_URL . '?aid=' . $post->ID . '&amp;action=freerenew">' . __('Relist Ad', 'appthemes') . '</a>';
                                        }
                                    } else {
                                        echo '&mdash;';
                                    }


                                } else { ?>

                              <?php if(get_option('cp_ad_edit') == 'yes'): ?><a href="<?php echo CP_EDIT_URL; ?>?aid=<?php the_id(); ?>"><img src="<?php bloginfo('template_directory'); ?>/images/pencil.png" title="" alt="" border="0" /></a>&nbsp;&nbsp;<?php endif; ?>
                              <a href="<?php echo CP_DASHBOARD_URL; ?>?aid=<?php the_id(); ?>&amp;action=<?php echo $postaction; ?>"><img src="<?php bloginfo('template_directory'); ?>/images/<?php echo $postimage; ?>" title="" alt="" border="0" /></a><br />
                              <?php if(get_post_meta(get_the_id(), 'cp_ad_sold', true) != 'yes' ) : ?>
							 <a href="<?php echo CP_DASHBOARD_URL; ?>?aid=<?php the_id(); ?>&amp;action=setSold"><?php _e('Mark Sold', 'appthemes'); ?></a>
                             <?php else : ?>
							 <a href="<?php echo CP_DASHBOARD_URL; ?>?aid=<?php the_id(); ?>&amp;action=unsetSold"><?php _e('Unmark Sold', 'appthemes'); ?></a>
							 <?php endif; ?>
                          <?php } ?>


                            </td>
                        </tr>

                        <?php
                        $i++;
                    
                        endforeach; 
                        ?>

                    <?php else : ?>

                        <tr class="even">
                            <td colspan="4">

                                <div class="pad10"></div>

                        <p class="text-center"><?php _e('You currently have no classified ads.','appthemes');?></p>

                        <div class="pad25"></div>

                        </td>
                        </tr>

                    <?php endif; ?>


                    </tbody>
                </table>



            </div><!-- /shadowblock -->

            </div><!-- /shadowblock_out -->



        </div><!-- /content_left -->


        <?php get_sidebar('user'); ?>

        <div class="clr"></div>


      </div><!-- /content_res -->

    </div><!-- /content_botbg -->

  </div><!-- /content -->


<?php get_footer(); ?>

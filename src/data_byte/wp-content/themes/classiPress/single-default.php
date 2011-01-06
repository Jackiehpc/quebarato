<?php get_header(); ?>

<script type="text/javascript">
// <![CDATA[
    Shadowbox.init({
        overlayOpacity: 0.5
    });
// ]]>
</script>


<!-- CONTENT -->
  <div class="content">

      <div class="content_botbg">

          <div class="content_res">

              <div id="breadcrumb">

                  <?php if(function_exists('cp_breadcrumb')) cp_breadcrumb(); ?>

              </div>

                <div class="content_left">
	

		<?php if(have_posts()) : ?>

                        <?php while(have_posts()) : the_post() ?>


                            <div class="shadowblock_out">

                                <div class="shadowblock">

                                    <p class="bigprice"><?php if(get_post_meta($post->ID, 'price', true)) cp_get_price_legacy($post->ID); else cp_get_price($post->ID); ?></p>

                                        <h2 class="dotted"><span class="colour"><a href="<?php the_permalink() ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></span></h2>


                                        <div class="bigright">
                                        

                                        <ul>

                                        <?php
                                        // grab the category id for the functions below
                                        $category = get_the_category();

                                        // check to see if ad is legacy or not
                                        if(get_post_meta($post->ID, 'expires', true)) {  ?>

                                            <li><span><?php _e('Location', 'cp') ?>:</span> <?php echo get_post_meta($post->ID, 'location', true); ?></li>
                                            <li><span><?php _e('Phone', 'cp') ?>:</span> <?php echo get_post_meta($post->ID, 'phone', true); ?></li>

                                            <?php if(get_post_meta($post->ID, 'cp_adURL', true)) ?>
                                                <li><span><?php _e('URL','cp'); ?>:</span> <?php echo cp_make_clickable(get_post_meta($post->ID, 'cp_adURL', true)); ?></li>

                                            <li><span><?php _e('Listed', 'cp') ?>:</span> <?php the_time(get_option('date_format') . ' ' . get_option('time_format')) ?></li>
                                            <li><span><?php _e('Expires', 'cp') ?>:</span> <?php echo cp_timeleft(strtotime(get_post_meta($post->ID, 'expires', true))); ?></li>

                                        <?php

                                        } else {

                                            // 3.0+ display the custom fields instead (but not text areas)
                                            cp_get_ad_details($post->ID, get_cat_ID($category[0]->cat_name));
                                        ?>

                                            <li id="cp_listed"><span><?php _e('Listed', 'cp') ?>:</span> <?php the_time(get_option('date_format') . ' ' . get_option('time_format')) ?></li>
                                            
                                            <?php if (get_post_meta($post->ID, 'cp_sys_expire_date', true)) ?>
                                                <li id="cp_expires"><span><?php _e('Expires', 'cp') ?>:</span> <?php echo cp_timeleft(strtotime(get_post_meta($post->ID, 'cp_sys_expire_date', true))); ?></li>

                                        <?php 
                                        } // end legacy check
                                        ?>

                                        </ul>

                                        </div><!-- /bigright -->

				<?php if(get_option('cp_ad_images') == 'yes') { ?>

                                    <div class="bigleft">


                                        <div id="main-pic">

                                            <?php if(get_post_meta($post->ID, 'images', true)) cp_single_image_legacy($post->ID, get_option('medium_size_w'), get_option('medium_size_h'), ''); else cp_get_image_url($post->ID, 'medium', $class = 'img-main', 1); ?>

                                            <div class="clr"></div>
                                        </div>

                                        <div id="thumbs-pic">

                                            <?php if(get_post_meta($post->ID, 'images', true)) echo cp_get_image_thumbs_legacy($post->ID, get_option('thumbnail_size_w'), get_option('thumbnail_size_h'), $post->post_title); else cp_get_image_url_single($post->ID, 'thumbnail', $post->post_title, -1); ?>

                                            <div class="clr"></div>
                                        </div>

                                    </div><!-- /bigleft -->

                                <?php } ?>

				 <div class="clr"></div>
					
					<div class="single-main">
                                            <?php 	
                                            // 3.0+ display text areas in content area before content.
                                            cp_get_ad_details($post->ID, get_cat_ID($category[0]->cat_name), 'content');													
                                            ?>
											
                                            <h3 class="description-area"><?php _e('Description','cp'); ?></h3>
                                            <?php the_content(); ?>
					</div>	

					<div class="prdetails">
					
                                            <p class="tags"><?php the_tags( '', ', ', ''); ?></p>

                                            <p class="stats"><?php if (function_exists('cp_ad_views_today')) { cp_ad_views_today($post->ID, '', __('total views','cp'), __('so far today','cp'), 0, 'show'); } ?></p>

                                            <p class="print"><?php if(function_exists('wp_email')) { email_link(); } ?>&nbsp;&nbsp;<?php if(function_exists('wp_print')) { print_link(); } ?></p>

                                            <p class="edit"><?php edit_post_link(__('Edit Listing','cp'), '', ''); ?></p>
		
					</div>


                                        <?php if(function_exists('selfserv_sexy')) { selfserv_sexy(); } ?>
					

                            </div><!-- /shadowblock -->

                        </div><!-- /shadowblock_out -->

			<?php endwhile; else: ?>

                            <p><?php _e('Sorry, no listings matched your criteria.','cp'); ?></p>

                        <?php endif; ?>


                        <div class="clr"></div>


			<?php
                        // show the ad block if it's been activated
                        if (get_option('cp_adcode_336x280_enable') == 'yes') {

                            if(function_exists('cp_single_ad_336x280')) { ?>

                            <div class="shadowblock_out">

                                <div class="shadowblock">

                                  <h2 class="dotted"><?php _e('Sponsored Links','cp') ?></h2>

                                  <?php cp_single_ad_336x280(); ?>

                                </div><!-- /shadowblock -->

                            </div><!-- /shadowblock_out -->

                        <?php
                            }
                        }
                        ?>

                        <?php wp_reset_query(); ?>


                        <div class="clr"></div>


                           <?php comments_template(); ?>


                </div><!-- /content_left -->


                <?php get_sidebar('ad'); ?>


            <div class="clr"></div>


      </div><!-- /content_res -->

    </div><!-- /content_botbg -->

  </div><!-- /content -->



<?php get_footer(); ?>
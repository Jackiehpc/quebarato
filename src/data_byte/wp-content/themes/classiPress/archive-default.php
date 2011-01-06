<?php get_header(); ?>

<!-- CONTENT -->
  <div class="content">

    <div class="content_botbg">

      <div class="content_res">

        <div id="breadcrumb">

          <?php if(function_exists('cp_breadcrumb')) cp_breadcrumb(); ?>

        </div>

        <!-- left block -->
        <div class="content_left">
		
            <?php $cat = get_query_var('cat'); // get the active category ID ?>

            <div class="shadowblock_out">

                <div class="shadowblock">

                  <div id="catrss"><a href="<?php get_category_link($cat) ?>feed/"><img src="<?php bloginfo('template_url'); ?>/images/rss.png" width="16" height="16" alt="<?php single_cat_title(); ?> <?php _e('RSS Feed', 'cp') ?>" title="<?php single_cat_title(); ?> <?php _e('RSS Feed', 'cp') ?>" /></a></div>
                  <h2><?php _e('Listings for','cp')?> <?php single_cat_title(); ?> (<?php echo $wp_query->found_posts ?>)</h2>

                </div><!-- /shadowblock -->

            </div><!-- /shadowblock_out -->


                <?php if(have_posts()) : ?>

		<?php while(have_posts()) : the_post() ?>

                <?php if (in_category(CP_BLOG_CAT_ID) || cp_post_in_desc_cat(CP_BLOG_CAT_ID) ) continue; ?>


                  <div class="whiteblock can_chg_bg">

                        <a href="<?php the_permalink(); ?>"><?php if(get_post_meta($post->ID, 'images', true)) cp_single_image_legacy($post->ID, get_option('medium_size_w'), get_option('medium_size_h')); else cp_get_image($post->ID, 'medium', 1); ?></a>

                        <div class="priceblockbig">

                            <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

                            <p class="price"><?php if(get_post_meta($post->ID, 'price', true)) cp_get_price_legacy($post->ID); else cp_get_price($post->ID); ?></p>

                            <div class="clr"></div>

                            <p class="bot4px dotted"><?php _e('Category','cp') ?>: <?php the_category(', ') ?> | <?php _e('Listed','cp') ?>: <?php echo cp_ad_posted($post->post_date); ?></p>
                            <p class="descr"><?php echo substr(strip_tags($post->post_content), 0, 200)."...";?></p>

                            <div class="clr"></div>

                        </div>

                        <div class="clr"></div>

                  </div><!-- /whiteblock -->

                <?php endwhile; ?>

                  <?php if(function_exists('cp_pagination')) { cp_pagination(); } ?>

                <?php else: ?>

                  <div class="shadowblock_out">

                <div class="shadowblock">

                    <?php if (is_category()) { ?>

			<h2><?php _e('There are not any listings in this category yet.', 'cp') ?></h2>

                    <?php } else if (is_date()) { ?>

                        <h2><?php _e('There are not any listings with this date.', 'cp') ?></h2>

                    <?php } else if (is_author()) {

                        $userdata = get_userdatabylogin(get_query_var('author_name')); ?>
                        <h2><?php printf(__('There are not any listings by %s yet.', 'cp'), $userdata->display_name) ?></h2>

                    <?php } else { ?>

                        <h2><?php _e('No listings found.', 'cp') ?></h2>

                    <?php } ?>

                        <div class="pad25"></div>

                    <div class="clr"></div>

                 </div><!-- /shadowblock -->

            </div><!-- /shadowblock_out -->


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


                        <div class="clr"></div>
                  
                   <?php endif; ?>

		<?php wp_reset_query(); ?>

   

	</div><!-- /content_left -->


        <?php get_sidebar(); ?>

        <div class="clr"></div>



      </div><!-- /content_res -->

    </div><!-- /content_botbg -->

  </div><!-- /content -->



<?php get_footer(); ?>
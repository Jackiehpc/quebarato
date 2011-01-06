<?php get_header(); ?>

<!-- CONTENT -->
<div class="content">

    <div class="content_botbg">

        <div class="content_res">

          <?php include_once('featured.php'); ?>

        <!-- left block -->
        <div class="content_left">

            <?php if (get_option('cp_home_layout') == 'directory'): ?>

                <div class="shadowblock_out">

                    <div class="shadowblock">

                        <h2 class="dotted"><?php _e('Ad Categories','appthemes')?></h2>

                        <div id="directory" class="directory <?php if(get_option('cp_cat_dir_cols') == 2) echo 'two'; ?>Col">


                            <?php echo cp_cat_menu_drop_down(get_option('cp_cat_dir_cols'), get_option('cp_dir_sub_num')); ?>


                            <div class="clr"></div>

                        </div><!--/directory-->

                    </div><!-- /shadowblock -->

                </div><!-- /shadowblock_out -->

            <?php endif; ?>


        <div class="tabcontrol">

            <ul class="tabnavig">
              <li><a href="#block1"><span class="big"><?php _e('Just Listed','appthemes')?></span></a></li>
              <li><a href="#block2"><span class="big"><?php _e('Most Popular','appthemes')?></span></a></li>
              <li><a href="#block3"><span class="big"><?php _e('Random','appthemes')?></span></a></li>
            </ul>
            

            <!-- tab 1 -->
            <div id="block1">

              <div class="clr"></div>

              <div class="undertab"><span class="big"><?php _e('Classified Ads','appthemes') ?> / <strong><span class="colour"><?php _e('Just Listed','appthemes') ?></span></strong></span></div>

                <?php
                    // show all ads but make sure the sticky featured ads don't show up first
                    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
                    query_posts(array('post_type' => 'ad_listing', 'caller_get_posts' => 1, 'paged' => $paged));
                ?>

                <?php get_template_part('loop', 'ad'); ?>

            </div><!-- /block1 -->



            <!-- tab 2 -->
            <div id="block2">

              <div class="clr"></div>

              <div class="undertab"><span class="big"><?php _e('Classified Ads','appthemes') ?> / <strong><span class="colour"><?php _e('Most Popular','appthemes') ?></span></strong></span></div>

                  <?php
                  // give us the most popular ads based on page views
                  $sql = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "cp_ad_pop_total a "
                       . "INNER JOIN " . $wpdb->posts . " p ON p.ID = a.postnum "
                       . "WHERE postcount > 0 AND post_status = 'publish' AND post_type = 'ad_listing' "
                       . "ORDER BY postcount DESC LIMIT 10");

                  $pageposts = $wpdb->get_results($sql);
                  ?>

                 <?php if ($pageposts): ?>

                     <?php foreach ($pageposts as $post): ?>

                         <?php setup_postdata($post); ?>

							  <div class="ad-block">

								  <div class="ad-left">

									  <?php if(get_option('cp_ad_images') == 'yes') cp_ad_loop_thumbnail(); ?>

								  </div>

								  <div class="<?php if(get_option('cp_ad_images') == 'yes') echo 'ad-right'; else echo 'ad-right-no-img'; ?> <?php echo get_option('cp_ad_right_class'); ?>">

									  <h3><a href="<?php the_permalink(); ?>"><?php if (mb_strlen(get_the_title()) >= 75) echo mb_substr(get_the_title(), 0, 75).'...'; else the_title(); ?></a></h3>

									  <div class="price-wrap">
										  <span class="tag-head">&nbsp;</span><p class="ad-price"><?php if(get_post_meta($post->ID, 'price', true)) cp_get_price_legacy($post->ID); else cp_get_price($post->ID); ?></p>
									  </div>

									  <div class="clr"></div>

									  <p class="ad-meta">
										  <span class="folder"><?php if (get_the_category()) the_category(', '); else echo get_the_term_list($post->ID, 'ad_cat', '', ', ', ''); ?></span> | <span class="owner"><?php if (get_option('cp_ad_gravatar_thumb') == 'yes') appthemes_get_profile_pic(get_the_author_meta('ID'), get_the_author_meta('user_email'), 16) ?><?php the_author_posts_link(); ?></span> | <span class="clock"><span><?php echo appthemes_date_posted($post->post_date); ?></span></span></p>

									  <p class="ad-desc"><?php echo mb_substr(strip_tags($post->post_content), 0, 150).'...';?></p>

									  <p class="stats"><?php if (get_option('cp_ad_stats_all') == 'yes') appthemes_get_stats($post->ID); ?></p>

									  <div class="clr"></div>

								  </div>

								  <div class="clr"></div>

							  </div><!-- /ad-block -->

                    <?php endforeach; ?>

                      <?php if(function_exists('appthemes_pagination')) appthemes_pagination(); ?>

                <?php else: ?>

                    <div class="whiteblock can_chg_bg">

                        <h3><?php _e('Sorry, no listings were found.','appthemes')?></h3>
                        
                    </div><!-- /whiteblock -->

                <?php endif; ?>

		<?php wp_reset_query(); ?>

            </div><!-- /block2 -->


            <!-- tab 3 -->
            <div id="block3">

              <div class="clr"></div>

              <div class="undertab"><span class="big"><?php _e('Classified Ads','appthemes') ?> / <strong><span class="colour"><?php _e('Random','appthemes') ?></span></strong></span></div>

                <?php
                    // show all random ads but make sure the sticky featured ads don't show up first
                    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
                    query_posts(array('post_type' => 'ad_listing', 'caller_get_posts' => 1, 'paged' => $paged, 'orderby' => rand,));
                ?>

                <?php get_template_part('loop', 'ad'); ?>

            </div><!-- /block3 -->

          </div><!-- /tabcontrol -->

      </div><!-- /content_left -->


            <?php get_sidebar(); ?>


            <div class="clr"></div>

        </div><!-- /content_res -->

    </div><!-- /content_botbg -->

</div><!-- /content -->


<?php get_footer(); ?>

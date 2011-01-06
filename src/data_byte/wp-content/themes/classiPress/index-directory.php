<?php get_header(); ?>

<!-- CONTENT -->
  <div class="content">

    <div class="content_botbg">

      <div class="content_res">

        <?php if (get_option('cp_enable_featured') <> 'no') { ?>

          <?php query_posts(array('post__in' => get_option('sticky_posts'), 'post_status' => 'publish', 'orderby' => 'rand')); ?>

            <?php if (have_posts()) : ?>

            <!-- featured listings -->
            <div class="shadowblock_out">
                <div class="shadowblockdir">

                    <h2 class="dotted"><?php _e('Featured Listings', 'cp')?></h2>
                    <div class="sliderblockdir">

                        <div id="list">
                            <div class="prev"><img src="<?php bloginfo('template_url'); ?>/images/prev.jpg" alt="" /></div>
                            <div class="slider">
                                <ul>

                                    <?php while (have_posts()) : the_post(); ?>

                                        <?php if (in_category(CP_BLOG_CAT_ID) || cp_post_in_desc_cat(CP_BLOG_CAT_ID)) continue; // don't show any blog posts ?>

                                        <li>
                                            <span class="feat_left"><a href="<?php the_permalink() ?>" title="<?php the_title(); ?>"><?php if(get_post_meta($post->ID, 'images', true)) cp_single_image_legacy($post->ID, get_option('thumbnail_size_w'), get_option('thumbnail_size_h')); else cp_get_image_url_feat($post->ID, 'thumbnail', 'captify', 1); ?></a><div class="clr"></div><span class="price_sm"><?php if(get_post_meta($post->ID, 'price', true)) cp_get_price_legacy($post->ID); else cp_get_price($post->ID); ?></span></span>
                                            <p><a href="<?php the_permalink() ?>"><?php if (strlen(get_the_title()) >= get_option('cp_featured_trim')) echo substr(get_the_title(), 0, get_option('cp_featured_trim')).'...'; else the_title(); ?></a> </p>
                                        </li>

                                    <?php endwhile; ?>

                                </ul>
                            </div>

                            <div class="next"><img src="<?php bloginfo('template_url'); ?>/images/next.jpg" alt="" /></div>

                        </div><!-- /slider -->

                        <div class="clr"></div>

                    </div><!-- /sliderblock -->

                </div><!-- /shadowblock -->

            </div><!-- /shadowblock_out -->

            <?php endif; ?>

            <?php wp_reset_query(); ?>

        <?php } // end feature ad slider check ?>

        <!-- left block -->
        <div class="content_left">


            <div class="shadowblock_out">

                <div class="shadowblock">

                    <h2 class="dotted"><?php _e('Ad Categories','cp')?></h2>

                    <div id="directory" class="directory">


                        <?php cp_cat_menu_drop_down(get_option('cp_cat_dir_cols'), get_option('cp_dir_sub_num')); ?>


                        <div class="clr"></div>

                    </div><!--/directory-->

                </div><!-- /shadowblock -->

            </div><!-- /shadowblock_out -->


        <div class="tabcontrol">
            <ul class="tabnavig">
              <li><a href="#block1"><span class="big"><?php _e('Just Listed','cp')?></span></a></li>
              <li><a href="#block2"><span class="big"><?php _e('Most Popular','cp')?></span></a></li>
              <li><a href="#block3"><span class="big"><?php _e('Random','cp')?></span></a></li>
            </ul>

            <!-- tab 1 -->
            <div id="block1">
              <div class="clr"></div>
              <div class="undertab"><span class="big"><a href="#"><?php _e('Classified Ads','cp') ?></a> / <strong><span class="colour"><?php _e('Just Listed','cp') ?></span></strong></span></div>


                <?php
                    // show all ads but make sure the sticky featured ads don't show up first
                    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
                    $args = array('caller_get_posts' => 1, 'paged' => $paged,'category__not_in' => cp_get_blog_cat_ids_array(),);
                ?>

                <?php query_posts($args); ?>

                <?php if (have_posts()) : ?>

                    <?php while (have_posts()) : the_post(); ?>

                    <?php // if(is_sticky()) continue; // don't show any sticky posts in the main section ?>

                        <?php // if (in_category(CP_BLOG_CAT_ID) || cp_post_in_desc_cat(CP_BLOG_CAT_ID)) continue; // don't show any blog posts ?>


                      <div class="whiteblock can_chg_bg">

                          <a href="<?php the_permalink(); ?>"><?php if(get_post_meta($post->ID, 'images', true)) cp_single_image_legacy($post->ID, get_option('medium_size_w'), get_option('medium_size_h')); else cp_get_image($post->ID, 'medium', 1); ?></a>

                            <div class="priceblockbig">

                                <h3><a href="<?php the_permalink(); ?>"><?php if (strlen(get_the_title()) >= 45) echo substr(get_the_title(), 0, 45).'...'; else the_title(); ?></a></h3>

                                <p class="price"><?php if(get_post_meta($post->ID, 'price', true)) cp_get_price_legacy($post->ID); else cp_get_price($post->ID); ?></p>

                                <div class="clr"></div>

                                <p class="bot4px dotted"><?php _e('Category','cp') ?>: <?php the_category(', ') ?> | <?php _e('Listed','cp') ?>: <?php echo cp_ad_posted($post->post_date); ?></p>
                                <p class="descr"><?php echo substr(strip_tags($post->post_content), 0, 200).'...';?></p>

                                <div class="clr"></div>

                            </div>

                            <div class="clr"></div>

                      </div><!-- /whiteblock -->

                    <?php endwhile; ?>

                    <?php if(function_exists('cp_pagination')) cp_pagination(); ?>

                <?php else: ?>
                    <div class="whiteblock can_chg_bg">
                        <h3><?php _e('Sorry, no listings were found.','cp')?></h3>
                    </div><!-- /whiteblock -->

                <?php endif; ?>

		<?php wp_reset_query(); ?>

            </div><!-- /block1 -->




            <!-- tab 2 -->
            <div id="block2">
              <div class="clr"></div>
              <div class="undertab"><span class="big"><a href="#"><?php _e('Classified Ads','cp') ?></a> / <strong><span class="colour"><?php _e('Most Popular','cp') ?></span></strong></span></div>

                  <?php
                  // give us the most popular ads based on page views
                  $sql = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "cp_ad_pop_total a "
                       . "INNER JOIN " . $wpdb->posts . " p ON p.ID = a.postnum "
                       . "WHERE postcount > 0 AND post_status = 'publish' "
                       . "ORDER BY postcount DESC LIMIT 10");

                  $pageposts = $wpdb->get_results($sql);
                  ?>

                 <?php if ($pageposts): ?>

                 <?php foreach ($pageposts as $post): ?>

                 <?php setup_postdata($post); ?>

                <?php // if(is_sticky()) continue; // don't show any sticky posts in the main section ?>

                <?php if (in_category(CP_BLOG_CAT_ID) || cp_post_in_desc_cat(CP_BLOG_CAT_ID)) continue; // don't show any blog posts ?>


                  <div class="whiteblock can_chg_bg">

                        <a href="<?php the_permalink(); ?>"><?php if(get_post_meta($post->ID, 'images', true)) cp_single_image_legacy($post->ID, get_option('medium_size_w'), get_option('medium_size_h')); else cp_get_image($post->ID, 'medium', 1); ?></a>

                        <div class="priceblockbig">

                            <h3><a href="<?php the_permalink(); ?>"><?php if (strlen(get_the_title()) >= 45) echo substr(get_the_title(), 0, 45).'...'; else the_title(); ?></a></h3>

                            <p class="price"><?php if(get_post_meta($post->ID, 'price', true)) cp_get_price_legacy($post->ID); else cp_get_price($post->ID); ?></p>

                            <div class="clr"></div>

                            <p class="bot4px dotted"><?php _e('Category','cp') ?>: <?php the_category(', ') ?> | <?php _e('Listed','cp') ?>: <?php echo cp_ad_posted($post->post_date); ?></p>
                            <p class="descr"><?php echo substr(strip_tags($post->post_content), 0, 200)."...";?></p>

                            <div class="clr"></div>

                        </div>

                        <div class="clr"></div>

                  </div><!-- /whiteblock -->

                <?php endforeach; ?>

                  <?php // if(function_exists('cp_pagination')) cp_pagination(); ?>

                <?php else: ?>
                    <div class="whiteblock can_chg_bg">
                        <h3><?php _e('Sorry, no listings were found.','cp')?></h3>
                    </div><!-- /whiteblock -->

                <?php endif; ?>

		<?php wp_reset_query(); ?>

            </div><!-- /block2 -->


            <!-- tab 3 -->
            <div id="block3">
              <div class="clr"></div>
              <div class="undertab"><span class="big"><a href="#"><?php _e('Classified Ads','cp') ?></a> / <strong><span class="colour"><?php _e('Random','cp') ?></span></strong></span></div>

                <?php
                    // show all random ads but make sure the sticky featured ads don't show up first
                    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
                    $args = array('caller_get_posts' => 1, 'paged' => $paged, 'orderby' => rand,'category__not_in' => cp_get_blog_cat_ids_array(),);
                ?>

                <?php query_posts($args); ?>

                <?php if (have_posts()) : ?>

                    <?php while (have_posts()) : the_post(); ?>

                    <?php // if(is_sticky()) continue; // don't show any sticky posts in the main section?>

                    <?php // if (in_category(CP_BLOG_CAT_ID) || cp_post_in_desc_cat(CP_BLOG_CAT_ID)) continue; // don't show any blog posts ?>


                      <div class="whiteblock can_chg_bg">

                            <a href="<?php the_permalink(); ?>"><?php if(get_post_meta($post->ID, 'images', true)) cp_single_image_legacy($post->ID, get_option('medium_size_w'), get_option('medium_size_h')); else cp_get_image($post->ID, 'medium', 1); ?></a>

                            <div class="priceblockbig">

                                <h3><a href="<?php the_permalink(); ?>"><?php if (strlen(get_the_title()) >= 45) echo substr(get_the_title(), 0, 45).'...'; else the_title(); ?></a></h3>

                                <p class="price"><?php if(get_post_meta($post->ID, 'price', true)) cp_get_price_legacy($post->ID); else cp_get_price($post->ID); ?></p>

                                <div class="clr"></div>

                                <p class="bot4px dotted"><?php _e('Category','cp') ?>: <?php the_category(', ') ?> | <?php _e('Listed','cp') ?>: <?php echo cp_ad_posted($post->post_date); ?></p>
                                <p class="descr"><?php echo substr(strip_tags($post->post_content), 0, 200)."...";?></p>

                                <div class="clr"></div>

                            </div>

                            <div class="clr"></div>

                      </div><!-- /whiteblock -->

                    <?php endwhile; ?>

                  <?php if(function_exists('cp_pagination')) cp_pagination(); ?>

                <?php else: ?>
                    <div class="whiteblock can_chg_bg">
                        <h3><?php _e('Sorry, no listings were found.','cp')?></h3>
                    </div><!-- /whiteblock -->

                <?php endif; ?>

		<?php wp_reset_query(); ?>

            </div><!-- /block3 -->


          </div><!-- /tabcontrol -->


            </div><!-- /content_left -->


            <?php get_sidebar(); ?>

            <div class="clr"></div>


        </div><!-- /content_res -->

    </div><!-- /content_botbg -->

</div><!-- /content -->


<?php get_footer(); ?>

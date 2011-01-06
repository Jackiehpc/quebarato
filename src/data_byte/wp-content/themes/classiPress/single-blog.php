<?php get_header(); ?>

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

                                    <div class="post">

                                        <h2 class="dotted"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a>
                                            <p class="meta"><?php _e('Posted','cp'); ?> <?php echo cp_ad_posted($post->post_date); ?> <?php _e('by','cp'); ?> <?php the_author_posts_link(); ?> <?php _e('in','cp'); ?> <?php the_category(', ') ?> | <?php comments_popup_link( __('No comments yet', 'cp'), __('1 comment', 'cp'), __('% comments', 'cp')); ?></p>
                                        </h2>
                                        
                                        <?php if (has_post_thumbnail()) { the_post_thumbnail('blog-thumbnail');} ?>
						 
                                        <?php the_content(); ?>

                                
                                        <div class="prdetails">

                                            <p class="tags"><?php the_tags( '', ', ', ''); ?></p>

                                            <p class="stats"><?php if (function_exists('cp_ad_views_today')) { cp_ad_views_today($post->ID, '', __('total views','cp'), __('so far today','cp'), 0, 'show'); } ?></p>

                                            <p class="print"><?php if(function_exists('wp_email')) { email_link(); } ?>&nbsp;&nbsp;<?php if(function_exists('wp_print')) { print_link(); } ?></p>

                                            <p class="edit"><?php edit_post_link(__('Edit Ad','cp'), '', ''); ?></p>


					</div>


                                        <?php if(function_exists('selfserv_sexy')) { selfserv_sexy(); } ?>

					

                                    </div><!--/post-->

                            </div><!-- /shadowblock -->

                        </div><!-- /shadowblock_out -->

			<?php endwhile; else: ?>

                            <p><?php _e('Sorry, no posts matched your criteria.','cp'); ?></p>

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


                        <div class="clr"></div>

                            <?php comments_template(); ?>
        
                </div><!-- /content_left -->


                <?php get_sidebar('blog'); ?>


            <div class="clr"></div>


      </div><!-- /content_res -->

    </div><!-- /content_botbg -->

  </div><!-- /content -->



<?php get_footer(); ?>
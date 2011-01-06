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

							<?php appthemes_stats_update($post->ID); //records the page hit ?>

							<div class="shadowblock_out">

								<div class="shadowblock">

									<div class="post">

										<div class="comment-bubble"><?php comments_popup_link('0', '1', '%'); ?></div>

										<h1 class="single blog"><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></h1>

										<p class="meta dotted"><span class="user"><?php the_author_posts_link(); ?></span> | <span class="folderb"><?php the_category(', ') ?></span> | <span class="clock"><span><?php echo appthemes_date_posted($post->post_date); ?></span></span></p>

										<?php if (has_post_thumbnail()) { the_post_thumbnail('blog-thumbnail');} ?>

										<?php the_content(); ?>

										<div class="prdetails">

											<p class="tags"><?php if(get_the_tags()) echo the_tags( '', '&nbsp;', ''); else echo __('No Tags', 'appthemes'); ?></p>
											<?php if (get_option('cp_ad_stats_all') == 'yes') { ?><p class="stats"><?php appthemes_stats_counter($post->ID); ?></p> <?php } ?>
											<p class="print"><?php if(function_exists('wp_email')) { email_link(); } ?>&nbsp;&nbsp;<?php if(function_exists('wp_print')) { print_link(); } ?></p>
											<?php edit_post_link('<p class="edit">'.__('Edit Post','appthemes'), '', '').'</p>'; ?>

										</div>

										<?php if(function_exists('selfserv_sexy')) { selfserv_sexy(); } ?>

									</div><!--#post-->

								</div><!-- #shadowblock -->

							</div><!-- #shadowblock_out -->

						<?php endwhile; ?>

							<?php if(function_exists('appthemes_pagination')) { appthemes_pagination(); } ?>

					<?php else: ?>

						<p><?php _e('Sorry, no posts matched your criteria.','appthemes'); ?></p>

					<?php endif; ?>

                        <div class="clr"></div>

						<?php
                        // show the ad block if it's been activated
                        if (get_option('cp_adcode_336x280_enable') == 'yes') :

                            if(function_exists('appthemes_single_ad_336x280')) { ?>

                            <div class="shadowblock_out">

                                <div class="shadowblock">

                                  <h2 class="dotted"><?php _e('Sponsored Links','appthemes') ?></h2>

                                  <?php appthemes_single_ad_336x280(); ?>

                                </div><!-- /shadowblock -->

                            </div><!-- /shadowblock_out -->

                            <?php } ?>

                        <?php endif; ?>


                        <div class="clr"></div>

                            <?php comments_template(); ?>

                </div><!-- /content_left -->


                <?php get_sidebar('blog'); ?>


            <div class="clr"></div>

      </div><!-- /content_res -->

    </div><!-- /content_botbg -->

  </div><!-- /content -->

<?php get_footer(); ?>
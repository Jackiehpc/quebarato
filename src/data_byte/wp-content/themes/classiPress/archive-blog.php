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

                        <?php $category = get_the_category(); // To show only 1 Category ?>

                            <div class="shadowblock_out">

                                <div class="shadowblock">

                                    <div class="post">

                                        <h2 class="dotted"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a>
                                            <p class="meta"><?php _e('Posted','cp'); ?> <?php echo cp_ad_posted($post->post_date); ?> <?php _e('by','cp'); ?> <?php the_author_posts_link(); ?> <?php _e('in','cp'); ?> <?php the_category(', ') ?> | <?php comments_popup_link( __('No comments yet', 'cp'), __('1 comment', 'cp'), __('% comments', 'cp')); ?></p>
                                        </h2>

                                        <?php if (has_post_thumbnail()) { the_post_thumbnail('blog-thumbnail');} ?>

                                        <?php the_content('Continue reading &raquo;', 'cp'); ?>

                                    </div><!-- /post-->

                                   </div><!-- /shadowblock -->

                            </div><!-- /shadowblock_out -->


                            <?php endwhile; ?>

                                <?php if(function_exists('cp_pagination')) { cp_pagination(); } ?>

                            <?php else: ?>

                                <p><?php _e('Sorry, no posts matched your criteria.','cp'); ?></p>

                            <?php endif; ?>

                
                        <div class="clr"></div>

   
                    </div><!-- /content_left -->


                <?php get_sidebar('blog'); ?>


            <div class="clr"></div>

      </div><!-- /content_res -->

    </div><!-- /content_botbg -->

  </div><!-- /content -->


<?php get_footer(); ?>

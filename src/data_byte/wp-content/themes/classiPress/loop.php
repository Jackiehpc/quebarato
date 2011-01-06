<?php
/**
 * Main loop for displaying blog posts
 *
 * @package ClassiPress
 * @author AppThemes
 *
 */
?>

<?php if(have_posts()) : ?>

    <?php while(have_posts()) : the_post() ?>

        <div class="shadowblock_out">

            <div class="shadowblock">

                <div class="post">

                    <div class="comment-bubble"><?php comments_popup_link('0', '1', '%'); ?></div>

                    <h1 class="single blog"><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></h1>

					<p class="meta dotted"><span class="user"><?php the_author_posts_link(); ?></span> | <span class="folderb"><?php the_category(', ') ?></span> | <span class="clock"><span><?php echo appthemes_date_posted($post->post_date); ?></span></span></p>
                    
                    <?php // hack needed for "<!-- more -->" to work with templates
                        global $more;
                        $more = 0;
                    ?>

                    <?php if (has_post_thumbnail()) { the_post_thumbnail('blog-thumbnail');} ?>

                    <?php the_content('<p>'.__('Continue reading &raquo;', 'appthemes').'</p>'); ?>

					<p class="stats"><?php if (get_option('cp_ad_stats_all') == 'yes') appthemes_get_stats($post->ID); ?></p>


                </div><!--#post-->

            </div><!-- #shadowblock -->

        </div><!-- #shadowblock_out -->

    <?php endwhile; ?>

        <?php if(function_exists('appthemes_pagination')) { appthemes_pagination(); } ?>

<?php else: ?>

    <p><?php _e('Sorry, no posts matched your criteria.','appthemes'); ?></p>

<?php endif; ?>
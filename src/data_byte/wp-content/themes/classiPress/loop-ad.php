<?php
/**
 * Main loop for displaying ads
 *
 * @package ClassiPress
 * @author AppThemes
 *
 */
?>

<?php if(have_posts()) : ?>

    <?php while(have_posts()) : the_post(); ?>

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

				<p class="ad-desc"><?php if (mb_strlen(get_the_content()) >= 165) echo mb_substr(get_the_content(), 0, 165).'...'; else echo strip_tags(get_the_content()); ?></p>

				<p class="stats"><?php if (get_option('cp_ad_stats_all') == 'yes') appthemes_get_stats($post->ID); ?></p>
				
                <div class="clr"></div>

            </div>

            <div class="clr"></div>

      </div><!-- /ad-block -->

    <?php endwhile; ?>

    <?php if(function_exists('appthemes_pagination')) appthemes_pagination(); ?>

<?php else: ?>

    <div class="shadowblock_out">

		<div class="shadowblock">

			<div class="pad10"></div>

			<p><?php _e('Sorry, no listings were found.','appthemes')?></p>

			<div class="pad50"></div>
        
		</div><!-- /shadowblock -->

	</div><!-- /shadowblock_out -->

<?php endif; ?>

<?php wp_reset_query(); ?>
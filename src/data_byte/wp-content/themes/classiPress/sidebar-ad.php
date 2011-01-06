<?php

global $userdata;

// make sure google maps is enabled
$gmap_check = get_option('cp_gmaps_key');

?>

<!-- right sidebar -->
<div class="content_right">

    
    <div class="tabprice">

        <ul class="tabnavig">
          <?php if (!empty($gmap_check)) { ?>
              <li><a href="#priceblock1"><span class="big"><?php _e('Map', 'appthemes') ?></span></a></li>
          <?php } ?>
          <li><a href="#priceblock2"><span class="big"><?php _e('Contact', 'appthemes') ?></span></a></li>
          <li><a href="#priceblock3"><span class="big"><?php _e('Poster', 'appthemes') ?></span></a></li>
        </ul>

    <?php if (!empty($gmap_check)) { ?>

        <!-- tab 1 -->
        <div id="priceblock1">

            <div class="clr"></div>

                <div class="singletab">

                    <?php include_once(TEMPLATEPATH . '/includes/sidebar-gmap.php' ); ?>

                </div><!-- /singletab -->

        </div>
        
    <?php } ?>

        <!-- tab 2 -->
        <div id="priceblock2">

            <div class="clr"></div>

                <div class="singletab">

              <?php if (get_option('cp_ad_inquiry_form') == 'yes' && is_user_logged_in()) {

                        include_once(TEMPLATEPATH . '/includes/sidebar-contact.php' );

                    } elseif (get_option('cp_ad_inquiry_form') <> 'yes'){

                        include_once(TEMPLATEPATH . '/includes/sidebar-contact.php' );

                    } else {
                    ?>
                        <div class="pad25"></div>
                        <p class="contact_msg center"><strong><?php _e('You must be logged in to inquire about this ad.', 'appthemes') ?></strong></p>
                        <div class="pad100"></div>
              <?php } ?>

                </div><!-- /singletab -->

        </div><!-- /priceblock2 -->




        <!-- tab 3 -->
        <div id="priceblock3">

          <div class="clr"></div>

          <div class="postertab">

            <div class="priceblocksmall dotted">

                <p class="member-title"><?php _e('Information about the ad poster','appthemes');?></p>

                <div id="userphoto">
                    <p class='image-thumb'><?php appthemes_get_profile_pic(get_the_author_meta('ID'), get_the_author_meta('user_email'), 64) ?></p>
                </div>

                <ul class="member">

					<li><span><?php _e('Listed by:','appthemes');?></span>
						<?php
							// check to see if ad is legacy or not
							if(get_post_meta($post->ID, 'name', true)) {
								if (get_the_author() != '') { ?>
									<a href="<?php echo get_author_posts_url(get_the_author_id()); ?>"><?php the_author_meta('display_name'); ?></a>
							<?php
								} else {
									echo get_post_meta($post->ID, 'name', true);
								} ?>

					  <?php } else { ?>
									<a href="<?php echo get_author_posts_url(get_the_author_id()); ?>"><?php the_author_meta('display_name'); ?></a>
						<?php
							}
						?>
					</li>

					<li><span><?php _e('Member Since:','appthemes');?></span> <?php echo date_i18n(get_option('date_format'), strtotime(get_the_author_meta('user_registered'))); ?></li>

              </ul>

              <div class="pad5"></div>

              <div class="clr"></div>

            </div>

              
			<div class="pad5"></div>

			<h3><?php _e('Other items listed by','appthemes'); ?> <?php the_author_meta('display_name'); ?></h3>

			<div class="pad5"></div>

			<ul>                            

				<?php query_posts(array('posts_per_page' => 5, 'post_type' => 'ad_listing', 'post_status' => 'publish', 'author' => get_the_author_meta('ID'), 'orderby' => RAND, 'post__not_in' => array($post->ID))); ?>

				<?php if(have_posts()) : ?>

					<?php while(have_posts()) : the_post() ?>

						<li>
							<a href="<?php the_permalink() ?>"><?php the_title(); ?></a>
						</li>

					<?php endwhile; ?>

				<?php else: ?>

					<li><?php _e('No other ads by this poster found.','appthemes'); ?></li>

				<?php endif; ?>

			</ul>

			<div class="pad5"></div>
                        
			<a href="<?php echo get_author_posts_url(get_the_author_id()); ?>" class="btn"><span><?php _e('View all ads by','appthemes'); ?> <?php the_author_meta('display_name'); ?> &raquo;</span></a>

  
          </div><!-- /singletab -->

        </div><!-- /priceblock3 -->

      </div><!-- /tabprice -->   




<?php if (function_exists('dynamic_sidebar') && dynamic_sidebar('sidebar_ad')) : else : ?>

<!-- no dynamic sidebar so don't do anything -->

<?php endif; ?>


</div><!-- /content_right -->

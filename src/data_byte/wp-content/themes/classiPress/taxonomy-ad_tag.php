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

			<?php
				// show only ads within this specific tag
				$term = get_term_by( 'slug', get_query_var( 'ad_tag' ), 'ad_tag' );
				$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
				query_posts(array('post_type' => 'ad_listing', 'ad_tag' => $term->slug, 'caller_get_posts' => 1, 'paged' => $paged));
			?>

            <div class="shadowblock_out">

                <div class="shadowblock">

                  <div id="catrss"><a href="<?php echo get_term_link($term, $taxonomy); ?>feed/"><img src="<?php bloginfo('template_url'); ?>/images/rss.png" width="16" height="16" alt="<?php echo $term->name; ?> <?php _e('RSS Feed', 'appthemes') ?>" title="<?php echo $term->name; ?> <?php _e('RSS Feed', 'appthemes') ?>" /></a></div>
                  <h2><?php _e('Listings tagged with','appthemes')?> '<?php echo $term->name; ?>' (<?php echo $wp_query->found_posts ?>)</h2>

                </div><!-- /shadowblock -->

            </div><!-- /shadowblock_out -->				


                <?php get_template_part( 'loop', 'ad' ); ?>


	</div><!-- /content_left -->


        <?php get_sidebar(); ?>


        <div class="clr"></div>

      </div><!-- /content_res -->

    </div><!-- /content_botbg -->

  </div><!-- /content -->


<?php get_footer(); ?>
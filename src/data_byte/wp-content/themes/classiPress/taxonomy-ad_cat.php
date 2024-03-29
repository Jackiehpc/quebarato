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
				// show only ads within this specific category
				$term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
				$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
				query_posts(array('post_type' => 'ad_listing', 'ad_cat' => $term->slug, 'caller_get_posts' => 1, 'paged' => $paged));
			?>


            <div class="shadowblock_out">

                <div class="shadowblock">

                  <div id="catrss"><a href="<?php echo get_term_link($term, $taxonomy); ?>feed/"><img src="<?php bloginfo('template_url'); ?>/images/rss.png" width="16" height="16" alt="<?php echo $term->name; ?> <?php _e('RSS Feed', 'appthemes') ?>" title="<?php echo $term->name; ?> <?php _e('RSS Feed', 'appthemes') ?>" /></a></div>
                  <h1 class="single dotted"><?php _e('Listings for','appthemes')?> <?php echo $term->name; ?> (<?php echo $wp_query->found_posts ?>)</h1>

				  <p><?php echo $term->description; ?></p>

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
<?php
/**
 * The Sidebar containing the primary and secondary widget areas.
 *
 * @package WordPress
 * @subpackage Twenty_Ten
 * @since Twenty Ten 1.0
 */
?>

		<div id="primary" class="widget-area" role="complementary">
		  
		  <div class="share-icons">
		    <a href="<?php echo site_url() ?>/feed" target="_blank">
		      <img src="<?php echo get_stylesheet_directory_uri() ?>/images/icons/feed.jpg" title="feed" alt="feed" />
		    </a>
		    <a href="http://twitter.com/quebarato" target="_blank">
          <img src="<?php echo get_stylesheet_directory_uri() ?>/images/icons/twitter.jpg" title="twitter" alt="twitter" />
        </a>
        <a href="http://facebook.com/quebarato" target="_blank">
          <img src="<?php echo get_stylesheet_directory_uri() ?>/images/icons/facebook.jpg" title="facebook" alt="facebook" />
        </a>
        <a href="http://youtube.com/quebarato" target="_blank">
          <img src="<?php echo get_stylesheet_directory_uri() ?>/images/icons/youtube.jpg" title="youtube" alt="youtube" />
        </a>
		  </div>
		  
		  		  
			<ul class="xoxo">

<?php
	/* When we call the dynamic_sidebar() function, it'll spit out
	 * the widgets for that widget area. If it instead returns false,
	 * then the sidebar simply doesn't exist, so we'll hard-code in
	 * some default sidebar stuff just in case.
	 */
	if ( ! dynamic_sidebar( 'primary-widget-area' ) ) : ?>
	
			<li id="search" class="widget-container widget_search">
				<?php get_search_form(); ?>
			</li>

			<li id="archives" class="widget-container">
				<h3 class="widget-title"><?php _e( 'Archives', 'twentyten' ); ?></h3>
				<ul>
					<?php wp_get_archives( 'type=monthly' ); ?>
				</ul>
			</li>

			<li id="meta" class="widget-container">
				<h3 class="widget-title"><?php _e( 'Meta', 'twentyten' ); ?></h3>
				<ul>
					<?php wp_register(); ?>
					<li><?php wp_loginout(); ?></li>
					<?php wp_meta(); ?>
				</ul>
			</li>

		<?php endif; // end primary widget area ?>
			</ul>
		</div><!-- #primary .widget-area -->

<?php
	// A second sidebar for widgets, just because.
	if ( is_active_sidebar( 'secondary-widget-area' ) ) : ?>

		<div id="secondary" class="widget-area" role="complementary">
			<ul class="xoxo">
				<?php dynamic_sidebar( 'secondary-widget-area' ); ?>
			</ul>
		</div><!-- #secondary .widget-area -->

<?php endif; ?>

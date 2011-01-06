<?php global $app_abbr; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

    <head profile="http://gmpg.org/xfn/11">

        <meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

        <title><?php wp_title('|',true,'right'); ?><?php bloginfo('name'); ?></title>

        <link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php if ( get_option('feedburner_url') <> "" ) { echo get_option('feedburner_url'); } else { echo get_bloginfo_rss('rss2_url').'?post_type=ad_listing'; } ?>" />
        <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

        <?php if (file_exists(TEMPLATEPATH.'/images/favicon.ico')) : ?>
                <link rel="shortcut icon" href="<?php bloginfo('stylesheet_directory'); ?>/images/favicon.ico" type="image/x-icon" />
        <?php endif; ?>

        <?php if (is_singular() && get_option('thread_comments')) wp_enqueue_script('comment-reply'); ?>
            
        <?php wp_head(); ?>

    </head>

    <body <?php body_class(); ?>>


    <div class="container">

		<?php if(get_option('cp_debug_mode') == 'yes'){ ?><div class="debug"><h3><?php _e('Debug Mode On','appthemes'); ?></h3><?php print_r($wp_query->query_vars); ?></div><?php } ?>

      <!-- HEADER -->
      <div class="header">

        <div class="header_top">

          <div class="header_top_res">

              <p>

              <?php echo cp_login_head(); ?>

              <a href="<?php if (get_option('cp_feedburner_url')) { echo get_option('cp_feedburner_url'); } else { echo get_bloginfo_rss('rss2_url').'?post_type=ad_listing'; } ?>" target="_blank"><img src="<?php bloginfo('template_url'); ?>/images/icon_rss.gif" width="16" height="16" alt="rss" class="srvicon" /></a>

              <?php if (get_option('cp_twitter_username')) : ?>
                  &nbsp;|&nbsp;
                  <a href="http://twitter.com/<?php echo get_option('cp_twitter_username'); ?>" target="_blank"><img src="<?php bloginfo('template_url'); ?>/images/icon_twitter.gif" width="16" height="16" alt="tw" class="srvicon" /></a>
              <?php endif; ?>

              </p>

          </div><!-- /header_top_res -->

        </div><!-- /header_top -->


        <div class="header_main">

          <div class="header_main_bg">

            <div class="header_main_res">

                <div id="logo">

                    <?php if (get_option('cp_use_logo') != 'no') { ?>

                        
                        <?php if (get_option('cp_logo')) { ?>
                            <a href="<?php bloginfo('url'); ?>"><img src="<?php echo get_option('cp_logo'); ?>" alt="<?php bloginfo('name'); ?>" class="header-logo" /></a>
                        <?php } else { ?>
                            <a href="/"><div class="cp_logo"></div></a>
                        <?php } ?>

                    <?php } else { ?>

                        <h1><a href="<?php echo get_option('home'); ?>/"><?php bloginfo('name'); ?></a></h1>
                        <div class="description"><?php bloginfo('description'); ?></div>

                    <?php } ?>

                    
                </div>

                <?php if (get_option('cp_adcode_468x60_enable') == 'yes') { ?>

                    <div class="adblock">

                        <?php appthemes_header_ad_468x60();?>

                    </div><!-- /adblock -->

              <?php } ?>

             <div class="clr"></div>

            </div><!-- /header_main_res -->

          </div><!-- /header_main_bg -->

        </div><!-- /header_main -->


        <div class="header_menu">

          <div class="header_menu_res">

              <a href="<?php echo CP_ADD_NEW_URL ?>" class="obtn btn_orange"><?php _e('Post an Ad', 'appthemes') ?></a>

            <ul id="nav"> 
			  			
              <li class="<?php if (is_home()) echo 'page_item current_page_item'; ?>"><a href="<?php echo get_option('home')?>"><?php _e('Home','appthemes'); ?></a></li>
              <li class="mega"><a href="#"><?php _e('Categories','appthemes'); ?></a>
                  <div class="adv_categories" id="adv_categories">

                        <?php echo cp_cat_menu_drop_down(get_option('cp_cat_menu_cols'), get_option('cp_cat_menu_sub_num')); ?>

                  </div><!-- /adv_categories -->
              </li>
                <?php wp_nav_menu(array('theme_location' => 'primary', 'fallback_cb' => 'appthemes_default_menu', 'container' => '')); ?>
            </ul>

            <div class="clr"></div>

            

          </div><!-- /header_menu_res -->

        </div><!-- /header_menu -->

      </div><!-- /header -->

	<?php include_once(TEMPLATEPATH . '/includes/theme-searchbar.php'); ?>

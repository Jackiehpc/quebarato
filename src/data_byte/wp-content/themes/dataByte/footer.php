GOLIMAR!!
<!-- FOOTER -->
  <div class="footer">
  teste
    <div class="footer_menu">

      <div class="footer_menu_res">

          <ul>
              <li class="first"><a href="<?php echo get_option('home')?>"><?php _e('Home','appthemes'); ?></a></li>
              <?php wp_list_pages('sort_column=menu_order&depth=1&title_li=&exclude='.get_option('cp_excluded_pages')); ?>              
          </ul>

        <div class="clr"></div>

      </div><!-- /footer_menu_res -->
        
    </div><!-- /footer_menu -->

    <div class="footer_main">

      <div class="footer_main_res">

        <div class="dotted">

              <?php if (function_exists('dynamic_sidebar') && dynamic_sidebar('sidebar_footer')) : else : ?> <!-- no dynamic sidebar so don't do anything --> <?php endif; ?>

          <div class="clr"></div>

        </div><!-- /dotted -->

        <p>&copy; <?php echo date_i18n('Y'); ?> <?php bloginfo('name'); ?>. <?php _e('All Rights Reserved.', 'appthemes'); ?></p>
        
        <?php if (get_option('cp_twitter_username')) : ?>
            <a href="http://twitter.com/<?php echo get_option('cp_twitter_username'); ?>" target="_blank"><img src="<?php bloginfo('template_url'); ?>/images/twitter_bot.gif" width="42" height="50" alt="Twitter" class="twit" /></a>
        <?php endif; ?>

        <div class="right">
            <p><a target="_blank" href="http://appthemes.com/themes/classipress/" title="Classified Ads Software"><?php _e('Classified Ads Software','appthemes'); ?></a> | <?php _e('Powered by','appthemes'); ?> <a target="_blank" href="http://www.wordpress.org/" title="WordPress">WordPress</a></p>
        </div>

        <div class="clr"></div>
        
      </div><!-- /footer_main_res -->

    </div><!-- /footer_main -->

    <?php wp_footer(); ?>

  </div><!-- /footer -->


</div><!-- /container -->


</body>
</html>

<!-- FOOTER -->
  <div class="footer">
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
            <p><a target="_blank" href="http://www.quebarato.com.br" title="">powered by QueBarato!</a></p>
        </div>

        <div class="clr"></div>
        
      </div><!-- /footer_main_res -->

    </div><!-- /footer_main -->

    <?php wp_footer(); ?>

  </div><!-- /footer -->


</div><!-- /container -->
		
		<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-4923065-39']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>

</body>
</html>
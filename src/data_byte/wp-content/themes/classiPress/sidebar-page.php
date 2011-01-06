<!-- right block -->
    <div class="content_right">


    <?php if (function_exists('dynamic_sidebar') && dynamic_sidebar('sidebar_page')) : else : ?>

    <!-- no dynamic sidebar setup -->

    <div class="shadowblock_out">
        <div class="shadowblock">
          <h2 class="dotted"><?php _e('BlogRoll', 'appthemes') ?></h2>

          <ul>

              <?php wp_list_bookmarks('title_li=&categorize=0'); ?>

          </ul>

            <div class="clr"></div>
        </div>
      </div>

     <div class="shadowblock_out">
        <div class="shadowblock">
          <h2 class="dotted"><?php _e('Meta', 'appthemes') ?></h2>

          <ul>

              <?php wp_register(); ?>
              <li><?php wp_loginout(); ?></li>
              <li><a href="http://appthemes.com/" title="Premium WordPress Themes">AppThemes</a></li>
              <li><a href="http://wordpress.org/" title="Powered by WordPress">WordPress</a></li>
              <?php wp_meta(); ?>

          </ul>

            <div class="clr"></div>
        </div>
      </div>

    <?php endif; ?>


    </div><!-- /content_right -->
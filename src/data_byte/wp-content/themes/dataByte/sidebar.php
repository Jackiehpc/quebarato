<!-- right block -->
    <div class="content_right">


<?php if (is_home()) { ?>

       <?php
       global $userdata;
       get_currentuserinfo();
       ?>
        
      <div class="shadowblock_out">

        <div class="shadowblock">

            <?php if (!is_user_logged_in()): ?>

                <?php echo get_option('cp_ads_welcome_msg'); ?>          
                <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?action=register" class="mbtn btn_orange"><?php _e('Join Now!', 'appthemes') ?></a>

            <?php else: ?>

                <div class="avatar"><?php appthemes_get_profile_pic($userdata->ID, $userdata->user_email, 60) ?></div>

                <div class="user">

                    <p class="welcome-back"><?php _e('Welcome back,','appthemes'); ?> <strong><?php echo $userdata->user_login; ?></strong>.</p>
                    <p class="last-login"><?php _e('You last logged in at:','appthemes'); ?> <?php appthemes_get_last_login($userdata->ID); ?></p>
                    <p><?php _e('Manage your ads or edit your profile from your personalized dashboard.','appthemes'); ?></p>

                    <div class="pad5"></div>

                    <a href="<?php echo CP_DASHBOARD_URL ?>" class="mbtn btn_orange"><?php _e('Manage Ads', 'appthemes') ?></a>&nbsp;&nbsp;&nbsp;<a href="<?php echo CP_PROFILE_URL ?>" class="mbtn btn_orange"><?php _e('Edit Profile', 'appthemes') ?></a>

                    <div class="pad5"></div>
                    
                <div class="clr"></div>

		</div><!-- /user -->

		<?php endif; ?>


        </div><!-- /shadowblock -->

      </div><!-- /shadowblock_out -->

<?php } ?>


<?php
  if (is_tax('ad_cat')) :

	// go get the taxonomy category id so we can filter with it
	// have to use slug instead of name otherwise it'll break with multi-word cats
	$ad_cat_array = get_term_by('slug', get_query_var('ad_cat'), 'ad_cat', ARRAY_A, $filter);
    ?>

	<div class="shadowblock_out">
		<div class="shadowblock">
			<h2 class="dotted"><?php _e('Sub Categories', 'appthemes') ?></h2>

			<ul>
				<?php
				// show_count=1 causes undefined index notice in WP3.0. Set to 0 to get rid until they fix in WP.
				wp_list_categories('hide_empty=0&orderby=name&show_count=1&title_li=&use_desc_for_title=1&taxonomy=ad_cat&depth=1&child_of=' . $ad_cat_array['term_id']);
				?>
			</ul>

			<div class="clr"></div>
		</div>
	</div>

<?php endif; ?>
      


    <?php if (function_exists('dynamic_sidebar') && dynamic_sidebar('sidebar_main')) : else : ?>

    <!-- no dynamic sidebar setup -->

    <div class="shadowblock_out">
        <div class="shadowblock">
          <h2 class="dotted"><?php _e('Links', 'appthemes') ?></h2>
          </ul>
          
          <ul>
              <li><a href="<?php echo get_bloginfo("url") ?>/add-new">Publicação de Anúncios</a></li>
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
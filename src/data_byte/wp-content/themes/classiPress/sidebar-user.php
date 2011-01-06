<?php global $userdata; ?>

<!-- right sidebar -->
<div class="content_right">


    <div class="shadowblock_out">

        <div class="shadowblock">

            <h2 class="dotted"><?php _e('User Options','appthemes')?></h2>

            <div class="recordfromblog">

                <ul>
                    <li><a href="<?php echo CP_DASHBOARD_URL ?>"><?php _e('My Dashboard','appthemes')?></a></li>
                    <li><a href="<?php echo CP_PROFILE_URL ?>"><?php _e('Edit Profile','appthemes')?></a></li>
                    <?php if (current_user_can('edit_others_posts')) { ?><li><a href="<?php echo get_option('siteurl'); ?>/wp-admin/"><?php _e('WordPress Admin','appthemes')?></a></li><?php } ?>
                    <li><a href="<?php echo wp_logout_url(); ?>"><?php _e('Log Out','appthemes')?></a></li>
                </ul>

            </div><!-- /recordfromblog -->

        </div><!-- /shadowblock -->

    </div><!-- /shadowblock_out -->
	


	<div class="shadowblock_out">

        <div class="shadowblock">

            <h2 class="dotted"><?php _e('Account Information','appthemes')?></h2>

				<div class="avatar"><?php appthemes_get_profile_pic($userdata->ID, $userdata->user_email, 60) ?></div>

                <ul class="user-info">
                    <li><h3 class="single"><a href="<?php echo get_author_posts_url($userdata->ID); ?>"><?php echo $userdata->user_login; ?></a></h3></li>
                    <li><strong><?php _e('Member Since:','appthemes')?></strong> <?php appthemes_get_reg_date($userdata->user_registered); ?></li>
					<li><strong><?php _e('Last Login:','appthemes'); ?></strong> <?php appthemes_get_last_login($userdata->ID); ?></li>
				</ul>

				<ul class="user-details">
                    <li><div class="emailico"></div><a href="mailto:<?php echo $userdata->user_email; ?>"><?php echo $userdata->user_email; ?></a></li>
					<li><div class="twitterico"></div><?php if($userdata->twitter_id) { ?><a href="http://twitter.com/<?php echo $userdata->twitter_id; ?>" target="_blank"><?php _e('Twitter','appthemes')?></a><?php } else { _e('N/A','appthemes'); } ?></li>
					<li><div class="facebookico"></div><?php if($userdata->facebook_id) { ?><a href="http://facebook.com/<?php echo $userdata->facebook_id; ?>" target="_blank"><?php _e('Facebook','appthemes')?></a><?php } else { _e('N/A','appthemes'); } ?></li>
					<li><div class="globeico"></div><?php if($userdata->user_url) { ?><a href="<?php echo $userdata->user_url; ?>" target="_blank"><?php echo $userdata->user_url; ?></a><?php } else { _e('N/A','appthemes'); } ?></li>
				</ul>

        </div><!-- /shadowblock -->

    </div><!-- /shadowblock_out -->



    <div class="shadowblock_out">

        <div class="shadowblock">

            <h2 class="dotted"><?php _e('Account Statistics','appthemes')?></h2>

                <ul class="user-stats">

                <?php
                // calculate the total count of live ads for current user
                $post_count_live = $wpdb->get_var($wpdb->prepare("SELECT count(ID) FROM $wpdb->posts WHERE post_author = $userdata->ID AND post_type = 'ad_listing' AND post_status = 'publish'"));
                $post_count_pending = $wpdb->get_var($wpdb->prepare("SELECT count(ID) FROM $wpdb->posts WHERE post_author = $userdata->ID AND post_type = 'ad_listing' AND post_status = 'pending'"));
                $post_count_offline = $wpdb->get_var($wpdb->prepare("SELECT count(ID) FROM $wpdb->posts WHERE post_author = $userdata->ID AND post_type = 'ad_listing' AND post_status = 'draft'"));
                $post_count_total = $post_count_live + $post_count_pending + $post_count_offline;
                ?>
                    
                <li><?php _e('Live Listings:','appthemes')?> <strong><?php echo $post_count_live; ?></strong></li>
                <li><?php _e('Pending Listings:','appthemes')?> <strong><?php echo $post_count_pending; ?></strong></li>
                <li><?php _e('Offline Listings:','appthemes')?> <strong><?php echo $post_count_offline; ?></strong></li>
                <li><?php _e('Total Listings:','appthemes')?> <strong><?php echo $post_count_total; ?></strong></li>

                </ul>

        </div><!-- /shadowblock -->

    </div><!-- /shadowblock_out -->




<?php if (function_exists('dynamic_sidebar') && dynamic_sidebar('sidebar_user')) : else : ?>

<!-- no dynamic sidebar so don't do anything -->

<?php endif; ?>


</div><!-- /content_right -->

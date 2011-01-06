<?php

/**
 * These are functions to run for upgrades
 * @since 3.0.5
 * 
 * 
 */


// function that converts < v3.0.4 post types to the new post type "ad_listing"
// called from the admin-notices.php file
function cp_convert_posts2Ads () {
	global $wpdb, $app_version;

	echo '<div id="message2" class="updated" style="padding:10px 20px;">';

	// setup post conversion and stop if there are no valid ad listings in the posts table to convert
	$blogCatIDs = array();
	$blogCatIDs = cp_get_blog_cat_ids_array();

	// get all posts not in blog cats for quick check
	$args = array('category__not_in' => $blogCatIDs, 'post_status' => 'any', 'numberposts' => 10);
	$theposts = get_posts($args);

	if(count($theposts) < 1)
		wp_die('<h3>Error</h3><p><strong>Conversion process failed. No ad listings found. You only have blog posts or your blog parent category ID is incorrect.</strong></p>');

	// convert all the NON-BLOG categories to be part of the new ad_cat taxonomy
	echo '<p><strong>Converting ad categories.........</strong></p>';


	// get all category ids
	$cat_ids = get_all_category_ids();

	$cat_count_total = count($cat_ids);

	echo '<ul>';

	$cat_count = 0;

	foreach($cat_ids as $cat_id) {

		// only move categories not belonging to the blog cats or blog sub cats
		if(!in_array($cat_id, $blogCatIDs)){
			$wpdb->update( $wpdb->term_taxonomy, array( 'taxonomy' => 'ad_cat' ), array( 'term_id' => $cat_id ) );
			$thisCat = get_category($cat_id);
			echo '<li style="color:#009900"><strong>' . $thisCat->name . '</strong> (ID:' . $cat_id . ')' . ' category has been moved</li>';
			$cat_count ++;
		} else {
			$thisCat = get_category($cat_id);
			echo '<li><strong>' . $thisCat->name . '</strong> (ID:' . $cat_id . ')' . ' category has been skipped</li>';
		}

	}

	echo '</ul>';


	//convert all the NON-BLOG posts to be part of the new "ad_listing" taxonomy
	echo '<br /><p><strong>Converting posts........</strong></p>';

	$newTagsSummary = array();
	$post_count = 0;
	$ad_count = 0;
	$tag_count = 0;

	echo '<ul>';


	// get all the posts
	$args = array('post_status' => 'any', 'numberposts' => -1);
	$theposts = get_posts($args);

	foreach($theposts as $post) {
		
		setup_postdata($post);    	

		// get the post terms
		$oldTags = wp_get_post_terms($post->ID);
		$newTags = array();			
		
		// get the cat object array for the post
		$post_cats = get_the_category($post->ID);

		// grab the first cat id found
		$cat_id = $post_cats[0]->cat_ID;

		//check if the post is in a blog category
		if(!in_array($cat_id, $blogCatIDs)){

			// if yes, then first see if it has any tags
			if(!empty($oldTags)) {
				foreach($oldTags as $thetag) :
					$newTags[] = $thetag->name;
					$newTagsSummary[] = '<li style="color:#009900"><strong>"' . $thetag->name . '"</strong> tag has been copied</li>';
					$tag_count++;
				endforeach;
			}

			// copy the tag array over if it's not empty
			if(!empty($newTags))
				wp_set_post_terms($post->ID, $newTags, 'ad_tag');

			//now change the post to an ad
			set_post_type($post->ID, 'ad_listing');
			echo '<li style="color:#009900"><strong>"' . $post->post_title . '"</strong> (ID:' . $post->ID . ') post was converted</li>';
			$ad_count++;

		// not an ad so must be a blog post
		} else {

			// see if it has tags since we still want to echo them not moved
			if(!empty($oldTags)) {
			foreach($oldTags as $thetag) {
				$newTags[] = $thetag->name;
				$newTagsSummary[] = '<li><strong>"' . $thetag->name . '"</strong> tag has been skipped</li>';
				//$tag_count++;
				}
			}

			echo '<li><strong>"<a href="post.php?post='.$post->ID.'&action=edit" target="_blank">' . $post->post_title . '</a>"</strong> (ID:' . $post->ID . ') post has been skipped (in blog or blog-sub category)</li>';
		}

		$post_count++;
		
	}

	
	echo '<br/><p><strong>Copying tags...........</strong></p>';

	// get the total count of tags
	$all_tags = get_tags();
	$tags_count_total = count($all_tags);


	// calculate the results
	$blog_cats_total = $cat_count_total - $cat_count;
	$blog_posts_total = $post_count - $ad_count;
	$blog_tags_total = $tags_count_total - $tag_count;

	// print out all the tags
	foreach($newTagsSummary as $key => $value)
		echo $value;

	echo '</ul><br/>';

	echo '<h3>Migration Summary</h3>';
	echo '<p>Total categories converted: <strong>' . $cat_count . '/'.$cat_count_total.'</strong>  <small>(excluded '.$blog_cats_total.' blog categories)</small><br/>';
	echo 'Total posts converted: <strong>' . $ad_count . '/'.$post_count.'</strong>  <small>(excluded '.$blog_posts_total.' blog posts)</small><br/>';
	echo 'Total tags copied: <strong>' . $tag_count . '/'.$tags_count_total.'</strong>  <small>(excluded '.$blog_tags_total.' tags not assigned to ads)</small><br/>';

	echo '<br/><p><strong>The ads conversion utility has completed!</strong><br/><br/>Note: If for some reason an ad did not get converted, you can manually do it via the "Post Type" option on the edit post page.</p>';


	//reset the old version to current so this script doesn't appear again
	update_option('cp_version_old', $app_version);
?>

	<form action="admin.php?page=settings" id="msgForm" method="post">
		<p class="submit btop">
			<input type="submit" value="Run Migration Script Again?" name="convert" />
		</p>
		<input type="hidden" value="convertToCustomPostType" name="submitted" />
	</form>

	<p><strong>IMPORTANT: </strong>If you navigate away from this page, you will no longer be able to access this script. If you wish to run it again, open another browser tab and make your changes there first. Then come back and push the above button and the script will re-run.</p>

	<?php

	echo '</div>';
}

?>
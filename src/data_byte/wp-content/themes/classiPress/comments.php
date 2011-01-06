<?php
/**
 * @package WordPress
 * @subpackage ClassiPress
 * 
 */
      

// Do not delete these lines
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
        die (__('Please do not load this page directly.', 'appthemes'));

if ( post_password_required() ) { ?>

        <p class="nocomments"><?php _e('This post is password protected. Enter the password to view comments.','appthemes'); ?></p>
<?php
        return;
}

?>


<!-- You can start editing here. -->

<?php if (have_comments()) : ?>

<?php $commentDivsExist = true; ?>
<div class="shadowblock_out">

    <div class="shadowblock">

        <div id="comments">

            <div id="comments_wrap">
            
                <h2 class="dotted"><?php comments_number(__('No Responses','appthemes'), __('One Response','appthemes'), __('% Responses','appthemes') );?> <?php _e('to','appthemes'); ?> <span class="colour">&#8220;<?php the_title(); ?>&#8221;</span></h2>

                    <ol class="commentlist">

                        <?php wp_list_comments('callback=custom_comment&type=comment'); ?>

                    </ol>

                <div class="navigation">

                        <div class="alignleft"><?php previous_comments_link('&laquo; ' . __('Older Comments', 'appthemes'), 0) ?></div>

                        <div class="alignright"><?php next_comments_link(__('Newer Comments', 'appthemes') . ' &raquo;', 0) ?></div>

                        <div class="clr"></div>

                    </div>

                    <div class="clr"></div>

                    <?php if (!empty($comments_by_type['pings'])) : ?>

                        <h2 class="dotted" id="pings"><?php _e('Trackbacks/Pingbacks', 'appthemes'); ?></h2>

                        <ol class="commentlist">

                            <?php wp_list_comments('type=pings'); ?>

                        </ol>

                    <?php endif; ?>

            <?php else : // this is displayed if there are no comments so far ?>

                    <?php if (comments_open()) : ?>
                            <!-- If comments are open, but there are no comments. -->

                     <?php else : // comments are closed ?>
                            <!-- If comments are closed. -->

                    <?php endif; ?>


            <?php endif; ?>

            

            <?php if ('open' == $post->comment_status) : ?>

                <div id="respond">

                    <h2 class="dotted"><?php comment_form_title( __('Leave a Reply','appthemes'), __('Leave a Reply to %s','appthemes') ); ?></h2>

                    <div class="cancel-comment-reply">

                            <small><?php cancel_comment_reply_link(); ?></small>

                    </div>


                    <?php if (get_option('comment_registration') && !$user_ID) : ?>

                        <p><?php printf(__("You must be <a href='%s'>logged in</a> to post a comment.", 'appthemes'), get_option('siteurl').'/wp-login.php?redirect_to='.urlencode(get_permalink())); ?></p>

                    <?php else : ?>

                        <form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform" class="commentform">

                            <fieldset class="form-comments">

                            <?php if ($user_ID) : ?>

                            <p><?php _e('Logged in as','appthemes'); ?> <a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a>. <a href="<?php echo wp_logout_url(); ?>" title="<?php _e('Logout of this account','appthemes'); ?>"><?php _e('Logout','appthemes'); ?> &raquo;</a></p>

                            <?php else : ?>

                            <p class="comments">
                                <input type="text" name="author" id="author" class="text" value="<?php echo $comment_author; ?>" size="22" tabindex="1" />
                                <label for="author"><?php _e('Name','appthemes'); ?> <?php if ($req) _e('(required)','appthemes'); ?></label>
                            </p>

                            <div class="clr"></div>

                            <p class="comments">
                                <input type="text" name="email" id="email" class="text" value="<?php echo $comment_author_email; ?>" size="22" tabindex="2" />
                                <label for="email"><?php _e('Email (will not be visible)','appthemes'); ?> <?php if ($req) _e('(required)','appthemes'); ?></label>
                            </p>

                            <div class="clr"></div>

                           <p class="comments">
                                <input type="text" name="url" id="url" class="text" value="<?php echo $comment_author_url; ?>" size="22" tabindex="3" />
                                <label for="url"><?php _e('Website','appthemes'); ?></label>
                           </p>

                           <div class="clr"></div>

                            <?php endif; ?>

                            <!--<li><small><strong>XHTML:</strong> You can use these tags: <?php echo allowed_tags(); ?></small><div class="clr"></div></li>-->

                            <p class="comments-box">
                                <textarea name="comment" rows="" cols="" id="comment" tabindex="4"></textarea>
                            </p>

                            <div class="clr"></div>

                            <p class="comments">
                                <input name="submit" type="submit" id="submit" tabindex="5" class="btn_orange" value="<?php _e('Leave a Reply','appthemes'); ?>" />
                                <input type="hidden" name="comment_post_ID" value="<?php echo $id; ?>" />
                            </p>

                            <?php comment_id_fields(); ?>
                            <?php do_action('comment_form', $post->ID); ?>

                             </fieldset>
                            
                        </form>

                    <?php endif; // If logged in ?>

                    <div class="clr"></div>

                </div> <!-- /respond -->               

            <?php endif; ?>
<?php if($commentDivsExist) : ?>
            </div> <!-- /comments_wrap -->

        </div><!-- /comments -->

    </div><!-- /shadowblock -->

</div><!-- /shadowblock_out -->
<?php endif; ?>
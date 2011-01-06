<?php

// Custom callback to list comments
function custom_comment($comment, $args, $depth) {
   $GLOBALS['comment'] = $comment;
   $GLOBALS['comment_depth'] = $depth;
?>


<li <?php comment_class(); ?>>

    <a name="comment-<?php comment_ID() ?>"></a>

        <?php if(get_comment_type() == 'comment'){ ?>

            <div class="avatar"><?php commenter_avatar(); ?></div>

        <?php } ?>

    <div class="comment-head">

        <div class="user-meta">

            <strong class="name"><?php commenter_link() ?></strong> <?php _e('on', 'appthemes') ?>

            <?php if(get_comment_type() == 'comment') { ?>

                <a class="comment-permalink" href="<?php echo get_comment_link(); ?>"><?php echo get_comment_date(get_option('date_format')) ?> @ <?php echo get_comment_time(get_option('time_format')); ?></a> <?php edit_comment_link(__('Edit', 'appthemes'), ' <span class="edit-link">(', ')</span>'); ?>

            <?php }?>
        
        </div> <!-- /user-meta -->

    </div> <!-- /comment-head -->


    <div class="comment-entry"  id="comment-<?php comment_ID(); ?>">

        <?php comment_text() ?>

        <?php if ($comment->comment_approved == '0') { ?>
        
            <p class='unapproved'><?php _e('Your comment is awaiting moderation.','appthemes') ?></p>

        <?php } ?>

        <div class="clr"></div>

        <div class="reply">

            <?php comment_reply_link(array_merge($args, array( 'reply_text' => __('Reply','appthemes'),
                                                                'login_text' => __('Log in to reply.','appthemes'),
                                                                'depth' => $depth,
                                                                'max_depth' => $args['max_depth'],
                                                                'before' => '<div class="comment-reply-link">',
                                                                'after' => '</div>',
                                                                ))) ?>

        </div><!-- /reply -->

    </div><!-- /comment-entry -->

<?php 
}


function commenter_link() {
    $commenter = get_comment_author_link();

    if (strstr(']* class=[^>]+>', $commenter)) {
        $commenter = str_replace('(]* class=[\'"]?)', '\\1url ' , $commenter);

    } else {

        $commenter = str_replace('(<a )/', '\\1class="url "' , $commenter);
    }

    echo $commenter;
}

function commenter_avatar() {
    $avatar_email = get_comment_author_email();
    $avatar = str_replace('class="avatar', 'class="photo avatar', get_avatar($avatar_email, 60));

    echo $avatar;
}

?>
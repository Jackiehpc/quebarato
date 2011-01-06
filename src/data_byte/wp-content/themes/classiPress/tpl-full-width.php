<?php
/*
Template Name: Full Width Page
*/
?>

<?php get_header(); ?>

<!-- CONTENT -->
<div class="content">

    <div class="content_botbg">

        <div class="content_res">

            <div id="breadcrumb">

                <?php if(function_exists('cp_breadcrumb')) cp_breadcrumb(); ?>

            </div>

            <!-- full block -->
            <div class="shadowblock_out">

                <div class="shadowblock">

                    <div class="post">

                        <?php if(have_posts()) : ?>

                            <?php while(have_posts()) : the_post() ?>

                        <h1 class="single dotted"><?php the_title();?></h1>

                                <?php the_content(); ?>

                            <?php endwhile; ?>

                        <?php else : ?>

                            <?php _e('No content found.','appthemes'); ?>

                        <?php endif; ?>

                        <div class="clr"></div>


                    </div><!--/post-->

                </div><!-- /shadowblock -->

            </div><!-- /shadowblock_out -->

            <div class="clr"></div>

               <?php if (comments_open()) comments_template(); ?>

        </div><!-- /content_res -->

    </div><!-- /content_botbg -->

</div><!-- /content -->
	
<?php get_footer(); ?>	


<?php get_header(); ?>

<?php // if($_GET['reportpost'] == $post->ID) { app_report_post($post->ID); $reported = true;} ?>

<!-- CONTENT -->
  <div class="content">

      <div class="content_botbg">

          <div class="content_res">

              <div id="breadcrumb">

                  <?php if(function_exists('cp_breadcrumb')) cp_breadcrumb(); ?>
                  
              </div>
              
              <!-- <div style="width: 105px; height:16px; text-align: right; float: left; font-size:11px; margin-top:-10px; padding:0 10px 5px 5px;"> -->
              <?php // if($reported) : ?>
				<!-- <span id="reportedPost"><?php _e('Post Was Reported', 'appthemes'); ?></span> -->
              <?php // else : ?>
			<!--	<a id="reportPost" href="?reportpost=<?php echo $post->ID; ?>"><?php _e('Report This Post','appthemes') ?></a> -->
              <?php // endif; ?>
			  <!-- </div> -->
              
              <div class="clr"></div>

                <div class="content_left">
	

		<?php if(have_posts()) : ?>

			<?php while(have_posts()) : the_post() ?>

				<?php appthemes_stats_update($post->ID); //records the page hit ?>

				<div class="shadowblock_out">

					<div class="shadowblock">

						<div class="price-wrap">
							<span class="tag-head">&nbsp;</span><p class="ad-price"><?php if(get_post_meta($post->ID, 'price', true)) cp_get_price_legacy($post->ID); else cp_get_price($post->ID); ?></p>
						</div>

							<h1 class="single-ad"><a href="<?php the_permalink() ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h1>

							<div class="pad5 dotted"></div>

							<div class="bigright">

								<ul>

									<?php
									// grab the category id for the functions below
									$cat_id = appthemes_get_custom_taxonomy($post->ID, 'ad_cat', 'term_id');

									// check to see if ad is legacy or not
									if(get_post_meta($post->ID, 'expires', true)) {  ?>

										<li><span><?php _e('Location:', 'appthemes') ?></span> <?php echo get_post_meta($post->ID, 'location', true); ?></li>
										<li><span><?php _e('Phone:', 'appthemes') ?></span> <?php echo get_post_meta($post->ID, 'phone', true); ?></li>

										<?php if(get_post_meta($post->ID, 'cp_adURL', true)) ?>
											<li><span><?php _e('URL:','appthemes'); ?></span> <?php echo appthemes_make_clickable(get_post_meta($post->ID, 'cp_adURL', true)); ?></li>

										<li><span><?php _e('Listed:', 'appthemes') ?></span> <?php the_time(get_option('date_format') . ' ' . get_option('time_format')) ?></li>
										<li><span><?php _e('Expires:', 'appthemes') ?></span> <?php echo cp_timeleft(strtotime(get_post_meta($post->ID, 'expires', true))); ?></li>

									<?php

									} else {

										if(get_post_meta($post->ID, 'cp_ad_sold', true) == 'yes') : ?>
										<li id="cp_sold"><span><?php _e('This item has been sold', 'appthemes'); ?></span></li>
										<?php endif; ?>
										<?php
										// 3.0+ display the custom fields instead (but not text areas)
										cp_get_ad_details($post->ID, $cat_id);
									?>
									
										
										<?php
											$id_pagseguro = get_post_meta($post->ID, 'cp_id_pagamentodigital', true);
											
											
											
											if($id_pagseguro && get_post_meta($post->ID, 'cp_token_pagamentodigital', true) &&  get_post_meta($post->ID, 'cp_usar_o_pagamento_digital_1', true)): ?>
											
												<form name="pagamentodigital" action="https://www.pagamentodigital.com.br/checkout/pay/" method="post">
													<input name="url_retorno" type="hidden" value="<?php echo home_url(); ?>/wp-content/themes/dataByte/pagamentodigital.php" />
													<input name="email_loja" type="hidden" value="<?php echo $id_pagseguro; ?>">
													<input name="produto_codigo_1" type="hidden" value="<?php echo $post->ID; ?>">
													<input name="produto_descricao_1" type="hidden" value="<?php the_title(); ?>">
													<input name="produto_qtde_1" type="hidden" value="1">
													<input name="produto_valor_1" type="hidden" value="<?php echo get_number_price($post->ID); ?>" >
													<input name="tipo_integracao" type="hidden" value="PAD">
													<input name="free" type="hidden" value="<?php echo $post->ID; ?>">
													<input name="frete" type="hidden" value="0">
													
													<input type="image" src="https://www.pagamentodigital.com.br/webroot/img/bt_comprar.gif" value="Comprar" alt="Comprar" border="0" align="absbottom" >
												</form>
												
											<?php endif; ?>
										<li id="cp_listed"><span><?php _e('Listed:', 'appthemes') ?></span> <?php the_time(get_option('date_format') . ' ' . get_option('time_format')) ?></li>

										<?php if (get_post_meta($post->ID, 'cp_sys_expire_date', true)) ?>
											<li id="cp_expires"><span><?php _e('Expires:', 'appthemes') ?></span> <?php echo cp_timeleft(strtotime(get_post_meta($post->ID, 'cp_sys_expire_date', true))); ?></li>

									<?php
									} // end legacy check
									?>

								</ul>

							</div><!-- /bigright -->


					<?php if(get_option('cp_ad_images') == 'yes'): ?>

						<div class="bigleft">


							<div id="main-pic">

								<?php cp_get_image_url(); ?>

								<div class="clr"></div>
							</div>

							<div id="thumbs-pic">

								<?php if(get_post_meta($post->ID, 'images', true)) echo cp_get_image_thumbs_legacy($post->ID, get_option('thumbnail_size_w'), get_option('thumbnail_size_h'), $post->post_title); else cp_get_image_url_single($post->ID, 'thumbnail', $post->post_title, -1); ?>

								<div class="clr"></div>
							</div>

						</div><!-- /bigleft -->

					<?php endif; ?>

				 <div class="clr"></div>
					
					<div class="single-main">
						
						<?php
						// 3.0+ display text areas in content area before content.
						cp_get_ad_details($post->ID, $cat_id, 'content');
						?>

						<h3 class="description-area"><?php _e('Description','appthemes'); ?></h3>
						
						<?php the_content(); ?>

						<div class='note'><strong><?php _e('Ad Reference ID:','appthemes'); ?></strong> <?php if(get_post_meta($post->ID, 'cp_sys_ad_conf_id', true)) echo get_post_meta($post->ID, 'cp_sys_ad_conf_id', true); else echo __('N/A', 'appthemes'); ?></div>

					</div>

						 <div class="dotted"></div>
						 <div class="pad5"></div>

					<div class="prdetails">
	
						<p class="tags"><?php if(get_the_term_list($post->ID, 'ad_tag')) echo get_the_term_list($post->ID, 'ad_tag', '', '&nbsp;', '' ); else echo __('No Tags', 'appthemes'); ?></p>
						<?php if (get_option('cp_ad_stats_all') == 'yes') { ?><p class="stats"><?php appthemes_stats_counter($post->ID); ?></p> <?php } ?>
						<p class="print"><?php if(function_exists('wp_email')) { email_link(); } ?>&nbsp;&nbsp;<?php if(function_exists('wp_print')) { print_link(); } ?></p>
						<?php edit_post_link('<p class="edit">'.__('Edit Listing','appthemes'), '', '').'</p>'; ?>
				
					</div>


					<?php if(function_exists('selfserv_sexy')) { selfserv_sexy(); } ?>
					

                            </div><!-- /shadowblock -->

                        </div><!-- /shadowblock_out -->

			<?php endwhile; else: ?>

                            <p><?php _e('Sorry, no listings matched your criteria.','appthemes'); ?></p>

                        <?php endif; ?>


                        <div class="clr"></div>


			<?php
                        // show the ad block if it's been activated
                        if (get_option('cp_adcode_336x280_enable') == 'yes') {

                            if(function_exists('appthemes_single_ad_336x280')) { ?>

                            <div class="shadowblock_out">

                                <div class="shadowblock">

                                  <h2 class="dotted"><?php _e('Sponsored Links','appthemes') ?></h2>

                                  <?php appthemes_single_ad_336x280(); ?>

                                </div><!-- /shadowblock -->

                            </div><!-- /shadowblock_out -->

                        <?php
                            }
                        }
                        ?>

                        <?php wp_reset_query(); ?>


                        <div class="clr"></div>


                           <?php comments_template(); ?>


                </div><!-- /content_left -->


                <?php get_sidebar('ad'); ?>


            <div class="clr"></div>


      </div><!-- /content_res -->

    </div><!-- /content_botbg -->

  </div><!-- /content -->



<?php get_footer(); ?>
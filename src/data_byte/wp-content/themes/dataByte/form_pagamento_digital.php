<?php
include_once '../../../wp-load.php' ;


$post = get_post($_REQUEST['ad_id']);

// var_dump($post);

$id_pagseguro = get_post_meta($post->ID, 'cp_id_pagamentodigital', true);

?>



<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<title><?php
	/*
	 * Print the <title> tag based on what is being viewed.
	 */
	global $page, $paged;

	wp_title( '|', true, 'right' );

	// Add the blog name.
	bloginfo( 'name' );

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		echo " | $site_description";

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		echo ' | ' . sprintf( __( 'Page %s', 'twentyten' ), max( $paged, $page ) );

	?></title>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<?php
	/* We add some JavaScript to pages with the comment form
	 * to support sites with threaded comments (when in use).
	 */
	if ( is_singular() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );

	/* Always have wp_head() just before the closing </head>
	 * tag of your theme, or you will break many plugins, which
	 * generally use this hook to add elements to <head> such
	 * as styles, scripts, and meta tags.
	 */
	wp_head();
?>
</head>


<?php // appthemes_highlight_search_term(esc_attr(get_search_query())); ?>

<!-- CONTENT -->
  <div class="content">
	AGUARDE ...<BR/>
    <img  src='<?php echo home_url(); ?>/wp-content/themes/dataByte/images/ajax-loader.gif' />
    
    
    <form name="pagamentodigital" id='the_form' action="https://www.pagamentodigital.com.br/checkout/pay/" method="post">
		<input name="url_retorno" type="hidden" value="<?php echo home_url(); ?>/wp-content/themes/dataByte/pagamentodigital.php" />
		<input name="email_loja" type="hidden" value="<?php echo $id_pagseguro; ?>">
		<input name="produto_codigo_1" type="hidden" value="<?php echo $post->ID; ?>">
		<input name="produto_descricao_1" type="hidden" value="<?php echo $post->post_title; ?>">
		<input name="produto_qtde_1" type="hidden" value="1">
		<input name="produto_valor_1" type="hidden" value="<?php echo get_number_price($post->ID); ?>" >
		<input name="tipo_integracao" type="hidden" value="PAD">
		<input name="free" type="hidden" value="<?php echo $post->ID; ?>">
		<input name="frete" type="hidden" value="0">
		
		<!-- <input type="image" src="https://www.pagamentodigital.com.br/webroot/img/bt_comprar.gif" value="Comprar" alt="Comprar" border="0" align="absbottom" > -->
	</form>
    
    
  </div><!-- /content -->

	<script type='text/javascript' >
	
		jQuery(document).ready(function(){
			//jQuery("#the_form").submit();
		});
	
	</script>


</div><!-- /container -->


</body>
</html>

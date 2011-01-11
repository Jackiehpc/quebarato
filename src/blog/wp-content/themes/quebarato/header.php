<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package WordPress
 * @subpackage Twenty_Ten
 * @since Twenty Ten 1.0
 */
?><!DOCTYPE html>
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

<body <?php body_class(); ?>>
<div id="wrapper" class="hfeed">
	<div id="header">
	
	<div id="header_container">
	<!-- INICIO HEADER -->
    <div id="hd" class="mod simple">
      <b class="top"><b class="tl"></b><b class="tr"></b></b>
      <div class="hd_top">

        <div class="line">
          <div class="unit">
            <div class="left">
              <a href="http://www.quebarato.com.br?og=657734&utm_source=Buscape&utm_medium=popunder&utm_content=popunder+Buscape+todos+homeqb&utm_campaign=lancamentoQB3" title="QueBarato! Anúnciar aqui é gratis" class="logo_qb">
                QueBarato! Anúnciar aqui é gratis
              </a>
            </div>
          </div>
          <div class="unit">

            <div class="middle">
              <ul class="line lista_links up">
                <li class="unit"></li>
                <li class="unit"></li>
                <li class="unit"></li>
                <li class="unit"></li>
                <li class="unit"></li>
                <li class="unit simple"></li>
                <li class="unit lastUnit right simple"></li>

              </ul>

              <form action="http://www.quebarato.com.br/search?og=657734&utm_source=Buscape&utm_medium=popunder&utm_content=popunder+Buscape+todos+homeqb&utm_campaign=lancamentoQB3" class="busca">
                <div class="line">
                  <div class="unit">
                    <input name="q" type="text" class="text_busca" value="">
                    <input name="og" type="hidden" value="657734">
                    <input name="utm_source" type="hidden" value="Buscape">
                    <input name="utm_medium" type="hidden" value="popunder">

                    <input name="utm_content" type="hidden" value="popunder Buscape todos homeqb">
                    <input name="utm_campaign" type="hidden" value="lancamentoQB3">
                  </div>
                  <script type="text/javascript">
                    initSearch($('.text_busca:last'));
                  </script>
                  <div class="unit">
                    <a class="busca_lista_categorias" title="em todas as categorias">em <span>todas as categorias</span><b></b></a>
                    <ul class="category_menu">

                             <li rel="http://www.quebarato.com.br/empregos.html?og=657734&utm_source=Buscape&utm_medium=popunder&utm_content=popunder+Buscape+todos+homeqb&utm_campaign=lancamentoQB3">Empregos</li>
                             <li rel="http://www.quebarato.com.br/imoveis.html?og=657734&utm_source=Buscape&utm_medium=popunder&utm_content=popunder+Buscape+todos+homeqb&utm_campaign=lancamentoQB3">Imóveis</li>
                             <li rel="http://www.quebarato.com.br/eventos.html?og=657734&utm_source=Buscape&utm_medium=popunder&utm_content=popunder+Buscape+todos+homeqb&utm_campaign=lancamentoQB3">Eventos</li>
                             <li rel="http://www.quebarato.com.br/produtos.html?og=657734&utm_source=Buscape&utm_medium=popunder&utm_content=popunder+Buscape+todos+homeqb&utm_campaign=lancamentoQB3">Produtos</li>
                             <li rel="http://www.quebarato.com.br/servicos.html?og=657734&utm_source=Buscape&utm_medium=popunder&utm_content=popunder+Buscape+todos+homeqb&utm_campaign=lancamentoQB3">Serviços</li>
                             <li rel="http://www.quebarato.com.br/veiculos.html?og=657734&utm_source=Buscape&utm_medium=popunder&utm_content=popunder+Buscape+todos+homeqb&utm_campaign=lancamentoQB3">Veículos</li>

                             <li rel="http://www.quebarato.com.br/relacionamentos.html?og=657734&utm_source=Buscape&utm_medium=popunder&utm_content=popunder+Buscape+todos+homeqb&utm_campaign=lancamentoQB3">Relacionamentos</li>
                          <li rel="http://www.quebarato.com.br/search" class="all"><strong>Todas as Categorias</strong></li>
                      <!-- <li class="prefs">definir preferências</li> -->
                    </ul>
                  </div>
                  <script type="text/javascript">
                    initCategoryMenu();
                  </script>
                  <div class="unit lastUnit"><input type="submit" class="button_busca" value=""></div>

                </div>
              </form>
              
              <ul class="line lista_links down lista_links_busca">
                <li class="unit"></li>
                <li class="unit"></li>
                <li class="unit"></li>
                <li class="unit"></li>
                <li class="unit"></li>
                <li class="unit"></li>

                <li class="unit simple"></li>
              </ul>
            </div>
          </div>
          <div class="unit lastUnit">
            <div class="right">
              <a class="publicaranuncio" title="publicar anúncio grátis" href="http://www.quebarato.com.br/publicar-anuncio-gratis.html?og=657734&utm_source=Buscape&utm_medium=popunder&utm_content=popunder+Buscape+todos+homeqb&utm_campaign=lancamentoQB3">Publicar Anúncio</a>
            </div>

          </div>
        </div>
      </div>
      <!-- INICIO HD UNDER  -->
      <div id="hd_under">
        <div class="mod simple darkGreen">
          <div class="line">
            <div class="inner_mod">
              
              <div id="navegation" role="navigation">
                <?php /*  Allow screen readers / text browsers to skip the navigation menu and get right to the good stuff */ ?>
                <div class="skip-link screen-reader-text"><a href="#content" title="<?php esc_attr_e( 'Skip to content', 'twentyten' ); ?>"><?php _e( 'Skip to content', 'twentyten' ); ?></a></div>
                <?php /* Our navigation menu.  If one isn't filled out, wp_nav_menu falls back to wp_page_menu.  The menu assiged to the primary position is the one used.  If none is assigned, the menu with the lowest ID is used.  */ ?>
                <?php wp_nav_menu( array( 'container_class' => 'menu-header', 'theme_location' => 'primary' ) ); ?>
            </div><!-- #access -->
              

            </div>
          </div>
        </div>
      </div>
      <b class="bottom"><b class="bl"></b><b class="br"></b></b>
      <!-- FIM HD UNDER -->
      </div>
    </div>  
    <!-- FIM HEADER --> 

	
		<div id="masthead">
			

			
		</div><!-- #masthead -->
	</div><!-- #header -->

	<div id="main">

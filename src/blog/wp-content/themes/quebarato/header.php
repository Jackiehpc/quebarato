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
	<div class="top_disclaimer">     
      <div class="not_logged">
        <div class="line">
          <div class="unit size3of5">
            <p>QueBarato! o classificado mais legal e seguro da internet. Aqui você compra, vende e negocia em tempo real.</p>
          </div>
          <div class="unit size2of5 lastUnit">
            <p class="right">
              Você não está conectado. <a onclick="return addReturnParam(this)" title="Entrar" class="faded" href="http://www.quebarato.com.br/login.html?og=657736&amp;utm_source=Harrenmedia&amp;utm_medium=popunder&amp;utm_content=popunder+Harrenmedia+todos+homeqb&amp;utm_campaign=lancamentoQB3"><strong>Entrar</strong></a> (ou <a class="faded" title="Cadastre-se" href="http://www.quebarato.com.br/cadastre-se.html?og=657736&amp;utm_source=Harrenmedia&amp;utm_medium=popunder&amp;utm_content=popunder+Harrenmedia+todos+homeqb&amp;utm_campaign=lancamentoQB3"><strong>Cadastre-se</strong></a>)
            </p>
          </div>
        </div>
      </div>    
    </div>
    
    <div class="mod simple" id="hd">
      <b class="top"><b class="tl"></b><b class="tr"></b></b>
      <div class="hd_top">
        <div class="line">
          <div class="unit">
            <div class="left">
              <a class="logo_qb" title="QueBarato! Anúnciar aqui é gratis" href="http://www.quebarato.com.br?og=657736&amp;utm_source=Harrenmedia&amp;utm_medium=popunder&amp;utm_content=popunder+Harrenmedia+todos+homeqb&amp;utm_campaign=lancamentoQB3">
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

              <form class="busca" action="http://www.quebarato.com.br/search?og=657736&amp;utm_source=Harrenmedia&amp;utm_medium=popunder&amp;utm_content=popunder+Harrenmedia+todos+homeqb&amp;utm_campaign=lancamentoQB3">
                <div class="line">
                  <div class="unit">
                    <input type="text" value="" class="text_busca text_busca_off" name="q">
                    <input type="hidden" value="657734" name="og">
                    <input type="hidden" value="Buscape" name="utm_source">
                    <input type="hidden" value="popunder" name="utm_medium">
                    <input type="hidden" value="popunder Buscape todos homeqb" name="utm_content">
                    <input type="hidden" value="lancamentoQB3" name="utm_campaign">
                  </div>
                  <script type="text/javascript">
                    initSearch($('.text_busca:last'));
                  </script>
                  <div class="unit">
                    <a title="em todas as categorias" class="busca_lista_categorias">em <span>todas as categorias</span><b></b></a>
                    <ul class="category_menu">
                             <li rel="http://www.quebarato.com.br/empregos.html?og=657736&amp;utm_source=Harrenmedia&amp;utm_medium=popunder&amp;utm_content=popunder+Harrenmedia+todos+homeqb&amp;utm_campaign=lancamentoQB3">Empregos</li>
                             <li rel="http://www.quebarato.com.br/imoveis.html?og=657736&amp;utm_source=Harrenmedia&amp;utm_medium=popunder&amp;utm_content=popunder+Harrenmedia+todos+homeqb&amp;utm_campaign=lancamentoQB3">Imóveis</li>
                             <li rel="http://www.quebarato.com.br/eventos.html?og=657736&amp;utm_source=Harrenmedia&amp;utm_medium=popunder&amp;utm_content=popunder+Harrenmedia+todos+homeqb&amp;utm_campaign=lancamentoQB3">Eventos</li>
                             <li rel="http://www.quebarato.com.br/produtos.html?og=657736&amp;utm_source=Harrenmedia&amp;utm_medium=popunder&amp;utm_content=popunder+Harrenmedia+todos+homeqb&amp;utm_campaign=lancamentoQB3">Produtos</li>
                             <li rel="http://www.quebarato.com.br/servicos.html?og=657736&amp;utm_source=Harrenmedia&amp;utm_medium=popunder&amp;utm_content=popunder+Harrenmedia+todos+homeqb&amp;utm_campaign=lancamentoQB3">Serviços</li>
                             <li rel="http://www.quebarato.com.br/veiculos.html?og=657736&amp;utm_source=Harrenmedia&amp;utm_medium=popunder&amp;utm_content=popunder+Harrenmedia+todos+homeqb&amp;utm_campaign=lancamentoQB3">Veículos</li>
                             <li rel="http://www.quebarato.com.br/relacionamentos.html?og=657736&amp;utm_source=Harrenmedia&amp;utm_medium=popunder&amp;utm_content=popunder+Harrenmedia+todos+homeqb&amp;utm_campaign=lancamentoQB3">Relacionamentos</li>
                          <li class="all" rel="http://www.quebarato.com.br/search"><strong>Todas as Categorias</strong></li>
                      <!-- <li class="prefs">definir preferências</li> -->
                    </ul>
                  </div>
                  <script type="text/javascript">
                    initCategoryMenu();
                  </script>
                  <div class="unit lastUnit"><input type="submit" value="" class="button_busca"></div>
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
              <a href="http://www.quebarato.com.br/publicar-anuncio-gratis.html?og=657736&amp;utm_source=Harrenmedia&amp;utm_medium=popunder&amp;utm_content=popunder+Harrenmedia+todos+homeqb&amp;utm_campaign=lancamentoQB3" title="publicar anúncio grátis" class="publicaranuncio">Publicar Anúncio</a>
            </div>
          </div>
        </div>
      </div>
      <!-- INICIO HD UNDER  -->
      <div id="hd_under">
        <div class="mod simple darkGreen">
          <div class="line">
            <div class="inner_mod">
              <div class="locale">
                <div class="line">
                  
                 <div class="breadcrumbs">
                  <?php
                  if(function_exists('bcn_display'))
                  {
                      bcn_display();
                  }
                  ?>
                </div>
                  
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <b class="bottom"><b class="bl"></b><b class="br"></b></b>
      <!-- FIM HD UNDER -->
      
    </div>
    <!-- FIM HEADER --> 

	
		<div id="masthead">
			<a href="http://blog.quebarato.com.br" id="lnkHome">&nbsp;</a>

		</div><!-- #masthead -->
	</div><!-- #header -->

	<div id="main">

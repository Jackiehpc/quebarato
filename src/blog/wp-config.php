<?php
/** 
 * As configurações básicas do WordPress.
 *
 * Esse arquivo contém as seguintes configurações: configurações de MySQL, Prefixo de Tabelas,
 * Chaves secretas, Idioma do WordPress, e ABSPATH. Você pode encontrar mais informações
 * visitando {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. Você pode obter as configuraçções de MySQL de seu servidor de hospedagem.
 *
 * Esse arquivo é usado pelo script ed criação wp-config.php durante a
 * instalação. Você não precisa usar o site, você pode apenas salvar esse arquivo
 * como "wp-config.php" e preencher os valores.
 *
 * @package WordPress
 */

// ** Configurações do MySQL - Você pode pegar essas informações com o serviço de hospedagem ** //
/** O nome do banco de dados do WordPress */
define('DB_NAME', 'blog');

/** Usuário do banco de dados MySQL */
define('DB_USER', 'root');

/** Senha do banco de dados MySQL */
define('DB_PASSWORD', 'admin');

/** nome do host do MySQL */
define('DB_HOST', 'localhost');

/** Conjunto de caracteres do banco de dados a ser usado na criação das tabelas. */
define('DB_CHARSET', 'utf8');

/** O tipo de collate do banco de dados. Não altere isso se tiver dúvidas. */
define('DB_COLLATE', '');

/**#@+
 * Chaves únicas de autenticação e salts.
 *
 * Altere cada chave para um frase única!
 * Você pode gerá-las usando o {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * Você pode alterá-las a qualquer momento para desvalidar quaisquer cookies existentes. Isto irá forçar todos os usuários a fazerem login novamente.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'h%U[G(5=_|PTzk1,It/XGyUMj(@c,d;|fUH` uiY:bl*FF43$nR~M+AH!3E32i(s');
define('SECURE_AUTH_KEY',  'y 8OSNz^x!v[,`ew-Mg#oHyHvDhs[/`Bq DJx3AiC waq}9|&,Qb)m~Aq;[k@jGD');
define('LOGGED_IN_KEY',    '(u:f)y|L@3},FKA&}Y/6xix+!L9tZa2wT/v9R++>&{eey4QI%Wp%}gr+k:z rGW&');
define('NONCE_KEY',        'qpHgd|wtZv?9/ma:GtyJvTaV^36[YYkeq;P` x+:@ip5B>-1g11t+sN`d0Z_0LmO');
define('AUTH_SALT',        '.`vM3r+&Yzm$kL+2g~jz|mGmi>{Cdbd4%40Y<[qi~|kb3?{tqx8x< PU5lMX:bUm');
define('SECURE_AUTH_SALT', 'v}R6JOzb8lhub#3=A*@d}j.4.h>{2+t7<-KL`gv1Wz3:-2A)6TBnIAPrd=12V3Et');
define('LOGGED_IN_SALT',   '8bf`[&-|^L{gV9COp:j|ZYp[%+r>+?[W_7sm.x!]`jcK{K}+;OvmUCZ+vbmya+V4');
define('NONCE_SALT',       '>A+0I;Ja+]TK$IZ<l>`UkdoHN}ag|&Q!%-fF)Nl+{+Q2<Xh9d|%QcP1,_-A/P|K7');

/**#@-*/

/**
 * Prefixo da tabela do banco de dados do WordPress.
 *
 * Você pode ter várias instalações em um único banco de dados se você der para cada um um único
 * prefixo. Somente números, letras e sublinhados!
 */
$table_prefix  = 'wp_';

/**
 * O idioma localizado do WordPress é o inglês por padrão.
 *
 * Altere esta definição para localizar o WordPress. Um arquivo MO correspondente a
 * língua escolhida deve ser instalado em wp-content/languages. Por exemplo, instale
 * pt_BR.mo em wp-content/languages e altere WPLANG para 'pt_BR' para habilitar o suporte
 * ao português do Brasil.
 */
define ('WPLANG', 'pt_BR');

/**
 * Para desenvolvedores: Modo debugging WordPress.
 *
 * altere isto para true para ativar a exibição de avisos durante o desenvolvimento.
 * é altamente recomendável que os desenvolvedores de plugins e temas usem o WP_DEBUG
 * em seuas ambientes de desenvolvimento.
 */
define('WP_DEBUG', false);

/* Isto é tudo, pode parar de editar! :) */

/** Caminho absoluto para o diretório WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');
	
/** Configura as variáveis do WordPress e arquivos inclusos. */
require_once(ABSPATH . 'wp-settings.php');


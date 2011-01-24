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
define('DB_NAME', 'data_byte');

/** Usuário do banco de dados MySQL */
define('DB_USER', 'root');

/** Senha do banco de dados MySQL */
define('DB_PASSWORD', '');

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
define('AUTH_KEY',         'uF1ORLX=k:v,QcNUS%][7o?2:)R}-o+>ID~S  {*eH]CHYxVv)ErLaQBQ3EH@#zM');
define('SECURE_AUTH_KEY',  'rqj)V//G% 12BVVBq1y`0E*d+~^+._tvJY3OW=4&hYJHlv lDHA:g? ttB0bRGfF');
define('LOGGED_IN_KEY',    '$1B?3^k:)&RY6zabbAlc%9wJuU0z=gv[U(hQ65;pAyZXmEC|?I>k.#E<VC`Y!f^r');
define('NONCE_KEY',        '`Wd$;hdTQ?X,tfWE%+Lj&`j.lU<U$t<Cel%IyY2Za%@jc{Hu`}]}.:D/xRf ]|gs');
define('AUTH_SALT',        '{3sD!AR[/rJ(d=yYA/FArh6|Y~-<F+rB(3VZ4 4p;R}Ax/#ri`Fi33a7TwMG(m!^');
define('SECURE_AUTH_SALT', 'l|P9M#~h.&O9swHscyF!ClA:mz(e/:4< (x-U=Tk.=^.#8Tn_cy`7[}&xr/M,)n$');
define('LOGGED_IN_SALT',   '0%.e>*_?=&WT_=B6#u.JL!)<xicu.9a6J0uno5Ylz!LCIV*Z%mm92)g{Whh5G+73');
define('NONCE_SALT',       ')%c68-4L,TV1s9a4wDBq/DpAwd[T/TE@1JUWY!P|s!o<ozaf:++&b,FF70.>D9FM');

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

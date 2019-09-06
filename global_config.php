<?php
/**
 * Configuração geral
 */
 
// Caminho para a raiz
if ( !defined('ABSPATH') )
  define('ABSPATH', dirname(__FILE__) . '/');
 
// Caminho para a pasta de uploads
define( 'UP_ABSPATH', ABSPATH . '/uploads' );
 
// URL da home
define( 'HOME_URI', 'http://localhost/erp' );
 
// Nome do host da base de dados
define( 'HOSTNAME', 'localhost' );
 
// Nome do DB
define( 'DB_NAME', 'laticinio_erp' );
 
// Usuário do DB
define( 'DB_USER', 'root' );
 
// Senha do DB
define( 'DB_PASSWORD', '' );
 
// Charset da conexão PDO
define( 'DB_CHARSET', 'utf8' );
 
// Se você estiver desenvolvendo, modifique o valor para true
define( 'DEBUG', true );

// Pasta para backup da base de dados
define( 'BACKUPFOLDER', $_SERVER['DOCUMENT_ROOT'].'/bd_backups');

// Número máximo de arquivos de backup mantidos
define( 'MAXNUMBERFILES', 30 );

// Usuário padrão
define( 'DEFAULT_USER', 'admin' );

// Senha do usuário padrão
define( 'DEFAULT_PASS', '142536' );
 
/**
 * Não edite daqui em diante
 */
?>
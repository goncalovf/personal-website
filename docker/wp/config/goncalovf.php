<?php

# WordPress
define( 'WP_HOME', 'https://' . ( getenv( 'VIRTUAL_HOST' ) ?: 'goncalovf.local' ) );
define( 'WP_SITEURL', WP_HOME );
define( 'SCRIPT_DEBUG', true );
define( 'WP_CACHE', false );
define( 'WP_DEBUG_DISPLAY', false );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEVELOPMENT_MODE', 'all' );
define( 'WP_ENVIRONMENT_TYPE', 'local' );
define( 'WP_AUTO_UPDATE_CORE', false );
define( 'FS_METHOD', 'direct' );

# nginx-proxy terminates TLS, so tell WordPress the request was HTTPS.
if ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && 'https' === $_SERVER['HTTP_X_FORWARDED_PROTO'] ) {
	$_SERVER['HTTPS'] = 'on';
}

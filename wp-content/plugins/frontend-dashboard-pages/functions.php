<?php
/**
 * Created by Buffercode.
 * User: M A Vinoth Kumar
 */

/**
 * Append the Version -- Pages
 */
add_filter( 'fed_plugin_versions', function ( $version ) {
	return array_merge( $version, array( 'pages' => 'Pages' ) );
} );



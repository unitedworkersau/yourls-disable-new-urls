<?php
/*
Plugin Name: Temporarily Disable New URLs
Description: Temporarily blocks creation of new short URLs while keeping existing redirects and link management available.
Version: 1.0.0
Author: United Workers Union
*/

// No direct calls.
if ( !defined( 'YOURLS_ABSPATH' ) ) {
	die();
}

yourls_add_filter( 'shunt_add_new_link', 'uwu_dnu_block_new_url', 10, 4 );
yourls_add_filter( 'shunt_html_addnew', 'uwu_dnu_hide_new_url_form', 10, 1 );

/**
 * Determine whether new URL creation is disabled.
 *
 * Activating the plugin disables creation by default. Define
 * YOURLS_DISABLE_NEW_URLS as false in user/config.php to temporarily
 * allow creation without deactivating the plugin.
 *
 * @return bool
 */
function uwu_dnu_is_disabled() {
	if ( !defined( 'YOURLS_DISABLE_NEW_URLS' ) ) {
		return true;
	}

	return YOURLS_DISABLE_NEW_URLS === true;
}

/**
 * Short-circuit all new short URL creation requests.
 *
 * @param mixed  $pre     Default shunt value.
 * @param string $url     Requested destination URL.
 * @param string $keyword Requested short URL keyword.
 * @param string $title   Requested title.
 * @return mixed
 */
function uwu_dnu_block_new_url( $pre, $url, $keyword, $title ) {
	if ( !uwu_dnu_is_disabled() ) {
		return $pre;
	}

	return [
		'status'     => 'fail',
		'code'       => 'error:disabled',
		'message'    => 'Creation of new short URLs is temporarily disabled.',
		'errorCode'  => '503',
		'statusCode' => '503',
		'shorturl'   => '',
		'title'      => $title,
		'url'        => [
			'keyword' => $keyword,
			'url'     => $url,
			'title'   => $title,
			'date'    => '',
			'ip'      => '',
			'clicks'  => 0,
		],
	];
}

/**
 * Replace the admin creation form with a maintenance notice.
 *
 * @param mixed $pre Default shunt value.
 * @return mixed
 */
function uwu_dnu_hide_new_url_form( $pre ) {
	if ( !uwu_dnu_is_disabled() ) {
		return $pre;
	}

	echo '<div id="new_url" style="color:white;"><p><strong>Creation of new short URLs is temporarily disabled.</strong></p></div>';

	return true;
}

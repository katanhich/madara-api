<?php
require 'formatting.php';
require 'functions.php';

function slugify_text( $text ): string {
	$text = remove_accents( $text );
	$text = sanitize_title_with_dashes( $text );

	if ( empty( $text ) ) {
		return 'n-a';
	}

	return $text;
}

/**
 * @throws Exception
 */
function my_current_time( $type, $gmt = 1 ): string {
	if ( 'timestamp' === $type || 'U' === $type ) {
		return time();
	}

	if ( 'mysql' === $type ) {
		$type = 'Y-m-d H:i:s';
	}

//	$timezone = $gmt ? new DateTimeZone( 'UTC' ) : new DateTimeZone( '-07:00' ) ;
	// always use gmt
	$timezone = new DateTimeZone( 'UTC' );
	$datetime = new DateTime( 'now', $timezone );

	return $datetime->format( $type );
}
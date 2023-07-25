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
function my_current_time( $type, $gmt = 0 ): string {
	if ( 'mysql' === $type ) {
		$type = 'Y-m-d H:i:s';
	}

	$timezone = $gmt ? new DateTimeZone( 'UTC' ) : new DateTimeZone( '-07:00' ) ;
	$datetime = new DateTime( 'now', $timezone );

	return $datetime->format( $type );
}
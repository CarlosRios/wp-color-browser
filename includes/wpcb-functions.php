<?php
/**
 * Stores any functions that will be used in the plugin
 *
 * @package    WP Color Browser
 * @subpackage Functions
 * @author     Carlos Rios
 * @version    1.0
 */

/**
 * Sanitizes a hexadecimal color, will be removed after 4.6 release
 *
 * @since   1.0
 * @version 1.0
 * @deprecated 4.6
 */
function wpcb_sanitize_hex_color( $color ) {
	// 3 or 6 hex digits, or the empty string.
	if ( preg_match('|^#([A-Fa-f0-9]{3}){1,2}$|', $color ) ){
		return $color;
	}

	return false;
}

/**
 * Figures out if a color is bright or dark and returns an opposite color.
 *
 * @since   1.0
 * @version 1.0
 * 
 * @param  string $hexcolor - a valid hexadecimal color
 * @return string | false
 */
function wpcb_get_contrast( $hexcolor )
{
	if( !wpcb_sanitize_hex_color( $hexcolor ) ){
		return false;
	}

	// Break up hex color into rgb variables
	$r = hexdec( substr( $hexcolor, 1, 2 ) );
	$g = hexdec( substr( $hexcolor, 3, 2 ) );
	$b = hexdec( substr( $hexcolor, 5, 2 ) );

	// Figure out YIQ
	$yiq = ( ( $r * 299 ) + ( $g * 587 ) + ( $b * 114 ) ) / 1000;

	return ( $yiq >= 128 ) ? '#000' : '#FFF';
}
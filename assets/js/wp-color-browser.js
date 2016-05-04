(function( $ )
{
	var theme_color = $('#wpcb-theme-color'),
		android_header = $('#wpcb-android-header'),
		android_url = $( '#wpcb-android-url-bar' ),
		previous_colors = $( '.wpcb-color' );

	$(function() {
		theme_color.wpColorPicker({
			clear: function() {
				android_header.css( 'background-color', '#f0f0f0' );
			},
			change: function(event, ui) {
				android_header.css( 'background-color', ui.color.toString());
				android_url.css( 'color', get_contrast( ui.color.toString() ) );
			}
		});
	});

	/**
	 * Checks the contrast of a given color and determines if the color
	 * should be used with a light or dark color for the text.
	 * 
	 * @param  string hexcolor
	 * @return string
	 */
	function get_contrast( hexcolor ){
		var r = parseInt(hexcolor.substr(1, 2), 16),
			g = parseInt(hexcolor.substr(3, 2), 16),
			b = parseInt(hexcolor.substr(5, 2), 16),
			yiq = ((r * 299) + (g * 587) + (b * 114)) / 1000;
		return (yiq >= 128) ? '#000' : '#FFF';
	}

	/**
	 * Change the color to a previous color.
	 */
	previous_colors.each( function(){
		$(this).on( 'click', function( event ){
			theme_color.wpColorPicker( 'color', $(this).data('color') );
		});
	});

})( jQuery );
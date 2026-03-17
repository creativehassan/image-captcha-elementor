(function( $ ) {
	'use strict';

	$( document ).ready( function() {
		elementor.hooks.addFilter(
			'elementor_pro/forms/content_template/field/image_captcha',
			renderImageCaptchaPreview,
			10,
			4
		);
	} );

	function renderImageCaptchaPreview( inputField, item, i, settings ) {
		var captchas = ( typeof eicData !== 'undefined' ) ? eicData.captchas : {};
		var captchaText = ( typeof eicData !== 'undefined' ) ? eicData.captchaText : '';
		var imageQuantity = parseInt( item.image_quantity, 10 ) || 3;

		var keys   = Object.keys( captchas );
		var choice = keys.sort( function() { return 0.5 - Math.random(); } ).slice( 0, imageQuantity );
		var human  = Math.floor( Math.random() * imageQuantity );

		var output = '<span class="captcha-image">' +
			'<span class="eic_instructions">' + captchaText +
			'&nbsp;<span class="choosen-icon">' + choice[ human ] + '</span>.</span>' +
			'<span class="captcha-icon-section">';

		for ( var idx = 0; idx < choice.length; idx++ ) {
			var value = ( idx === human ) ? 'preview_correct' : 'preview_wrong';
			output += '<label><input type="radio" name="eic_captcha" value="' + value + '" />' + captchas[ choice[ idx ] ] + '</label>';
		}
		output += '</span></span>';

		var contentTemplate = '<input type="hidden" name="eic_honeypot" />' +
			'<div class="eic-form-control-wrap eic_captcha" data-name="eic_captcha" data-quantity="' + imageQuantity + '">' +
			'<span class="eic-form-control eic-radio">' + output + '</span>' +
			'</div>';

		return contentTemplate;
	}
})( jQuery );

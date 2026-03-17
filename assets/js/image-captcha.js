(function( $ ) {
	'use strict';

	function revealHiddenCaptchas() {
		$( '.eic-form-control-wrap.eic_captcha.eic-captcha--hidden' ).each( function() {
			var $captcha = $( this );
			var $form    = $captcha.closest( 'form' );

			if ( ! $form.length ) {
				return;
			}

			$form.on(
				'input.eicReveal',
				'input:not([name="eic_captcha"]):not([name="eic_honeypot"]):not([name="eic_token"]):not([type="hidden"]), textarea, select',
				function() {
					if ( ! $captcha.hasClass( 'eic-captcha--hidden' ) ) {
						return;
					}

					$captcha.removeClass( 'eic-captcha--hidden' );
					$captcha.find( '.eic-form-control.eic-radio' ).hide().slideDown( 300 );
					$form.off( '.eicReveal' );
				}
			);
		} );
	}

	$( document ).ready( revealHiddenCaptchas );

	$( document ).on( 'error submit_success', function() {
		var $captchaWrap = $( '.eic-form-control-wrap.eic_captcha' );

		if ( ! $captchaWrap.length ) {
			return;
		}

		var quantity = parseInt( $captchaWrap.data( 'quantity' ), 10 ) || 3;

		$.ajax( {
			url:  eicData.ajaxUrl,
			type: 'POST',
			data: {
				action:         'eic_regenerate_captcha',
				nonce:          eicData.nonce,
				image_quantity: quantity
			},
			success: function( response ) {
				if ( response.success ) {
					$captchaWrap.find( '.eic-form-control.eic-radio' ).html( response.data.html );
				}
			}
		} );
	} );
})( jQuery );

"use strict";
var iconsForm = ($scope, $) => {
	var allInput = document.querySelectorAll(".elementor-form-fields-wrapper input");
	var allTextarea = document.querySelectorAll(".elementor-form-fields-wrapper textarea");
	var allLabels = document.querySelectorAll(".elementor-form-fields-wrapper .elementor-field-label");
	let icon;
	let inputHeight = jQuery('.elementor-form-fields-wrapper input').outerHeight();
	let labelHeight = jQuery('.elementor-field-label').outerHeight();
	let fontSize;
	let color;

	// Input
	allInput.forEach(function(field) {
		icon = jQuery(field).attr('dce-icon-render');
		if(icon) {
			let wrapper = jQuery('<div class="dce-field-input-wrapper"></div>');
			jQuery(field).wrap( wrapper ).parent().prepend(icon);
			color = jQuery(field).css('color');
			inputHeight = jQuery(field).outerHeight();
			jQuery(field).css('padding-left', inputHeight + 'px' );
			jQuery( '.dce-field-input-wrapper svg').addClass('input-icons');
			jQuery( '.elementor-field-label svg').addClass('label-icons');
			jQuery( '.dce-field-input-wrapper i.input-icons' ).css('font-size', inputHeight - 20 + 'px');
			jQuery( '.dce-field-input-wrapper svg.input-icons' ).css('height', inputHeight - 20 + 'px');
			jQuery( '.dce-field-input-wrapper svg.input-icons' ).css('width', inputHeight - 20 + 'px');
			jQuery( '.dce-field-input-wrapper .input-icons' ).css('top', '10px');
			jQuery( '.dce-field-input-wrapper .input-icons' ).css('left', '10px');
		}
	});

	// Textarea
	allTextarea.forEach(function(textarea) {
		icon = jQuery(textarea).attr('dce-icon-render');
		if(icon) {
			let wrapper = jQuery('<div class="dce-field-input-wrapper"></div>');
			jQuery(textarea).wrap( wrapper ).parent().prepend(icon);
			color = jQuery(textarea).css('color');
			jQuery(textarea).css('padding-left', inputHeight + 'px' );
			jQuery( '.dce-field-input-wrapper svg').addClass('input-icons');
			jQuery( '.elementor-field-label svg').addClass('label-icons');
			jQuery( '.dce-field-input-wrapper i.input-icons' ).css('font-size', inputHeight - 20 + 'px');
			jQuery( '.dce-field-input-wrapper svg.input-icons' ).css('height', inputHeight - 20 + 'px');
			jQuery( '.dce-field-input-wrapper svg.input-icons' ).css('width', inputHeight - 20 + 'px');
			jQuery( '.dce-field-input-wrapper .input-icons' ).css('top', '10px');
			jQuery( '.dce-field-input-wrapper .input-icons' ).css('left', '10px');
		}
	});

	// Labels
	allLabels.forEach(function(label) {
		icon = jQuery(label).attr('dce-icon-render');
		if(icon) {
			$(icon).prependTo( label );

			jQuery( '.elementor-field-label svg' ).css('height', labelHeight + 'px');
			jQuery( '.elementor-field-label svg' ).css('width', labelHeight + 'px');
			jQuery( '.elementor-field-label svg' ).css('margin-right', labelHeight / 4 + 'px');
		}
	});
}

jQuery(window).on('elementor/frontend/init', function() {
	elementorFrontend.hooks.addAction('frontend/element_ready/form.default', iconsForm);
});

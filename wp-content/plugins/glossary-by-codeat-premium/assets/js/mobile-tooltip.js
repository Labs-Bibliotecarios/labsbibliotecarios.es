jQuery(document).ready(function ($) {
	function set_mobile() {
		jQuery("body").removeClass("glossary-mobile");
		jQuery(".glossary-tooltip-content-mobile").toggleClass(
			"glossary-tooltip-content glossary-tooltip-content-mobile"
		);
		jQuery(".glossary-tooltip-text-mobile").toggleClass(
			"glossary-tooltip-text glossary-tooltip-text-mobile"
		);
		if (window.matchMedia("(max-width: 768px)").matches) {
			jQuery("body").addClass("glossary-mobile");
			jQuery(".glossary-tooltip-content").toggleClass(
				"glossary-tooltip-content glossary-tooltip-content-mobile"
			);
			jQuery(".glossary-tooltip-text").toggleClass(
				"glossary-tooltip-text glossary-tooltip-text-mobile"
			);
			if (
				jQuery(".glossary-tooltip-content-mobile").children("span.close")
					.length === 0
			) {
				if (jQuery("body.is-rtl").length === 0) {
					jQuery(".glossary-tooltip-content-mobile").prepend(
						'<span class="close">X</span>'
					);
				} else {
					jQuery(".glossary-tooltip-content-mobile").append(
						'<span class="close">X</span>'
					);
				}
			}
		}
	}
	set_mobile();
	jQuery(window).resize(function () {
		set_mobile();
	});
	jQuery(".glossary-mobile").on(
		"click",
		".glossary-link a, .glossary-link span",
		function (e) {
			jQuery(this)
				.parent()
				.parent()
				.find(".glossary-tooltip-content-mobile")
				.toggleClass("glossary-show-tooltip");
			e.preventDefault();
		}
	);
	jQuery(".glossary-mobile").on(
		"click",
		".glossary-show-tooltip .close",
		function (e) {
			jQuery(this).parent().toggleClass("glossary-show-tooltip");
			e.preventDefault();
		}
	);
});

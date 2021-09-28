function set_attributes(values, index) {
	switch (index) {
		// Tooltip
		case "text_color":
			document.querySelector(".glossary-tooltip-text").style.color =
				values[index];
			break;
		case "text_background":
			document.querySelector(".glossary-tooltip-text").style.background =
				values[index];
			document.querySelector(".glossary-tooltip-content").style.background =
				values[index];
			document.documentElement.style.setProperty("--background", values[index]);
			break;
		case "text_size":
			document.querySelector(".glossary-tooltip-text").style.fontSize =
				values[index] + "px";
			break;
		// Lemma
		case "keyterm_color":
			document.querySelector(".glossary-tooltip .glossary-link").style.color =
				values[index];
			document.querySelector(
				".glossary-tooltip .glossary-underline"
			).style.background = values[index];
			break;
		case "keyterm_background":
			document.querySelector(
				".glossary-tooltip .glossary-link"
			).style.background = values[index];
			document.querySelector(
				".glossary-tooltip .glossary-underline"
			).style.background = values[index];
			break;
		case "keyterm_size":
			document.querySelector(
				".glossary-tooltip .glossary-link"
			).style.fontSize = values[index] + "px";
			document.querySelector(
				".glossary-tooltip .glossary-underline"
			).style.fontSize = values[index] + "px";
			break;
		case "link_keyterm_color":
			document.querySelector(".glossary-tooltip-text a").style.color =
				values[index];
			break;
	}
}

function update_css(values) {
	for (var index in values) {
		if ({}.hasOwnProperty.call(values, index)) {
			set_attributes(values, index);
		}
	}
}

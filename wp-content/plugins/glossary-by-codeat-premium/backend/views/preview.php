<!DOCTYPE html>
<head>
	<?php // phpcs:ignore
	$theme = gl_get_settings();
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	wp_dequeue_script( GT_SETTINGS . '-hint' );
	wp_dequeue_script( GT_SETTINGS . '-mobile-tooltip-js' );
	wp_dequeue_style( GT_SETTINGS . '-a2z-widget' );
	wp_dequeue_style( GT_SETTINGS . '-search-widget' );
	wp_dequeue_style( GT_SETTINGS . '-shortcode' );
	wp_dequeue_style( GT_SETTINGS . '-css' );
	wp_dequeue_style( GT_SETTINGS . '-mobile-tooltip' );

	if ( !defined( 'W3TC' ) ) {
		do_action( 'wp_head' );
	}

	$version = get_option( GT_SETTINGS . '_css_last_edit' );

	if ( !empty( $version ) ) {
		$version = '&amp;version=' . $version;
	}

	?>
	<script type='text/javascript' src='<?php echo str_replace( 'backend/views/', '', plugin_dir_url( __FILE__ ) ); //phpcs:ignore ?>assets/js/preview.js'></script>
	<style>
		body::before {
			height:0;
			width:0;
			position:static;
		}
		body {
			left:110px;
			bottom:25px;
			position: absolute;
		}
		.glossary-tooltip-hover .glossary-tooltip-content,
		.glossary-tooltip-hover .glossary-tooltip-text {
			visibility:visible;
			opacity:1;
		}
		<?php

		if ( 'line' !== $theme[ 'tooltip_style' ] ) {
			?>
			.glossary-tooltip-hover .glossary-tooltip {
				display: block !important;
			}
					<?php
		}

		if ( 'line' === $theme[ 'tooltip_style' ] || empty( $theme[ 'tooltip_style' ] ) ) {
			?>
			.glossary-tooltip-hover .glossary-tooltip-text {
				-webkit-transform:none;
				transform:none;
			}
			<?php
		}

		if ( 'book' === $theme[ 'tooltip_style' ] ) {
			?>
			.glossary-tooltip-hover .glossary-tooltip-content {
				width: 360px;
				margin-left: -120px;
			}
			<?php
		}

		if ( 'fancy' === $theme[ 'tooltip_style' ] ) {
			?>
			.glossary-tooltip-hover .glossary-tooltip-content {
				-webkit-transform: rotateX(0deg) rotateY(0deg) rotateZ(0deg) scale(1);
				transform: rotateX(0deg) rotateY(0deg) rotateZ(0deg) scale(1);
			}
			.glossary-tooltip-content::before {
				border-top-color:var(--background);
			}
			<?php
		}

		if ( 'box' === $theme[ 'tooltip_style' ] ) {
			?>
			.glossary-tooltip-content {
				bottom: 20px;
			}
			<?php
		}

		$customizer = new Glossary\Frontend\Css_Customizer;
		$customizer->initialize();
		echo $customizer->print_css(); // phpcs:ignore
		?>
	</style>
</head>
<body>
	<span class="glossary-tooltip glossary-tooltip-hover">
		<span class="glossary-link">
			<a href="#" target="_blank" class="glossary-underline">Glossary Term (hover me)</a>
		</span>
		<span class="glossary-tooltip-content clearfix">
			<span class="glossary-tooltip-text">Lorem ipsum dolor sit amet, consectetur adipiscing elit... <a href="#">More</a></span>
		</span>
	</span>
</body>
</html>

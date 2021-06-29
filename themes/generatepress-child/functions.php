<?php

add_action( 'wp_enqueue_scripts', function() {
    wp_dequeue_style( 'generate-fonts' );
} );

add_action( 'admin_init', function() {
    add_filter( 'generate_google_fonts_array', '__return_empty_array' );
} );
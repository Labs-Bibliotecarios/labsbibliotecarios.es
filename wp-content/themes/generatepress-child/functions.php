<?php

// Modificación del logo Wordpress en página de Login
function my_login_logo() { ?>
    <style type="text/css">
        #login h1 a, .login h1 a {
            background-image: url(https://labsbibliotecarios.es/wp-content/uploads/2021/09/Logo-LABBBs-nuevo-login.png);
		height:100px;
		width:300px;
		background-size: 300px 130px;
		background-repeat: no-repeat;
		background-attachment:fill;
        padding-bottom: 30px;
        }
		
		#loginform:before {
			content:"Si aún no tienes cuenta puedes crearla en labsbibliotecarios.es para acceder al Foro.";
		}
		
		.login label {
			margin-top: 10px;
		}
    </style>
<?php }
add_action( 'login_enqueue_scripts', 'my_login_logo' );




function my_login_logo_url() {
    return home_url();
}
add_filter( 'login_headerurl', 'my_login_logo_url' );

function my_login_logo_url_title() {
    return 'LabsBibliotecarios';
}
add_filter( 'login_headertitle', 'my_login_logo_url_title' );



add_shortcode( 'author_url', function() {
    return get_author_posts_url( get_current_user_id() );
} );



// Configuración de AVATAR

// add_filter( 'avatar_defaults', 'wpb_new_gravatar' );
// function wpb_new_gravatar ($avatar_defaults) {
// $myavatar = 'https://labsbibliotecarios.es/wp-content/uploads/2021/08/Avatar-4.png';
// $avatar_defaults[$myavatar] = "Default Gravatar";
// return $avatar_defaults;
// }
// 
// // Random Chameleon Avatar


add_filter( 'pre_option_avatar_default', 'chameleon_default_avatar' );

function chameleon_default_avatar ( $value )
{
  return admin_url( 'images' ) . '/cham_avatars/Avatars_'.rand( 1 , 4 ).'.png';
}


// DISCOURSE EMAIL VERIFICATION
// 
add_filter( 'discourse_email_verification', 'wpdc_custom_disable_email_verification' );
function wpdc_custom_disable_email_verification() {

    return false;
}
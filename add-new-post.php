<?php

/*
 * Plugin Name:       Add New Post
 * Plugin URI:        https://github.com/mehrazmorshed/add-new-post
 * Description:       Create a form by using shortcode 'post_form' and insert a new post while submitting data.
 * Version:           1.0
 * Tested Up to:      6.3
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Mehraz Morshed
 * Author URI:        https://profiles.wordpress.org/mehrazmorshed/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://github.com/mehrazmorshed/add-new-post
 * Text Domain:       add-new-post
 * Domain Path:       /languages
 */

/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * EXIT IF ACCESSED DIRECTLY
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * REGISTERING SHORTCODE
 */
add_shortcode('post_form', 'add_new_post_form');

/**
 * RENDERING A FORM
 */
function add_new_post_form() {
    $new_post_form = '';
    $new_post_form .= '<div class="add-new-form">';
    $new_post_form .= '<p class="form-title">Add New Post</p>';
    $new_post_form .= '<form method="post" action="">';
    $new_post_form .= '<br>';
    $new_post_form .= '<label for="post_title">Post Title :</label>';
    $new_post_form .= '<br>';
    $new_post_form .= '<input type="text" id="post_title" name="post_title" class="form-control" placeholder="Enter Post Title">';
    $new_post_form .= '<br>';
    $new_post_form .= '<label for="post_content">Post Content :</label>';
    $new_post_form .= '<br>';
    $new_post_form .= '<textarea id="post_content" name="post_content" class="form-control" placeholder="Enter Post Content"></textarea>';
    $new_post_form .= '<br>';
    $new_post_form .= '<label for="your_name">Your Name :</label>';
    $new_post_form .= '<br>';
    $new_post_form .= '<input type="text" id="your_name" name="your_name" class="form-control" placeholder="Enter Your Name">';
    $new_post_form .= '<br>';
    $new_post_form .= '<label for="email_address">Email Address :</label>';
    $new_post_form .= '<br>';
    $new_post_form .= '<input type="text" id="email_address" name="email_address" class="form-control" placeholder="Enter Email Address">';
    $new_post_form .= '<br>';
    $new_post_form .= '<input type="submit" name="post_submit" class="btn btn-block btn-primary" value="Submit Post">';
    $new_post_form .= '</form>';
    $new_post_form .= '</div>';

    return $new_post_form;
 }

/**
 * CREATING A NEW USER
 */
add_action( 'init', 'create_new_user' );

/**
 * CALLBACK FUNCTION
 */
function create_new_user() {
    if( isset( $_POST[ 'post_submit' ] ) ) {
        $username = sanitize_text_field( $_POST[ 'your_name' ] );
        $email = sanitize_text_field( $_POST[ 'email_address' ] );
        $password = '123456';
        if( username_exists( $username ) || email_exists( $email ) ) {
            return;
        }
        $user_id = wp_create_user( $username, $password, $email );
        if( is_wp_error( $user_id ) ) {
            die( $user_id->get_error_message() );
        }
        $new_user = get_user_by( 'id', $user_id );
    }
}

/**
 * CREATING A POST
 */
add_action('init','create_a_new_post');

/**
 * CALLBACK FUNCTION
 */
function create_a_new_post(){
    if( isset( $_POST[ 'post_submit' ] ) ) {
        $post_title = sanitize_text_field( $_POST[ 'post_title' ] );
        $post_content = sanitize_textarea_field( $_POST[ 'post_content' ] );
        $your_name = sanitize_text_field( $_POST[ 'your_name' ] );
        $email_address = sanitize_text_field( $_POST[ 'email_address' ] );
        $new_post_array = array(
            'post_title' => $post_title,
            'post_content' => $post_content,
            'post_status' => 'publish',
            'post_author' => get_current_user_id(),
            'post_type' => 'post'
        );
        wp_insert_post( $new_post_array );
    }
}

/**
 * SENDING EMAIL
 */
function add_new_post_data() {
    if( isset( $_POST[ 'post_submit' ] ) ) {
        $mail_head = sanitize_text_field( $_POST[ 'post_title' ] );
        $mail_body = sanitize_textarea_field( $_POST[ 'post_content' ] );
        $receiver_name = sanitize_text_field( $_POST[ 'your_name' ] );
        $receiver_email = sanitize_text_field( $_POST[ 'email_address' ] );
        $to = $receiver_email;
        $subject = $mail_head;
        $message = $mail_body. '<br>' .$receiver_name;
        wp_mail( $to, $subject,$message );
    }
 }

/**
 * ENQUEUE CSS STYLE
 */
add_action( 'wp_enqueue_scripts', 'add_new_post_enqueue_style' );

/**
 * CALLBACK FUNCTION
 */
function add_new_post_enqueue_style() {
    wp_enqueue_style( 'add-new-post-style', plugins_url( 'assets/css/add-new-post-style.css', __FILE__ ) );
}

/**
 * ENQUEUE JavaScript & jQuery
 */
add_action( 'wp_enqueue_scripts', 'add_new_post_enqueue_script' );

/**
 * CALLBACK FUNCTION
 */
function add_new_post_enqueue_script() {
    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'add-new-post-script', plugins_url( 'assets/js/add-new-post-script.js', __FILE__ ), array(), '1.0.0', 'true' );
}

/**
 * ENQUEUE ADMIN SETTINGS STYLE
 */
add_action( 'admin_enqueue_scripts', 'add_new_post_style_settings' );

/**
 * CALLBACK FUNCTION
 */
function add_new_post_style_settings() {
    wp_enqueue_style( 'add-new-post-settings', plugins_url( 'assets/css/add-new-post-settings.css', __FILE__ ), false, "1.0.0" );
}

/**
 * RENDERING ADMIN SETTINGS PAGE
 */
add_action( 'admin_menu', 'add_new_post_settings_page' );

/**
 * CALLBACK FUNCTION
 */
function add_new_post_settings_page() {
    add_menu_page( 'Add New Post Settings', 'Add New Post', 'manage_options', 'add-new-post', 'add_new_post_settings', 'dashicons-admin-plugins', 101 );
}

/**
 * CALLBACK FUNCTION PAGE RENDERING
 */
function add_new_post_settings() {
    ?>
    <div class="add-new-post-main">
        <!-- add-new-post-body CLASS STARTS-->
        <div class="add-new-post-body add-new-post-common">
            <h1 id="page-title"><?php print esc_attr( 'Add New Post Settings' ); ?></h1>
            <!-- FORM STARTS -->
            <form action="options.php" method="post">
                <?php wp_nonce_field( 'update-options' ); ?>
                <!-- FORM BACKGROUND COLOR -->
                <label for="add-new-post-form-bg-color" name="add-new-post-form-bg-color"><?php print esc_attr( 'Form Background Color' ); ?></label>
                <input type="color" id="add-new-post-form-bg-color" name="add-new-post-form-bg-color" value="<?php print get_option('add-new-post-form-bg-color'); ?>">
                <!-- SUBMIT BUTTON COLOR -->
                <label for="add-new-post-button-color" name="add-new-post-button-color"><?php print esc_attr( 'Submit Button Color' ); ?></label>
                <input type="color" id="add-new-post-button-color" name="add-new-post-button-color" value="<?php print get_option('add-new-post-button-color'); ?>">
                <!-- SUBMIT BUTTON HOVER -->
                <label for="add-new-post-button-hover" name="add-new-post-button-hover"><?php print esc_attr( 'Submit Button Hover' ); ?></label>
                <input type="color" id="add-new-post-button-hover" name="add-new-post-button-hover" value="<?php print get_option('add-new-post-button-hover'); ?>">
                <!-- INPUT TYPES -->
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="page_options" value="add-new-post-form-bg-color, add-new-post-button-color, add-new-post-button-hover">
                <input class="button button-primary" type="submit" name="submit" value="<?php _e('Save Changes', 'add-new-post'); ?>">
            </form>
            <!-- FORM ENDS -->
        </div>
        <!-- add-new-post-body CLASS ENDS-->
        <!-- add-new-post-aside CLASS STARTS-->
        <div class="add-new-post-aside add-new-post-common">
            <!-- AUTHOR INFO SECTION STARTS -->
            <h2 class="aside-title"><?php print esc_attr( 'Plugin Author Info' ); ?></h2>
            <div class="author-card">
                <a class="link" href="https://profiles.wordpress.org/mehrazmorshed/" target="_blank">
                    <img class="center" src="<?php print plugin_dir_url( __FILE__ ) . '/assets/images/author.png'; ?>" width="128px">
                    <h3 class="author-title"><?php print esc_attr( 'Mehraz Morshed' ); ?></h3>
                </a>
                <h4 class="author-title"><?php print esc_attr( 'WordPress Developer' ); ?></h4>
                <h1 class="author-title">
                    <a class="link" href="https://www.facebook.com/mehrazmorshed" target="_blank"><span class="dashicons dashicons-facebook"></span></a>
                    <a class="link" href="https://twitter.com/mehrazmorshed" target="_blank"><span class="dashicons dashicons-twitter"></span></a>
                    <a class="link" href="https://www.linkedin.com/in/mehrazmorshed" target="_blank"><span class="dashicons dashicons-linkedin"></span></a>
                </h1>
            </div>
            <!-- AUTHOR INFO SECTION ENDS -->
            <!-- OTHER USEFUL PLUGINS SECTION STARTS -->
            <h3 class="aside-title"><?php print esc_attr( 'Other Useful Plugins' ); ?></h3>
            <div class="author-card">
                <a class="link" href="https://wordpress.org/plugins/customized-login" target="_blank"><span class="dashicons dashicons-admin-plugins"></span> <b>Custom Login Page</b></a>
                <hr>
                <a class="link" href="https://wordpress.org/plugins/tap-to-top" target="_blank">
                <span class="dashicons dashicons-admin-plugins"></span> <b>Tap To Top</b></a>
                <hr>
                <a class="link" href="https://wordpress.org/plugins/hide-admin-navbar" target="_blank"><span class="dashicons dashicons-admin-plugins"></span> <b>Hide Admin Navbar</b></a>
                <hr>
                <a class="link" href="https://wordpress.org/plugins/turn-off-comments" target="_blank"><span class="dashicons dashicons-admin-plugins"></span> <b>Turn Off Comments</b></a>
            </div>
            <!-- OTHER USEFUL PLUGINS SECTION ENDS -->
            <!-- DONATION SECTION STARTS -->
            <h3 class="aside-title"><?php print esc_attr( 'Add New Post' ); ?></h3>
            <a class="link" href="https://www.buymeacoffee.com/mehrazmorshed" target="_blank">
                <button class="button button-primary btn">
                    <?php print esc_attr( 'Donate To This Plugin' ); ?>
                </button>
            </a>
            <!-- DONATION SECTION ENDS -->
        </div>
        <!-- add-new-post-aside class ENDs-->
    </div>
    <?php
}

/**
 * UPDATING CSS STYLES
 */
add_action( 'wp_head', 'add_new_post_css_update' );

/**
 * CALLBACK FUNCTION
 */
function add_new_post_css_update() {
    ?>
    <style type="text/css">
        .add-new-form {
            background-color: <?php print get_option( 'add-new-post-form-bg-color' ); ?> !important;
        }
        input[type=submit] {
            background-color: <?php print get_option( 'add-new-post-button-color' ); ?> !important;
        }
        input[type=submit]:hover {
            background-color: <?php print get_option( 'add-new-post-button-hover' ); ?> !important;
        }
    </style>
    <?php
}

/**
 * REGISTERING REDIRECT ON ACTIVATION
 */
register_activation_hook( __FILE__, 'add_new_post_plugin_activation' );

/**
 * REGISTER CALLBACK FUNCTION
 */
function add_new_post_plugin_activation() {

    add_option( 'add_new_post_redirect_on_activation', true );
}

/**
 * REDIRECT ON PLUGIN ACTIVATION
 */
add_action( 'admin_init', 'add_new_post_plugin_redirect' );

/**
 * CALLBACK FUNCTION
 */
function add_new_post_plugin_redirect() {

    if( get_option( 'add_new_post_redirect_on_activation', false ) ) {

        delete_option( 'add_new_post_redirect_on_activation' );
        if( ! isset( $_GET[ 'active-multi' ] ) ) {

            wp_safe_redirect( admin_url( 'admin.php?page=add-new-post' ) );
            exit;
        }
    }
}

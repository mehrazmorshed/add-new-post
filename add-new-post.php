<?php

/*
 * Plugin Name:       Add New Post
 * Plugin URI:        https://wordpress.org/plugins/add-new-post/
 * Description:       Handle the basics with this plugin.
 * Version:           1.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Mehraz Morshed
 * Author URI:        https://profiles.wordpress.org/mehrazmorshed/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://example.com/my-plugin/
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

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 1. registers a shortcode
 */
add_shortcode('post_form', 'add_new_post_form');

/**
 * 2. placing that shortcode on a page renders a form.
 * The form contains these fields-
 * Post Title, Post Content, Your Name and Email Address
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
 * 5. Optionally, create a new user using the given email and assign it to the post
 */

function create_new_user() {

    if(isset($_POST['post_submit'])) {

        $username = sanitize_text_field($_POST['your_name']);
        $email = sanitize_text_field($_POST['email_address']);
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

add_action( 'init', 'create_new_user' );





/**
 * 3. submitting the form creates a post using the corresponding field data
 */

function create_a_new_post(){

    if(isset($_POST['post_submit'])) {

        $post_title = sanitize_text_field($_POST['post_title']);
        $post_content = sanitize_textarea_field($_POST['post_content']);
        $your_name = sanitize_text_field($_POST['your_name']);
        $email_address = sanitize_text_field($_POST['email_address']);

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
add_action('init','create_a_new_post');


/**
 * 4. additionally it sends an email 
 * to the mentioned email address
 * containing the newly published post URL
 */


  function add_new_post_data() {
    if(isset($_POST['post_submit'])) {
        $mail_head = sanitize_text_field($_POST['post_title']);
        $mail_body = sanitize_textarea_field($_POST['post_content']);
        $receiver_name = sanitize_text_field($_POST['your_name']);
        $receiver_email = sanitize_text_field($_POST['email_address']);



        $to = $receiver_email;
        $subject = $mail_head;
        $message = $mail_body. '<br>' .$receiver_name;

        wp_mail($to, $subject,$message);
    }
 }


/**
 * enqueue css style
 */

function add_new_post_enqueue_style() {wp_enqueue_style( 'add-new-post-style', plugins_url( 'assets/css/add-new-post-style.css', __FILE__ ) );
}
add_action( 'wp_enqueue_scripts', 'add_new_post_enqueue_style' );

/**
 * enqueue JavaScript and jQuery
 */
function add_new_post_enqueue_script() {

    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'add-new-post-script', plugins_url( 'assets/js/add-new-post-script.js', __FILE__ ), array(), '1.0.0', 'true' );
}
add_action( 'wp_enqueue_scripts', 'add_new_post_enqueue_script' );
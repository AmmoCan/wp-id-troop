<?php
/*
Plugin Name: WP ID Troop
Plugin URI: http://www.2-Drops.com
Description: Simple non-bloated Military and Veteran verification using the ID.me API.
Version: 1.0.0
Author: AmmoCan
Author URI: http://www.linkedin.com/in/ammocan
License: GPLv2 or later
Text Domain: wp-id-troop
*/

//do not allow direct access
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

require_once 'vendor/autoload.php';

class WpIdTroop {};

function wp_id_troop_settings_page() {
?>
  <div class="wrap">
    <h1>ID.me API Info.</h1>
    <form method="post" action="options.php">
      <?php
        settings_fields('section');
        do_settings_sections('wpidtroop-options');      
        submit_button(); 
      ?>          
    </form>
  </div>
<?php
}

function add_wp_id_troop_menu_item() {
  
	add_menu_page(
	  'WP ID Troop',
	  'WP ID Troop',
	  'manage_options',
	  'wpidtroop',
	  'wp_id_troop_settings_page',
	  null,
	  99
  );
	
}
add_action('admin_menu', 'add_wp_id_troop_menu_item');

function display_client_redirect_element() {
	?>
    <input type="text" name="client_redirect" id="client_redirect" value="<?php echo get_option('client_redirect')?>" />
  <?php
}

function display_client_id_element() {
	?>
    <input type="text" name="client_id" id="client_id" value="<?php echo get_option('client_id')?>" />
  <?php
}

function display_client_secret_element() {
	?>
    <input type="password" name="client_secret" id="client_secret" value="<?php echo get_option('client_secret')?>" />
  <?php
}

function display_wp_id_troop_fields() {
	add_settings_section('section', 'All Settings', null, 'wpidtroop-options');
	
	add_settings_field('client_redirect', 'Redirect Uri', 'display_client_redirect_element', 'wpidtroop-options', 'section');
	add_settings_field('client_id', 'Client ID', 'display_client_id_element', 'wpidtroop-options', 'section');
  add_settings_field('client_secret', 'Client Secret', 'display_client_secret_element', 'wpidtroop-options', 'section');
  
  register_setting('section', 'client_redirect');
  register_setting('section', 'client_id');
  register_setting('section', 'client_secret');
}

add_action('admin_init', 'display_wp_id_troop_fields');

function wp_id_troop_display_btn() {
  
  if ( ! isset( $_SESSION['payload'] ) ):
  ?>
  
    <form method="post" action="<?php echo admin_url( 'admin-post.php'); ?>">
      
      <input type="hidden" name="action" value="oauth_submit" />
      <input type="image" src="https://s3.amazonaws.com/idme/developer/idme-buttons-2.0.1/assets/img/btn-alt-Troop.png" border="0" class="idme-btn-primary-sm-Troop" alt="Submit" />
      
    </form>
  
  <?php

  else: 
  
    echo $_SESSION['payload'];
  
  endif;
}

// Use for creating a shortcode for the ID.me verification button,
// so user can place it on the page/post/form of their choice
function wp_id_troop_btn_shortcode() {
    ob_start();
    wp_id_troop_display_btn();
 
    return ob_get_clean();
}
add_shortcode( 'wp_id_troop', 'wp_id_troop_btn_shortcode' );
  
function wp_id_troop_handle_oauth() {
  
  // Get the saved application info
  $client_id = get_option( 'client_id' );
  $client_secret = get_option( 'client_secret' );
  $client_redirect = get_option( 'client_redirect' );
  $url_authorize = 'https://api.id.me/oauth/authorize';
  $token_url = 'https://api.id.me/oauth/token';
  $att_url = 'https://api.id.me/api/public/v2/attributes.json';
  
  if ( $client_id && $client_secret ) {
    $provider = new \League\OAuth2\Client\Provider\GenericProvider([
       'clientId'                => $client_id,
       'clientSecret'            => $client_secret,
       'redirectUri'             => $client_redirect,
       'urlAuthorize'            => $url_authorize,
       'urlAccessToken'          => $token_url,
       'urlResourceOwnerDetails' => $att_url,
       
    ]);
  
  }
  
  // If this is a form submission, start the workflow
  // (Step 2)
  if ( ! isset( $_GET['code'] ) && $_SERVER['REQUEST_METHOD'] === 'POST' ) {
  
    // Fetch the authorization URL from the provider; this returns the
    // urlAuthorize option and generates and applies any necessary parameters
    // (e.g. state).
    $authorizationUrl = $provider->getAuthorizationUrl();
    
    // Get the state generated for you and store it to the session.
    //$authUrl = $provider->getAuthorizationUrl();
    $_SESSION['oauth2state'] = $provider->getState();
    
    // Redirect the user to the authorization URL.
    //header("Location: ".$authUrl);
    header('Location: ' . filter_var( $authorizationUrl, FILTER_SANITIZE_URL ) );
    exit;
  
  // Check given state against previously stored one to mitigate CSRF attack
  // (Step 3 just happened and the user was redirected back)
  } elseif ( empty( $_GET['state'] ) || ( $_GET['state'] !== $_SESSION['oauth2state'] ) ) {
  
    unset($_SESSION['oauth2state']);
    exit('Invalid state');
  
  } else {
  
    // Try to get an access token using the authorization code grant.
    // Pass in the affiliation in the scope
    // (Step 4)
    $accessToken = $provider->getAccessToken( 'authorization_code', [
       'code' => $_GET['code'], 'scope' => 'military'
    ]);
      
    // Save the token for future use
    update_option( 'wpidtroop_token', $accessToken->getToken(), TRUE );
  }
}
add_action( 'admin_post_oauth_submit', 'wp_id_troop_handle_oauth' );

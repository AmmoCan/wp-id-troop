WP ID Troop 
===========

* ! Warning : This plugin isn't 100% tested yet - Results may not be desirable

WordPress.org plugin solution to adding ID.me Troop/Military verification to your website, that “just works”.

## Description

This plugin adds a custom menu section labeled 'WP ID Troop', where you can enter your 'Redirect Uri', 'Client ID' and 'Client Secret' ID.me API information. You can then use a shortcode to place a 'Verify with Troop ID' button to your website. As a user clicks/presses on the button, they will be sent to ID.me to sign in and approve the verification of their military status. Once the verification is complete, the user will then be redirected to the Uri you had used in your application and saved in the 'Redirect Uri' field under the plugin's settings.

### Requirements:
  * API approval from ID.me.
  * A sense of humor.

## Installation

You will need to install this manually:

1. Unzip the archive and put the ‘wp-id-troop’ folder into your plugins folder (/wp-content/plugins/).
2. Activate the plugin from the Plugins menu.
3. Go to and click/press on 'WP ID Troop'.
4. Add your 'Redirect Uri', 'Client ID' and 'Client Secret' info. and click/press the 'Save Changes' button.
5. Add this shortcode: [wp_id_troop] to the page, post, etc., where you would like the 'Verify with Troop ID' button to show.

## Frequently Asked Questions

#### Will this work with my theme?
Yes, this plugin will work with any WordPress.org theme.
#### Do I really need to create my own ID.me API account?
Yes, not only is it free, but in order for this plugin to work you need to have a 'Client ID' and 'Client Secret' code issued by ID.me, as well as the 'Redirect Uri' you had used in your application.
#### Do I really need a sense of humor?
No, not really, but it couldn't hurt.

## Resource Links

1. [Get ID.me API Keys](https://api.id.me/registration/new)
2. [ID.me API Docs](https://developer.id.me/documentation)

## Screenshots

Coming Soon! Meanwhile, you can check out the screenshots at: https://developer.id.me/documentation

## Changelog

#### 0.9.0
* Start version.

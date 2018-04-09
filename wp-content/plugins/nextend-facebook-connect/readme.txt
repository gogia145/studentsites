=== Nextend Social Login and Register (Facebook, Google, Twitter) ===
Contributors: nextendweb
Tags: social login, facebook, google, twitter, linkedin, register, login, social, nextend facebook connect, social sign in
Donate link: https://www.facebook.com/nextendweb
Requires at least: 4.5
Tested up to: 4.9
Stable tag: 3.0.2
Requires PHP: 5.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

One click registration & login plugin for Facebook, Google, Twitter and more. Quick setup and easy configuration.

== Description ==

>Nextend Facebook Connect, Nextend Google Connect and Nextend Twitter Connect are discontinued and Nextend Social Login takes their place. Feel free to update the old plugins and enjoy Nextend Social Login.

>[Demo](https://try-nextend-social-login.nextendweb.com/wp-login.php)  |  [Tutorial videos](https://www.youtube.com/watch?v=buPTza2-6xc&list=PLSawiBnEUNftt3EDqnP2jIXeh6q0pZ5D8&index=1)  |  [Docs](https://nextendweb.com/nextend-social-login-docs/documentation/)  |  [Support](https://nextendweb.com/contact-us/)  |  [Pro Addon](https://nextendweb.com/social-login/)

Nextend Social Login is a professional, easy to use and free WordPress plugin. It lets your visitors  register and login to your site using their social profiles instead of forcing them to spend valuable time to fill out the default registration form. Besides that, they don't need to wait for validation emails or keep track of their username and password anymore.

[youtube https://www.youtube.com/watch?v=buPTza2-6xc]

Nextend Social Login seamlessly integrates with your existing WordPress login and registration form. Existing users can add or remove their social accounts at their WordPress profile page. A single user can attach as many social account as they want allowing them to log in with Facebook, Google or Twitter.

#### Three popular providers: Facebook, Google and Twitter

Providers are the services which the visitors can use to register and log in to your site. Nextend Social Login allows your visitors to log in with their account from the most popular social networks: Facebook, Google and Twitter.

#### Free version features

* One click registration and login via Facebook, Google and Twitter
* Your current users can easily connect their Facebook, Google or Twitter profiles with their account
* Social accounts are tied to a WordPress user account so every account can be accessed with and without social account
* You can define custom redirect URL after the registration (upon first login) using any of the social accounts.
* You can define custom redirect URL after each login with any of the enabled social accounts.
* Display Facebook, Google, Twitter profile picture as avatar
* Login widget and shortcodes
* Customizable designs to match your site
* Editable and translatable texts on the login buttons
* Very simple to setup and use
* Clean, user friendly UI
* Fast and helpful support

#### Additional features in the [Pro addon](https://nextendweb.com/social-login/)

* WooCommerce compatibility
* Pro providers: LinkedIn and more coming soon
* Configure whether email address should be asked on registration at each provider
* Configure whether username should be asked on registration at each provider
* Choose from icons or wide buttons
* Several login layouts
* Restrict specific user roles from using the social logins. (You can restrict different roles for each provider.)
* Assign specific user roles to the newly registered users who use any social login provider. (You can set different roles for each provider.)

#### Usage

After you activated the plugin configure and enable the provider you want to use, then the plugin will automatically

* add the login buttons to the WordPress login page. See screenshot #1
* add the account linking buttons to the WordPress profile page. See screenshot #2

== Frequently Asked Questions ==

= 1. Why can't I get the real email address of the people who register with Twitter automatically? =
Twitter offers no way to get a valid email address for a user. This is why they have to manually enter an email address upon registration.

= 2. Why are random email addresses generated for users registering with their FaceBook account? =
When the user tries to register with their Facebook account Facebook pops up a window where each user can view what kind of access they give for the app. In this modal they can chose not to share their email address. When they're doing so we generate a random email address for them. They can of course change this at their profile.
Also in the pro version it's possible to ask an email address if they chose not to share it at the app settings.

= 3. What should I do when I experience any problems? =
[Contact us](https://nextendweb.com/contact-us/) via email and explain the issue you have.

= 4. How can I translate the plugin? =
Find the `.pot` file at the /languages folder. From that you can start the translation process. [Drop us](https://nextendweb.com/contact-us/) the final `.po` and `.mo` files and we'll put them to the next releases.

= 5. I have a feature request... =
That's awesome! [Contact us](https://nextendweb.com/contact-us/) and let's discuss the details.

= 6. Does Nextend Social Login work with BuddyPress? =
Unfortunately, currently there are no BuddyPress specific settings. However your users will still be able login and register at the normal WordPress login page. Then when logged in they can use every BuddyPress feature their current user role have access to.

== Installation ==

### Automatic installation

1. Search for Nextend Social Login through 'Plugins > Add New' interface.
2. Find the plugin box of Nextend Social Login and click on the 'Install Now' button.
3. Then activate the Nextend Social Login plugin.
4. Go to the 'Settings > Nextend' Social Connect to see the available providers.
5. Configure the provider you would like to use. (You'll find detailed instructions for each provider.)
6. Test the configuration then enable the provider.

### Manual installation

1. Download [Nextend Social Login](https://downloads.wordpress.org/plugin/nextend-facebook-connect.zip)
2. Upload Nextend Social Login through 'Plugins > Add New > Upload' interface or upload nextend-facebook-connect folder to the `/wp-content/plugins/` directory.
3. Activate the Nextend Social Login plugin through the 'Plugins' menu in WordPress.
4. Go to the 'Settings > Nextend Social Connect' to see the available providers.
5. Configure the provider you would like to use. (You'll find detailed instructions for each provider.)
6. Test the configuration then enable the provider.


== Screenshots ==

1. Nextend Social Login and Register on the main WP login page
2. Nextend Social Login and Register in the profile page for account linking

== Changelog ==

= 3.0.2 =
* Fixed upgrade script

= 3.0.1 =
* 3.0 is a major update. Please [review the update guide](https://nextendweb.com/nextend-social-login-docs/upgrade-nextend-social-login-3/) guide before upgrading.
* Nextend Facebook Connect renamed to Nextend Social Login and contains Google and Twitter providers too.
* Brand new UI
* Popup login
* Pro Addon

= 2.1 =
* New providers: Twitter and Google
* Major UI redesign
* API testing before a provider is enabled to eliminate possible configuration issues

= 2.0.2 =
* Fix: Fatal error: Call to undefined method Facebook\Facebook::getAccessToken()

= 2.0.1 =
* Fix: Redirect uri mismatch in spacial server environment

= 2.0.0 =
* The latest Facebook PHP API used: https://github.com/facebook/php-graph-sdk
* Facebook SDK for PHP requires PHP 5.4 or greater.
* Fix: Facebook 2.2 API does not work anymore
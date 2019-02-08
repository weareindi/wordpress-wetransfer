=== Ozpital WPWeTransfer ===
Contributors: ozpital
Donate link: https://www.paypal.me/ozpital
Tags: wetransfer, transfer, file upload, embed
Requires at least: 4.9.6
Tested up to: 5.0.3
Stable tag: 0.2.1
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Upload directly to WeTransfer without leaving WordPress

== Description ==

Utilising the WeTransfer API (https://developers.wetransfer.com/) you can now place a WeTransfer upload form on your WordPress website with ease.
No longer do you need to send your users away from your site. Let them transfer files to WeTransfer and share the WeTransfer URL with you programmatically.

== Installation ==

1. Sign up to the WeTransfer API (https://developers.wetransfer.com/) and create a WeTransfer API Key to use with this plugin.
2. Upload the plugin files to the `/wp-content/plugins/ozpital-wpwetransfer` directory, or install the plugin through the WordPress plugins screen directly.
3. Activate the plugin through the 'Plugins' screen in WordPress
4. Use the Settings->Ozpital WPWeTransfer screen to apply your WeTransfer API Key
5. Use the shortcode `[ozpital-wpwetransfer]` to display in your posts/pages
6. Listen to the `ozpital-wpwetransfer-success` javascript event to handle a successful transfer programmatically. eg: `document.addEventListener('ozpital-wpwetransfer-success', function(event) { console.log(event); });`


== Screenshots ==

1. Administration area
2. Initial frontend view
3. Added files
4. Files uploading
5. Upload successful

== Changelog ==

= 0.2.1 =
* Add `ozpital-wpwetransfer-change` event
* Add `ozpital-wpwetransfer-transferring` event
* Fix issue with WeTransfer URL not displaying when it should

= 0.2.0 =
* Add settings link to plugin listing
* Add custom WeTransfer message field

= 0.1.2 =
* Fix for script editor not appearing in admin menu

= 0.1.1 =
* Added support for defined `WETRANSFER_API_KEY`
* Added support for env set `WETRANSFER_API_KEY`
* Added script editor to admin options page (incl. Contact Form 7 (CF7) example)
* Changed plugin actions priority

= 0.1.0 =
* Refactor for WeTransfer API V2
* Amend styling to better resemble WeTransfer.com
* Remove custom web font
* Now compatible with IE10+

= 0.0.10 =
* Add files array to "ozpital-wpwetransfer-success" event

= 0.0.8|0.0.9 =
* Prevent activation if system PHP version is below 7.0

= 0.0.7 =
* Change minimum platform requirements

= 0.0.6 =
* Initial upload to WordPress Plugin Directory

== Upgrade Notice ==

Nothing to upgrade

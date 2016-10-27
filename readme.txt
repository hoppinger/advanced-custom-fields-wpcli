=== Advanced Custom Fields WP-CLI ===
Contributors: sebastiaandegeus
Tags: WP-CLI, Advanced, Custom Fields, acf
Requires at least: 4.5
Stable tag: 3.0
Tested up to:4.6.1
License: MIT

Manage Advanced Custom Fields through WP-CLI

== Description ==

**WP-CLI for Advanced Custom Fields**

This extension for Advanced Custom Fields that makes it possible to manage your field-groups through the console of wp-cli. The goal of this project is to make life easier for developers who working on Wordpress projects that use Advanced Custom Fields and love the WP-CLI command line tools.


**Reasons to start this project**

Advanced custom fields did not interface with WP-CLI
Sharing field-groups through XML or PHP code caused problems with differences between development, test, staging and production enviroments when shared with XML.
No direct SVN or GIT support without manually putting the exported PHP or XML into a versioned directory.
Naming convention for XML files was always the same, resulting in renaming hassle.
Only using the generated field-groups on runtime through PHP code in functions.php disables the editing mode (which is an awesome UI that we require). So importing should be possible.


== Installation ==

Requirements

* Advanced Custom Fields plugin
* `wp-cli` http://wp-cli.org/

1. install `wp-cli` http://wp-cli.org/
2. clone this repo as `advanced-custom-fields-wpcli` in your plugins directory
3. activate `advanced-custom-fields-wpcli` plugin through "wp plugin activate advanced-custom-fields-wpcli" (or activate in the plugin menu)
4. open a terminal and go to your wordpress directory
5. type `wp` (and see the `acf` commands if installed correctly)
6. type `wp acf` to test the `acf-wpcli` extension
7. start using the commands as explained in "Commands"

When the plugin is enabled, any exported field groups found on the filesystem in your registered paths will be added to Wordpress at runtime.

If you would like to disable this behaviour you can remove the `acf_wpcli_register_groups` action:
remove_action('plugins_loaded', 'acf_wpcli_register_groups');


== Changelog ==

= 2.0 =
* Removed uniqid feature (no longer needed).
* Bugfix: database fieldgroups are now prefered over exported fieldgroups.
* Cleaned up legacy xml import/export libraries.
* Add namespaces.
* Cleaned up all alternative notation uses.
* Multisite now correctly makes use of the global --url parameter.
* Added more comments and versioning.
* Removed dependency of wp-importer.
* Added support for composer installs.
* Dropped XML support, hello Json.


== Upgrade Notice ==

= 3.0 =
* Added Behat tests

# WP-CLI for Advanced Custom Fields

This extension for Advanced Custom Fields that makes it possible to manage your `field-groups` through the console of [wp-cli](http://http://wp-cli.org/). The goal of this project is to make life easier for developers who working on Wordpress projects that use Advanced Custom Fields and love the WP-CLI command line tools.


## Reasons to start this project

* Advanced custom fields did not interface with WP-CLI
* Sharing `field-groups` through XML or PHP code caused problems with differences between development, test, staging and production enviroments when shared with XML.
* No direct SVN or GIT support without manually putting the exported PHP or XML into a versioned directory.
* Naming convention for XML files was always the same, resulting in renaming hassle.
* Only using the generated `field-groups` on runtime through PHP code in `functions.php` disables the editing mode (which is an awesome UI that we require). So importing should be possible.

## Requirements

* Advanced Custom Fields plugin
* `wp-cli` http://wp-cli.org/


## Commands

This project adds the `acf` command to `wp-cli` with the following subcommands:
	
* `wp acf`: Default test and prints the help overview.
* `wp acf status`: provides a list of found `field-groups` in the current database of your Wordpress project.
* `wp acf export`:
  * creates a `field-group` directory into your current theme's directory.
  * creates a directory with the `field-group` name for each fieldgroup.
  * creates a `data.php` and `data.xml` for each `field-group` inside their respective folders.
  * creates a uniqid file that contains the id used by ACF to identify the field
  * use `wp acf export all` to export everything without prompting
		
* `wp acf import`: imports the XML(s) from `active-theme`/field-groups/{field-group_name}/data.xml`
  * When using wp acf import a selection menu apears to choose which field-group to import
		
* `wp acf clean`: cleans up the database from all found ACF post types and their coupled `post_meta` values, use this after you've edited the `field-groups` in the UI and used export to generate the new `data.php` files. Watch out: __cannot__ be undone.


## How to use

1. install `wp-cli` http://wp-cli.org/
2. clone this repo as `advanced-custom-fields-wpcli` in your plugins directory
3. activate `advanced-custom-fields-wpcli` plugin through "wp plugin activate advanced-custom-fields-wpcli" (or activate in the plugin menu)
4. open a terminal and go to your wordpress directory
5. type `wp` (and see the `acf` commands if installed correctly)
6. type `wp acf` to test the `acf-wpcli` extension
7. start using the commands as explained in "Commands"

When the plugin is enabled, any exported field groups found on the filesystem in your theme's `field-groups` folder will be added to Wordpress at runtime. 

## TODOs

* make `acf-wpcli` extension update-proof
* clean up code and refractor
* add more comments and versioning
* try to fix the `wp-importer` problem that caused the use of copied code from `wp-importer` plugin
* release this plugin as official WordPress plugin

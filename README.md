# WP-CLI for Advanced Custom Fields

This extension for Advanced Custom Fields that makes it possible to manage your `field_groups` through the console of [wp-cli](https://github.com/wp-cli/wp-cli). The goal of this project is to make life easier for developers who working on Wordpress projects that use Advanced Custom Fields.



## Reasons to start this project

* `wp-cli` did not interface with ACF.
* Sharing `field_groups` through XML or PHP code caused problems with differences between development, test, staging and live enviroments when shared with XML.
* No direct SVN or GIT support without manually putting the exported PHP or XML into a versioned folder.
* Naming convention for XML files was always the same, resulting in renaming hassle.
* Inconsitensy when selecting multiple `field_groups` by XML.
* Generated `field_groups` on runtime through PHP code in `functions.php` diables the editing mode (which is an awesome UI that we require)



### Requirements

* Advanced Custom Fields plugin (activated)
* `wp-cli` download from [GitHub](http://github.com/wp-cli/wp-cli/) or use the pear package



### Commands

This project adds the `acf` command to `wp-cli` with the following subcommands:
	
* `wp acf status`: provides a dumped array of found `field_groups` in the current database of your Wordpress project.
* `wp acf export`: 	
  * writes a `field_group` folder into your current theme's directory.
  * writes a folder with the `field_group` name for each `found_field` group.
  * writes a `data.php` and `data.xml` for each `field_group` inside their respective folders.
  * use `wp acf export all` to export everything without prompting
		
* `wp acf import`: imports the XML(s) from the path `field_groups/{blog_id}/{field_group_name}/data.xml`
	* `wp acf import all` imports all the found field-groups from their respective folders
	* `wp acf import field-group-name` imports only a single field group
		
* `wp acf clean`: cleans up the database from all found ACF post types and there coupled `post_meta` values, use this after you've edited the `field_groups` in the UI and used export to generate the new `data.php` files.
		
* `wp acf`: Default test and prints the help overview.



### How to use

1. install `wp-cli` (pear or download)
2. put the `advanced-custom-fields-wpcli` folder in `wp-content/plugins`
3. activate `advanced-custom-fields-wpcli` plugin through "wp plugin activate advanced-custom-fields-wpcli"
4. go the terminal and go to your wordpress folder
5. type `wp` (and see the wp-cli commands if installed correctly)
6. type `wp acf` to test the `acf-wpcli` extension
7. start using the commands as explained in "Commands"



### TODOs

* make `acf-wpcli` extension update-proof
* try to make `acf-wpcli` a real part of ACF through the plugin creator [Elliot Condon Advanced Custom Fields](http://www.advancedcustomfields.com)
* clean up code and refractor
* add more comments and versioning
* relocate the `field_groups` folder to something that makes a little more sense
* try to fix the `wp-importer` problem that caused the use of copied code from `wp-importer` plugin


### Versions


#### 0.3
* made multisite compatible for both console and cms part

#### 0.2

* Moved the extension out of advanced-custom-fields to it's own plugin folder to resolve update issues bound to happen
* Check to make sure the plugin admin code will only be ran when the ACF plugin is already loaded

#### 0.1

* Initial project start
* Added status, export, import and clean commands
* Used wordpress-importer classes directly in the project
* moved the data.php or use database check to advanced-custom-fields/wp-cli/run.php
* wrote first version of readme.txt

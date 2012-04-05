# WP-CLI for Advanced Custom Fields

This extension for Advanced Custom Fields makes it possible to manage your field_groups through the console of wp-cli. The goal of this project is to make life easier for developers who work together on Wordpress projects that use Advanced Custom Fields.



## Reasons to start this project

* There was no link to wp-cli for ACF
* Sharing field_groups through XML or by php code caused the following problems:
* prone for minor and major differences to sneak in between development, test, stage and live enviroments when sharing with XML.
* No direct SVN or Git support without manually putting the exported php or xml into a versioned folder
* Naming convention for xml files was always the same. Resulting in a hassle renaming to the version file.
* Inconsitensy when selecting multiple field_groups. Errors occured and is also prone for version problems.
* generated field_groups on runtime through php from functions.php prevented editing mode (the UI is so awesome, we want it!)



### Requirements
	Advanced Custom Fields plugin (activated)
	wp-cli: download from [GitHub](http://github.com/wp-cli/wp-cli/wp-cli) or use the pear package



### Commands

added the acf command to wp-cli with subcommands
	
	wp acf status: 	
		provides a dumped array of found field_groups in the current database of your Wordpress project.
		
	wp acf export: 	
		writes a field_group folder in httpdocs (or other root folder of your Wordpress installation).
		writes a folder with the field_group name for each found_field group.
		writes a data.php and data.xml for each field_group inside their respective folders.
		
	wp acf import: 	
		Imports the xml field_groups that are found in the field_groups/{field_group_name}/data.xml
		You can now edit the field_groups in /wp-admin/edit.php?post_type=acf (use the awesome UI).
		
	wp acf clean:	
	 	Cleans up the database from all found acf post types and there coupled post_meta values
		Use this after you've edited the field_groups in the UI and used export to generate the new data.php files
		
	wp acf			
		*Default test and help overview.



### How to use
1. Install wp-cli (pear or download)
2. put the advanced-custom-fields-wpcli folder in wp-content/plugins
3. activate advanced-custom-fields-wpcli plugin through "wp plugin activate advanced-custom-fields-wpcli"
4. go the terminal and go to your wordpress folder
5. type wp (and see the wp-cli commands if installed correctly)
6. type wp acf to test the acf-wpcli extension
7. Start using the commands as explained in "Commands"



### Feature requests, coming up soon
* make acf-wpcli extension update-proof
* try to make acf-wpcli a real part of ACF through the plugin creator [Elliot Condon Advanced Custom Fields](http://www.advancedcustomfields.com)
* clean up code and refractor
* add more comments and versioning
* relocate the field_groups folder to something that makes a little more sense
* try to fix the wp-importer problem that caused the use of copied code from wp-importer plugin



### Versions
**0.2**

* moved the extension out of advanced-custom-fields to it's own plugin folder to resolve update issues bound to happen
* check to make sure the plugin admin code will only be ran when the ACF plugin is already loaded

**0.1**

* Initial project start
* added status, export, import and clean commands
* Used wordpress-importer classes directly in the project
* moved the data.php or use database check to advanced-custom-fields/wp-cli/run.php
* wrote first version of readme.txt
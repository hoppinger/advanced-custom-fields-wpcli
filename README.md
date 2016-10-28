# WP-CLI for Advanced Custom Fields

Manage your ACF field groups on the command line with wp-cli. Import,export and clean your field groups to add them to git or any other vcs.

## Requirements
    A WordPress installation
    Advanced Custom Fields plugin or ACF5-PRO plugin
    wp-cli http://wp-cli.org/

<<<<<<< f258ebb58a188af921dc4253b80c5d7ea9775c0f
## Installation
    1) Git clone https://github.com/hoppinger/advanced-custom-fields-wpcli.git into wp-content/plugins folder
    2) Go to WordPress and activate the plugin
    3) Use wp acf command or wp help acf to use the functions
=======
  ## Reasons to start this project

  * Advanced custom fields did not interface with WP-CLI
  * Sharing `field-groups` through XML or PHP code caused problems with differences between development, test, staging and production enviroments when shared with XML.
  * No direct SVN or GIT support without manually putting the exported PHP or XML into a versioned directory.
  * Naming convention for XML files was always the same, resulting in renaming hassle.
  * Only using the generated `field-groups` on runtime through PHP code in `functions.php` disables the editing mode (which is an awesome UI that we require). So importing should be possible.

  ## Requirements

  * Advanced Custom Fields plugin
  * `wp-cli` http://wp-cli.org/


  ## How to install

  1. install `wp-cli` http://wp-cli.org/
  2. clone this repo as `advanced-custom-fields-wpcli` in your plugins directory
  3. activate `advanced-custom-fields-wpcli` plugin through "wp plugin activate advanced-custom-fields-wpcli" (or activate in the plugin menu)
  4. open a terminal and go to your wordpress directory
  5. type `wp` (and see the `acf` commands if installed correctly)
  6. type `wp acf` to test the `acf-wpcli` extension
  7. start using the commands as explained in "Commands"

  When the plugin is enabled, any exported field groups found on the filesystem in your registered paths will be added to Wordpress at runtime.

  If you would like to disable this behaviour you can remove the `acf_wpcli_register_groups` action:
  ```php
    remove_action('plugins_loaded', 'acf_wpcli_register_groups');
  ```

  ## Commands

  This project adds the `acf` command to `wp-cli` with the following subcommands:

  * `wp acf`: Default test and prints the help overview.
  * `wp acf status`: provides a list of found `field-groups` in the current database of your Wordpress project.
  * `wp acf export`:
  * creates a `field-group` directory into your current theme's directory.
  * creates a directory with the `field-group` name for each fieldgroup.
  * creates a `data.php` and `data.json` for each `field-group` inside their respective folders.
  * use `wp acf export all` to export everything without prompting
  * `wp acf import`: imports the XML(s) from `active-theme`/field-groups/{field-group_name}/data.xml`
  * When using wp acf import a selection menu apears to choose which field-group to import
  * `wp acf clean`: cleans up the database from all found ACF post types and their coupled `post_meta` values, use this after you've edited the `field-groups` in the UI and used export to generate the new `data.php` files. Watch out: __cannot__ be undone.

  ## Filters

  * acfwpcli_fieldgroup_paths
  	By default ACF-CLI will search 2 paths for field-groups. The active_theme and active_child_theme.
  	The acfwpcli_fieldgroup_paths gives you the ability to add more paths where ACF-CLI should load/export from/to.
  	Example:
  ```php
    add_filter( 'acfwpcli_fieldgroup_paths', 'add_plugin_path' );

  	public function add_plugin_path( $paths ) {
      $paths['my_plugin'] = MY_PLUGIN_ROOT . '/lib/field-groups/';
      return $paths;
    }
  ```
>>>>>>> readme centralisation wordpress txt and github md

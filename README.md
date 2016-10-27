#Advanced Custom Fields WP-CLI

Manage your ACF field groups on the command line with wp-cli. Import,export and clean your field groups to add them to git or any other vcs.

## Requirements
    A WordPress installation
    Advanced Custom Fields plugin or ACF5-PRO plugin
    wp-cli http://wp-cli.org/

## Installation
    1) Git clone https://github.com/hoppinger/advanced-custom-fields-wpcli.git into wp-content/plugins folder
    2) Go to WordPress and activate the plugin
    3) Use wp acf command or wp help acf to use the functions

## Commands:

**Import** : Import ACF field groups from local files to database

  * [--json_file=<json_file>]   : The path to the json file.
  * [--all]                     : Import all the fieldgroups


**Export** : Export ACF field groups to local files

  * [--group=<group>]           : The field group to export, can be used with "My Field Group" or "my-field-group".
  * [--export_path=<path>]      : The field groups directory path to export towards.
  * [--all]                     : Export all the fieldgroups.


**Clean** : Remove everything ACF from the database

  * [--network]                 : Clean the fieldgroups in all the sites in the network

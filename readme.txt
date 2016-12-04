=== Advanced Custom Fields WP-CLI ===
Contributors: sebastiaandegeus, marceldillen
Tags: WP-CLI, Advanced, Custom Fields, acf
Requires at least: 4.5
Stable tag: 3.0
Tested up to: 4.6.1
License: MIT

Manage Advanced Custom Fields through WP-CLI
=== WP-CLI for Advanced Custom Fields ===

= Description =

WP-CLI for Advanced Custom Fields helps you manage your field-groups through WP-CLI.
The reason we started this project is to make life easier for developers working on Wordpress projects using the Advanced Custom Fields Pro plugin.
Fields can now easily be imported, exported and shared over SVN, GIT or comparable systems.
== Installation ==

= Requirements =

* Advanced Custom Fields 5 Pro plugin
* `wp-cli` http://wp-cli.org/

= How to install =

Install WP-CLI as described on [http://wp-cli.org/](http://wp-cli.org/ "WP-CLI")

Using composer:
```
composer require wpackagist-plugin/advanced-custom-fields-wpcli
```

By GIT clone in plugins directory:
```
git clone https://github.com/hoppinger/advanced-custom-fields-wpcli.git
```

WordPress plugin installation:
Download zip and put the files in the plugins directory.

* Activate this plugin in the plugin menu or using:
```
wp plugin activate advanced-custom-fields-wpcli
```

Go the wordpress directory in your terminal and run:
```
wp acf
```
To see if everything is working correctly.

When the plugin is enabled, any exported field groups found on the filesystem in your registered paths will be added to Wordpress at runtime.
If you would like to disable this behaviour you can remove the `acf_wpcli_register_groups` action:
```
remove_action('plugins_loaded', 'acf_wpcli_register_groups');
```
== Filters ==

= acfwpcli_fieldgroup_paths =

The acfwpcli_fieldgroup_paths gives you the ability to add more paths where ACF-CLI should load/export from/to.
You should always add at least one path to this filter.

```
add_filter( 'acfwpcli_fieldgroup_paths', 'add_plugin_path' );

public function add_plugin_path( $paths ) {
  $paths['my_plugin'] = MY_PLUGIN_ROOT . '/lib/field-groups/';
  return $paths;
}
````
== Commands ==

This project adds the `acf` command to `wp-cli` with the following subcommands:

= Help =
```
wp acf
```
Prints the help overview and can be used as a default test to see if the plugin is working.


= Export =

Export a field-group to a json file in the directory set by a filter.
```
wp acf export
```

You want to export all field-groups all at once you can use:
```
wp acf export --all
```

= Import =
```
wp acf import
```
Import all or specific fields from a option menu,

= Clean =
```
wp acf clean
```
Delete all Advanced Custom Fields Records from the database.
Do this after you have edited fields-groups from the UI and exported the changes.
**Warning: This can not be undone, please use carefully**
== Upgrade Notice ==

= 3.0 =
* Make sure you import all your custom fields before updating.
* Make sure you are you using ACF5, ACF4 is not supported.
* Update the plugin
* Add the filter to your project (See Filters)
* Export you fields
* Remove unnecessary files like your old import directory, php files and json files.

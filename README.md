# WP-CLI for Advanced Custom Fields 


### Description 

WP-CLI for Advanced Custom Fields helps you manage your field-groups through WP-CLI.
The reason we started this project is to make life easier for developers working on Wordpress projects using the Advanced Custom Fields Pro plugin.
Fields can now easily be imported, exported and shared over SVN, GIT or comparable systems.

## Installation 


### Requirements 

* Advanced Custom Fields 5 Pro plugin
* `wp-cli` http://wp-cli.org/


### How to install 

Install WP-CLI as described on [http://wp-cli.org/](http://wp-cli.org/ "WP-CLI")

Using composer: (doesn't work for now until we have released the plugin on wordpress.org/plugins)
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

## Commands 

This project adds the `acf` command to `wp-cli` with the following subcommands:


### Help 
```
wp acf
```
Prints the help overview and can be used as a default test to see if the plugin is working.



### Export

Export a field-group to a json file in the directory set by a [filter](#filters).

```
wp acf export
```

For testing purposes, etc. you can also define a export directory explicitly without applying the filter by using the `--export_path` parameter.

```
wp acf export --export_path=acf-exports/
```

You want to export all field-groups all at once you can use:

```
wp acf export --all
```

### Import

```
wp acf import
```
Import all or specific fields from a option menu,


### Clean
```
wp acf clean
```
Delete all Advanced Custom Fields Records from the database.
Do this after you have edited fields-groups from the UI and exported the changes.
**Warning: This can not be undone, please use carefully**

## Filters


### acfwpcli_fieldgroup_paths

The acfwpcli_fieldgroup_paths gives you the ability to add more paths where ACF-CLI should load/export from/to.
You should **always add at least one path** to this filter.

```
add_filter( 'acfwpcli_fieldgroup_paths', 'add_plugin_path' );

public function add_plugin_path( $paths ) {
  $paths['my_plugin'] = MY_PLUGIN_ROOT . '/lib/field-groups/';
  return $paths;
}
````

## Unit testing

To test changes to the plugin you can use unit testing. Start by making sure all the necessary dependencies are installed, if not run:
```
composer install
```

You will need a new Wordpress installation to make sure the tests run
independent from your Wordpress installation and database. To create a wordpress installation for testing run the following command:
```
bash bin/test_wp_install.sh wordpress_test db_username db_password localhost latest
```

Where 'wordpress_test' is the name for the database used to run the tests. Make sure this database doesn't exist or can be deleted. When the database
already exists the script will ask you if the database can be deleted. 'Latest' can be changed if you want to test with a specific version of Wordpress, 3.6.2 for example.

After you installed you can start running tests using the follow command:
```
vendor/bin/behat
```

This will run all test. These tests include an import and export of all types of fields, cleaning, multiple fields in one field-group and tests for
the menu options. If you want to run one specific test you can do this by running:
```
vendor/bin/behat features/testname.feature
```

If you need a different test you can create your own by added it to the features in the features folder.

## Upgrade Notice 


### 3.0 
* Make sure you import all your custom fields before updating.
* Make sure you are you using ACF5, ACF4 is not supported.
* Update the plugin
* Add the filter to your project (see [Filters](#filters))
* Export you fields
* Remove unnecessary files like your old import directory, php files and json files.

## Changelog 


### 3.0 
* Bugfix: Import no longer created duplicates
* Add unit testing with behat and PHPUnit


### 2.0 
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

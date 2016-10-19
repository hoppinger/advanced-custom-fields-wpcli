Feature: Import and Export Text Field

  Scenario: Importing a text export
    Given a WP install
    When I run "php /private/tmp/wordpress/wp-cli.phar export"
    Then STDOUT should contain:
    """
    All done with export.
    """

  Scenario: Buying a single product over £10
    Given a WP install
    When I run "wp export"
    Then STDOUT should contain:
    """
    All done with export.
    """

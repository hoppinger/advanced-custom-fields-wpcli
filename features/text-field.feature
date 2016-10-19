Feature: Import and Export Text Field

  Scenario: Importing a text export
    Given a WP install
    When I run the wp-cli command "acf import --json_file=test_exports/text_group.json"
    Then STDOUT should contain:
    """
    All done with export.
    """

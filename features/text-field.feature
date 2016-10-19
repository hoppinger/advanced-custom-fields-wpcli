Feature: Import and Export Text Field

  Scenario: Importing a text export
    Given a WP install
    When I run the wp-cli command to import the file "namefield.json"
    Then the import result code should be 0
    And the import result should not be empty
    And the import result string should start with "Success:"

    When I run the wp-cli command to export custom field "mygroup"
    Then the export result code should be 0
    And the export result should not be empty
    And the export result string should start with "Success:"

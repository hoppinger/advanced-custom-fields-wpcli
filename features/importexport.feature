Feature: Import and Export Field Groups

  Scenario: Importing and exporting a field group with a text field
    Given a WP install
    When I run the wp-cli command to import the file "text-group.json"
    Then the import result code should be 0
    And the import result should not be empty
    And the import result string should start with "Success:"

    When I run the wp-cli command to export custom field "text-group"
    Then the export result code should be 0
    And the export result should not be empty
    And the export result string should start with "Success:"
    And the exported file should match the original import file

  Scenario: Importing and exporting a field group with a text area field
    Given a WP install
    When I run the wp-cli command to import the file "textarea-group.json"
    Then the import result code should be 0
    And the import result should not be empty
    And the import result string should start with "Success:"

    When I run the wp-cli command to export custom field "textarea-group"
    Then the export result code should be 0
    And the export result should not be empty
    And the export result string should start with "Success:"
    And the exported file should match the original import file

    Scenario: Importing and exporting a field group with a number field
      Given a WP install
      When I run the wp-cli command to import the file "number-group.json"
      Then the import result code should be 0
      And the import result should not be empty
      And the import result string should start with "Success:"

      When I run the wp-cli command to export custom field "number-group"
      Then the export result code should be 0
      And the export result should not be empty
      And the export result string should start with "Success:"
      And the exported file should match the original import file

    Scenario: Importing and exporting a field group with an email field
      Given a WP install
      When I run the wp-cli command to import the file "email-group.json"
      Then the import result code should be 0
      And the import result should not be empty
      And the import result string should start with "Success:"

      When I run the wp-cli command to export custom field "email-group"
      Then the export result code should be 0
      And the export result should not be empty
      And the export result string should start with "Success:"
      And the exported file should match the original import file

    Scenario: Importing and exporting a field group with an URL field
      Given a WP install
      When I run the wp-cli command to import the file "url-group.json"
      Then the import result code should be 0
      And the import result should not be empty
      And the import result string should start with "Success:"

      When I run the wp-cli command to export custom field "url-group"
      Then the export result code should be 0
      And the export result should not be empty
      And the export result string should start with "Success:"
      And the exported file should match the original import file

    Scenario: Importing and exporting a field group with a password field
      Given a WP install
      When I run the wp-cli command to import the file "password-group.json"
      Then the import result code should be 0
      And the import result should not be empty
      And the import result string should start with "Success:"

      When I run the wp-cli command to export custom field "password-group"
      Then the export result code should be 0
      And the export result should not be empty
      And the export result string should start with "Success:"
      And the exported file should match the original import file

    Scenario: Importing and exporting a field group with a wysiwyg field
      Given a WP install
      When I run the wp-cli command to import the file "wysiwyg-group.json"
      Then the import result code should be 0
      And the import result should not be empty
      And the import result string should start with "Success:"

      When I run the wp-cli command to export custom field "wysiwyg-group"
      Then the export result code should be 0
      And the export result should not be empty
      And the export result string should start with "Success:"
      And the exported file should match the original import file

    Scenario: Importing and exporting a field group with a embed field
      Given a WP install
      When I run the wp-cli command to import the file "embed-group.json"
      Then the import result code should be 0
      And the import result should not be empty
      And the import result string should start with "Success:"

      When I run the wp-cli command to export custom field "embed-group"
      Then the export result code should be 0
      And the export result should not be empty
      And the export result string should start with "Success:"
      And the exported file should match the original import file

    Scenario: Importing and exporting a field group with an image field
      Given a WP install
      When I run the wp-cli command to import the file "image-group.json"
      Then the import result code should be 0
      And the import result should not be empty
      And the import result string should start with "Success:"

      When I run the wp-cli command to export custom field "image-group"
      Then the export result code should be 0
      And the export result should not be empty
      And the export result string should start with "Success:"
      And the exported file should match the original import file

    Scenario: Importing and exporting a field group with a file field
      Given a WP install
      When I run the wp-cli command to import the file "file-group.json"
      Then the import result code should be 0
      And the import result should not be empty
      And the import result string should start with "Success:"

      When I run the wp-cli command to export custom field "file-group"
      Then the export result code should be 0
      And the export result should not be empty
      And the export result string should start with "Success:"
      And the exported file should match the original import file

    Scenario: Importing and exporting a field group with a gallery field
      Given a WP install
      When I run the wp-cli command to import the file "gallery-group.json"
      Then the import result code should be 0
      And the import result should not be empty
      And the import result string should start with "Success:"

      When I run the wp-cli command to export custom field "gallery-group"
      Then the export result code should be 0
      And the export result should not be empty
      And the export result string should start with "Success:"
      And the exported file should match the original import file

    Scenario: Importing and exporting a field group with a select field
      Given a WP install
      When I run the wp-cli command to import the file "select-group.json"
      Then the import result code should be 0
      And the import result should not be empty
      And the import result string should start with "Success:"

      When I run the wp-cli command to export custom field "select-group"
      Then the export result code should be 0
      And the export result should not be empty
      And the export result string should start with "Success:"
      And the exported file should match the original import file

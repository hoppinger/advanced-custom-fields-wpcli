Feature: Import and Export Radio Groups

    Scenario: Importing and exporting a field group with a radio field
        Given a WP install
        Then the exit code should be 0
        When I run the command "acf import --json_file='features/bootstrap/test_imports/radio-group.json'"
        Then the exit code should be 0
        And the result should not be empty
        And the result string should start with "Success:"
        When I run the command "acf export --export_path='features/bootstrap/test_exports/' radio-group"
        Then the exit code should be 0
        And the result should not be empty
        And the result string should start with "Success:"
        And the imported and exported "radio-group.json" files should match

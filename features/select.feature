Feature: Import and Export Select Groups

    Scenario: Importing and exporting a field group with a select field
        Given a WP install
        Then the exit code should be 0
        When I run the command "acf import --json_file='features/bootstrap/test_imports/select-group.json'"
        Then the exit code should be 0
        And the result should not be empty
        And the result string should start with "Success:"
        When I run the command "acf export --field_group='select-group' --export_path='features/bootstrap/test_exports/'"
        Then the exit code should be 0
        And the result should not be empty
        And the result string should start with "Success:"
        And the imported and exported "select-group.json" files should match

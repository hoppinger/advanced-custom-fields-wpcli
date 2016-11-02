Feature: Import and Export post Groups

    Scenario: Importing and exporting a field group with a post field
        Given a WP install
        Then the exit code should be 0
        When I run the command "acf import --json_file='features/bootstrap/test_imports/post-group.json'"
        Then the exit code should be 0
        And the result should not be empty
        And the result string should start with "Success:"
        When I run the command "acf export --field_group='post-group' --export_path='features/bootstrap/test_exports/'"
        Then the exit code should be 0
        And the result should not be empty
        And the result string should start with "Success:"
        And the imported and exported "post-group.json" files should match

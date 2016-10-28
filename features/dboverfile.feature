Feature: DB is prefered over files

    Scenario: Export a field group and see if changes to the db over prefered over export files
        Given a WP install
        And a "number" feature
        When I run the command "acf export --field_group='number-group' --export_path='features/bootstrap/test_exports/'"
        And I remove the fields from "number-group.json"
        Then the "number-group" should not have been added to the local groups

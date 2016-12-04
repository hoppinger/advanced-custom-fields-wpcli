Feature: ACF WP-CLI Menu

    Scenario: Export all fields using the menu
        Given a WP install
        Then the exit code should be 0
        And acf fields in the database
        When I run the command "acf export" and answer "1"
        Then the exit code should be 0
        And the result should not be empty
        And the result string should start with "Success:"

    Scenario: Export specific field using the menu
        Given a WP install
        Then the exit code should be 0
        And acf fields in the database
        When I run the command "acf export" and answer "2"
        Then the exit code should be 0
        And the result should not be empty
        And the result string should start with "Success:"

    Scenario: Import all fields using the menu
        Given a WP install
        Then the exit code should be 0
        And acf fields in the database
        When I run the command "acf import" and answer "1"
        Then the exit code should be 0
        And the result should not be empty
        And the result string should start with "Success:"

    Scenario: Import specific field using the menu
        Given a WP install
        Then the exit code should be 0
        And acf fields in the database
        When I run the command "acf import" and answer "2"
        Then the exit code should be 0
        And the result should not be empty
        And the result string should start with "Success:"

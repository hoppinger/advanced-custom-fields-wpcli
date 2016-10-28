Feature: ACF WP-CLI Clean Command

    Scenario: Clean a database with acf fields
        Given a WP install
        Then the exit code should be 0
        And acf fields in the database
        When I run the command "acf clean"
        Then the exit code should be 0
        And the result should not be empty
        And the result string should start with "Success:"
        When I run the command "acf clean"
        Then the exit code should be 0
        And the result should be empty

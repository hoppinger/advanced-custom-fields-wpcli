Feature: Clean all ACF fields

    Scenario: Run acf clean with acf records in the database
      Given a WP install
      And a database with custom fields
      When I run the wp-cli command clean
      Then I expect the result code to be 0
      Then I expect the database to no longer contain ACF records

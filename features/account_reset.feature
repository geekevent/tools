@entity::password_reset
Feature: Account
  Main scenario for account

  Scenario: password reset
    When modules are inserted
    And An account created
    And a token for "account_reset_token"
    When I go to the reset password route with "password_reset" as content
    Then I found an acount with "bla" as password
@entity::password_reset
Feature: Account
  Main scenario for account

  Scenario: password reset
    When modules are inserted
    And An account created
    And a token for "account_reset_token"
    When I go to the "account_reset" route with "post" as method and "password_reset" as content
    Then I found an account with "%X12345678" as password
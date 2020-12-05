@entity::my_account
@logged
Feature: Account
  Main scenario for account

  Scenario: account modification
    When modules are inserted
    And a token for "account_update_token"
    When I go to the "/admin/accounts/me" path with "post" as method and "account_modification" as content
    Then I found the account with id 1 and "%ABC12345678" as password

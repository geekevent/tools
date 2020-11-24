@entity::account
@logged
Feature: Account
  Main scenario for account


  Scenario: account creation
    When modules are inserted
    And a token for "account_creation_token"
    And I send a "POST" request to "/admin/accounts" with body:
      """
      {
        "account" : {
          "givenName": "foo",
          "familyName": "bar",
          "login": "foo@bar.fr"
        }
      }
    """
    Then I found a "account" with "foo@bar.fr" as login

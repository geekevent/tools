@entity::role
Feature: Role
  Main scenario for role CRUD


  Scenario: Role creation
    When a token for "role_token"
    And I send a "POST" request to "/roles" with body:
      """
      {
        "role" : {
          "name": "foo"
        }
      }
    """
    Then I found a "role" with "foo" as name

  Scenario: Role update creation
    When a token for "role_token"
    And I send a "POST" request to "/roles" with body:
      """
      {
        "role" : {
          "name": "foo"
        }
      }
    """
    When a token for "role_token"
    Then I send a "POST" request to "/roles/1" with body:
    """
      {
        "role" : {
          "name": "bar"
        }
      }
    """
    Then I found a "role" with "bar" as name

  Scenario: Role update creation
    When a token for "role_token"
    And I send a "POST" request to "/roles" with body:
      """
      {
        "role" : {
          "name": "foo"
        }
      }
    """
    Then I send a "GET" request to "/roles/1/delete"
    Then I didn't found a "role" with "foo" as name
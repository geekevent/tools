@entity::role
Feature: Role
  Add Module to Role


  Scenario: Role creation
    When a token for "role_token"
    And I send a "POST" request to "/roles/" with body:
      """
      {
        "role" : {
          "name": "foo"
        }
      }
    """
    When a token for "role_module_token"
    When modules are inserted
    And I send a "POST" request to "/roles/modules" with body:
      """
      {
        "roles": {
          "1": [1]
        }
      }
    """
    Then I found in "role" with 1 as id and with the module with id 1
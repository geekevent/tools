@entity::module
@logged
Feature: Module
  Main scenario for module CRUD

  Scenario: module creation
    When modules are inserted
    And a token for "module_token"
    And I send a "POST" request to "/admin/modules/1" with body:
      """
      {
        "module" : {
          "name": "foo",
          "identifier": "bar"
        }
      }
    """
    Then I found a "module" with "foo" as name
    And the "module" with id 1 as "ROLE" as identifier

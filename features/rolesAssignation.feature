@entity::role
@logged
Feature: Role
  Add Module to Role


  Scenario: Role creation
    When a token for "role_token"
    When I go to the "/admin/roles" path with "POST" as method and "role_create" as content
    When a token for "role_module_token"
    When modules are inserted
    When I go to the "/admin/roles/modules" path with "POST" as method and "module_role" as content
    Then I found in "role" with 1 as id and with the module with id 1
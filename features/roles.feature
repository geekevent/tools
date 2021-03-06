@entity::role
@logged
Feature: Role
  Main scenario for role CRUD


  Scenario: Role creation
    When a token for "role_token"
    When I go to the "/admin/roles" path with "POST" as method and "role_create" as content
    Then I found a "role" with "foo" as name

  Scenario: Role update creation
    When a token for "role_token"
    When I go to the "/admin/roles" path with "POST" as method and "role_create" as content
    When a token for "role_token"
    When I go to the "/admin/roles/2" path with "POST" as method and "role_update" as content
    Then I found a "role" with "bar" as name

  Scenario: Role delete
    When a token for "role_token"
    When I go to the "/admin/roles" path with "POST" as method and "role_create" as content
    Then I send a "GET" request to "/admin/roles/2/delete"
    Then I didn't found a "role" with "foo" as name
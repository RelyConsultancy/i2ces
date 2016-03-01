Feature: Api me endpoint
  In order to get current user data
  As a customer
  I need to be able to get details using a rest call

  Scenario: Successfully access api endpoint
    Given Login as an existing "usr" user and "pass" password
    When I access "api/me" endpoint
    Then I should get the response status "200"

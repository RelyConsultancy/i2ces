Feature: Users can access the system
  As an administrator
  I want to authenticate
  So that I can verify system is functioning properly

  Scenario: Administrator is not authenticated
    Given I am on homepage
    When I enter valid credentials
    Then I am taken to the dashboard

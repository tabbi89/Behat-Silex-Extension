Feature: Rebooting application
  In order to use fresh application
  As a Silex app tester
  I want to have rebooted application after each scenario

  Tested scenarios should not depend on each other, so after each scenario we should have fresh application

  Scenario: Setup some basic variables
    Given I setup session variable "test" with value "test_value"
    And I setup some application variable "test2" with value "test_value"

  Scenario: Checks if variables are not set
    When I get session variable "test" I should get NULL
    And application variable "test2" should not be set
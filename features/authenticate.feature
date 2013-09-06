# features/authenticate.feature
Feature: authenticate
  In order to get access to Bright
  As a Bright Administrator
  I need to be logged in
  
Scenario: Login as an existing user
	Given I am not logged in
	When I login with "vz@uitloper.nu" and "testpw"
	Then I should be logged in
	
Scenario: Login as an unexisting user
	Given I am not logged in
	When I login with "vz@vollezalen.nl" and "INVALIDPASSWORD"
	Then I should not be logged in
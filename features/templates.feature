# features/templates.feature
Feature: templates
  In order to see all the templates
  As a Bright Administrator
  I need to be able to list all available templates
  
  
Scenario: List 2 templates
  Given I am logged in as Administrator
  And the following templates exist:
  | id | label    |
  | 1  | homepage |
  | 2  | text     |
  And I have a template named "homepage"
  And I have a template named "text"
  When I run "core\content\Templates\getTemplates()"
  Then the result should be an array of 2 \bright\core\model\vo\Template objects
  
Scenario: Create a template
  Given I am logged in as Administrator
  And the following templates exist:
  | id | label    |
  | 1  | homepage |
  | 2  | text     |
  And I create a template named "page"
  When I run "core\content\Templates\getTemplates()"
  Then the result should be an array of 3 \bright\core\model\vo\Template objects
  
Scenario: Delete a template
  Given I am logged in as Administrator
  And there is a template called "homepage"
  And I run "core\content\Templates\deleteTemplate()" with 
  """
  1
  page  
  """
  When I run "core\content\Templates\getTemplates()"
  Then the result should be an array of 2 \bright\core\model\vo\Template objects
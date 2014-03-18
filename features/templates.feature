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
  When I run "core\factories\TemplateFactory\getTemplates()"
  Then the result should be an array of 2 \bright\core\model\vo\Template objects
  
Scenario: Create a template
  Given I am logged in as Administrator
  And the following templates exist:
  | id | label    |
  | 1  | homepage |
  | 2  | text     |
  And I create a template named "page"
  When I run "core\factories\TemplateFactory\getTemplates()"
  Then the result should be an array of 3 \bright\core\model\vo\Template objects
  
Scenario: Delete a template
  Given I am logged in as Administrator
  And the following templates exist:
  | id | label    |
  | 1  | homepage |
  | 2  | text     |
  | 3  | page     |
  And I run "core\factories\TemplateFactory\deleteTemplateByLabel()" with 
  """
  page  
  """
  Then the result should be "1"
  When I run "core\factories\TemplateFactory\getTemplates()"
  Then the result should be an array of 2 \bright\core\model\vo\Template objects
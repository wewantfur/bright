# features/pages.feature
Feature: pages
  In order to see something, i need some content
  
Scenario: List all pages
  Given I am logged in as Administrator
  When I run "core\content\Pages\getPages()"
  Then the result should be an array of 6 \bright\core\model\vo\Page objects

Scenario: Get a page
  Given I am logged in as Administrator
  When I run "core\factories\PageFactory\GetContentByPageId()" with
  """
  1
  """
  Then the result should be a "\bright\core\model\vo\Page"

#Scenario: Edit a page
#  Given I am logged in as Administrator
#  When I run "core\content\Pages\getPage()"
#  Then I should be able to edit the page
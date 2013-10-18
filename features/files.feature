# features/files.feature
Feature: files
  Handles all the file management
  
Scenario: List all folders
  Given I am logged in as Administrator
  When I run "core\files\Files\getFolders()"
  Then the result should be an array of 1 \bright\core\model\vo\Folder objects	
  
Scenario: List all files
  Given I am logged in as Administrator
  When I run "core\files\Files\getFiles()" with
  """
  /
  """
  Then the result should be an array of 4 \bright\core\model\vo\File objects	
  
Scenario: Delete a file 
  Given I am logged in as Administrator
  When I run "core\files\Files\deleteFile()" with
  """
  1280.jpg
  /
  """
  Then the result should be an array of 3 \bright\core\model\vo\File objects	
  
Scenario: Create a folder 
  Given I am logged in as Administrator
  When I run "core\files\Files\setFolder()" with
  """
  amsterdam
  /
  """
  Then the result should be an array of 3 \bright\core\model\vo\Folder objects	
  
  
Scenario: Delete a folder 
  Given I am logged in as Administrator
  When I run "core\files\Files\setFolder()" with
  """
  amsterdam
  /
  """
  Then the result should be an array of 3 \bright\core\model\vo\Folder objects
  When I run "core\files\Files\deleteFolder()" with
  """
  amsterdam
  /
  """
  Then the result should be an array of 2 \bright\core\model\vo\Folder objects
  
  
Scenario: Move a file 
  Given I am logged in as Administrator
  When I run "core\files\Files\moveFile()" with
  """
  1280.jpg 
  /
  groningen
  """
  Then the result should be an array of 3 \bright\core\model\vo\File objects
  When I run "core\files\Files\getFiles()" with
  """
  groningen
  """
  Then the result should be an array of 2 \bright\core\model\vo\File objects
  
  
Scenario: Upload a file
  Given I am logged in as Administrator
  When I run "core\files\Files\setFile()"
  Then the result should be an array of 5 \bright\core\model\vo\File objects
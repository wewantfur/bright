<?php

use bright\core\model\vo\TemplateField;

use bright\core\model\vo\Template;
use bright\core\content\Templates;

use bright\core\exceptions\AuthException;

use bright\core\auth\Authorization;

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;

use Behat\Behat\Context\Step;

use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;



/**
 * Features context.
 */
class TemplateContext extends BehatContext
{
	
	private $result;
	
    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param array $parameters context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
        // Initialize your context here
        unset($_SESSION['bright']);
        $this -> result = null;
    }
    
    /**
     * @Given /^I have a template named "([^"]*)"$/
     */
    public function iHaveATemplateNamed($arg1)
    {
    	$tpl = new Templates();
    	return $tpl -> getTemplateByLabel($arg1);
    }
    
	/**
     * @Given /^the following templates exist:$/
     */
    public function theFollowingTemplatesExist(TableNode $table)
    {
	    $hash = $table->getHash();
	    $tpls = array();
	    foreach ($hash as $row) {
	    	$tpl = new Template();
	    	$tpl -> id = (int)$row['id'];
	    	$tpl -> label = $row['label'];
	    	$tpls[] = $tpl;
	    }
	    
	    return $tpls;
    }
    
    /**
     * @Given /^there is a template called "([^"]*)"$/
     */
    public function thereIsATemplateCalled($arg1)
    {
    	Templates::getTemplateByLabel($arg1);
    }
    
    
    /**
     * @Given /^I create a template named "([^"]*)"$/
     */
    public function iCreateATemplateNamed($arg1)
    {
    	$t = new Template();
    	$t -> label = $arg1;
    	$t -> displaylabel = ucfirst($arg1);
    	$t -> icon = 'page_white_text';
    	$t -> enabled = true;
    	$t -> type = Templates::TYPE_PAGE;
    	
    	$tf = new TemplateField();
    	$tf -> label = 'header';
    	$tf -> displaylabel = 'Header';
    	$tf -> fieldtype = 'string';
    	$t -> fields[] = $tf;
    	
    	$tf = new TemplateField();
    	$tf -> label = 'body';
    	$tf -> displaylabel = 'Body';
    	$tf -> fieldtype = 'string';
    	$t -> fields[] = $tf;
    	
    	$tf = new TemplateField();
    	$tf -> label = 'footer';
    	$tf -> displaylabel = 'Footer';
    	$tf -> fieldtype = 'string';
    	$t -> fields[] = $tf;
    	
    	Templates::setTemplate($t);
    }
    
    
}

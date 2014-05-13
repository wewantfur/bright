<?php

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

//
// Require 3rd-party libraries here:
//
require_once 'public_html/bright/core/Bootstrap.php';
require_once 'PHPUnit/Autoload.php';
require_once 'PHPUnit/Framework/Assert/Functions.php';


/**
 * Features context.
 */
class FeatureContext extends BehatContext
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
        $this->useContext('templatecontext', new TemplateContext($parameters));
        $this->useContext('filescontext', new FilesContext($parameters));
        $this->useContext('pagecontext', new PageContext($parameters));
    }
    
    
    /**
     * @Given /^I am on "([^"]*)"$/
     */
    public function iAmOn($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Given /^I am not logged in$/
     */
    public function iAmNotLoggedIn()
    {
        $auth = new Authorization();
        if(!$auth -> IsBEAuth())
        	return true;
        
        throw new AuthException("");
    }

    /**
     * @When /^I login with "([^"]*)" and "([^"]*)"$/
     */
    public function iLoginWithAnd($arg1, $arg2) {
    	$auth = new Authorization();
    	try {
    		$auth -> AuthBE($arg1, $arg2);
    	} catch(AuthException $ex) {
    		// Ignore auth exception
    	}
    }

    /**
     * @Then /^I should be logged in$/
     */
    public function iShouldBeLoggedIn()
    {    	
    	if(!Authorization::IsBEAuth())
    		throw new AuthException("no user logged in", AuthException::NO_USER);
    }
    
    /**
     * @Then /^I should not be logged in$/
     */
    public function iShouldNotBeLoggedIn()
    {
    	if(Authorization::IsBEAuth())
    		throw new \Exception("no user should be logged in");
    }
    
    
    
    
    /**
     * @Given /^I am logged in as Administrator$/
     */
    public function iAmLoggedInAsAdministrator()
    {
//     	return array(
//     			new Step\When('I am on "/bright/cms/login.php"'),
//     			new Step\When('I fill in "email" with "vz@uitloper.nu"'),
//     			new Step\When('I fill in "password" with "123456"'),
//     			new Step\When('I press "Login"'),
//     	);
    	$auth = new Authorization();
    	$auth -> AuthBE('ids@wewantfur.com', '7011845');
        //throw new PendingException();
    }

    /**
     * @Then /^I should get:$/
     */
    public function iShouldGet(PyStringNode $string)
    {
        throw new PendingException();
    }
    
    /**
     * @Then /^the result should be an array of (\d+) ([^\s]*) objects$/
     */
    public function shouldBeAndArrayOfN($num, $type)
    {
    	$num = (int)$num;
    	assertCount($num, $this->result);
    	assertContainsOnlyInstancesOf($type,$this->result);
    	$res = array();
    	foreach($this -> result as $item) {
    		if(!is_scalar($item)) {
    			$res[] = (string) $item;
    		} else {
    			$res[] = $item;
    		}
    	}
    	error_log(var_export($res, true));
    }
    
    /**
     * @Then /^the result should be "([^"]*)"$/
     */
    public function theResultShouldBe($arg1)
    {
    	assertEquals($arg1, $this -> result);
    }

    /**
     * @Then /^the result should be a "([^"]*)"$/
     */
    public function theResultShouldBeA($arg1)
    {
        assertInstanceOf($arg1, $this -> result);
    }




    /**
     * @When /^I run "([^"]*)"$/
     */
    public function iRun($arg1)
    {
    	return $this -> _run($arg1);
    }
    
    /**
     * @Given /^I run "([^"]*)" with$/
     */
    public function iRunWith($arg1, PyStringNode $string)
    {
    	$args = explode("\n",$string);
    	return $this -> _run($arg1, $args);
    }
    
    
    private function _run($arg1, $args = null) {
    	$a = explode('\\', $arg1);
    	$m = array_pop($a);
    	$cname = '\bright\\' . join('\\', $a);
    	if(!class_exists($cname,true))
    		throw new Exception("Class $cname not found");
    	
    	$cls = new $cname();
    	
    	$m = str_replace('()', '', $m);
    	
    	if(!method_exists($cls, $m))
    		throw new Exception("Method $m not found on $cname");
    	
    	if($args) {
	    	$this -> result = call_user_func_array(array($cls, $m), $args);
    		
    	} else {
	    	$this -> result = call_user_func(array($cls, $m));
    		
    	}
    	
    	
    	return $this -> result;
    }
    
    
}

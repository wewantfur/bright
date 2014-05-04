<?php
namespace bright\core\plugins;

class PluginLocator {
	/**
	 * All services.
	 *
	 * @var array
	 */
	private $services;

	/**
	 * The services which have an instance.
	 *
	 * @var array
	 */
	private $instantiated;


	public function __construct() {
		$this -> services = array();
		$this -> instantiated = array();
	}

	/**
	 * Registers a service with specific interface.
	 *
	 * @param string        $name
	 * @param string|object $service The fully qualified class name or an instance of the class
	 */
	public function add($pluginname, $service) {

		if(is_object($service))
			$this->instantiated[$pluginname] = $service;

		$this->services[$pluginname] = (is_object($service) ? get_class($service) : $service);
	}

	/**
	 * Checks if a service is registered.
	 *
	 * @param string $pluginname
	 *
	 * @return bool
	 */
	public function has($pluginname)
	{
		return (isset($this->services[$pluginname]) || isset($this->instantiated[$pluginname]));
	}

	/**
	 * Gets the service registered for the interface.
	 *
	 * @param string $pluginname
	 *
	 * @return \bright\core\interfaces\IPlugin
	 */
	public function get($pluginname)
	{
		// Retrieves the instance if it exists and it is shared
		if(isset($this->instantiated[$pluginname]))
			return $this->instantiated[$pluginname];

		// otherwise gets the service registered.
		$service = $this->services[$pluginname];

		// Creates the service object
		$object = new $service();

		return $object;
	}
}
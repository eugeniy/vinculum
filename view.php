<?php
/**
 * A simple view class for the MVC pattern.
 * 
 * @author Eugeniy Kalinin
 * @license http://pandabytes.info/license/
 * @link http://pandabytes.info/vinculum/
 *
 */
class View {
	
	protected $fileName;
	protected $data = array();

	/**
	 * Constructor.
	 * @param String $fileName
	 * @param Array $data
	 * @return void
	 */
	public function __construct($fileName = null, $data = null)
	{
		$this->SetFileName($fileName);
		$this->SetDataArray($data);
	}

	
	/**
	 * Factory method.
	 * Creates and returns an instance of the class.
	 * @example echo View::Factory("checkout.php");
	 * @param String $fileName
	 * @param Array $data
	 * @return Class instance
	 */
	public static function Factory($fileName = null, $data = null)
	{
		return new View($fileName, $data);
	}
	
	
	/**
	 * Outputs the template onto the screen.
	 * Variables set up to this point are passed to the template.
	 * @return string template data
	 */
	public function Render($fileName = null)
	{
		// Allow dynamic file name changes
		if ($fileName !== null)
			$this->SetFileName($fileName);
		
		// Start capturing the output
		ob_start();
		
		if (is_string($this->fileName) AND is_readable($this->fileName))
		{
			// Import variables into the namespace
			extract($this->data, EXTR_SKIP);
			// Include the view, allow access to class instance
			include $this->fileName;
		}
		// Dump the buffer and return the output
		return ob_get_clean();
	}
	

	/**
	 * File name setter.
	 * @param string $fileName
	 * @return Current class instance
	 */
	public function SetFileName($fileName)
	{
		$this->fileName = $fileName;
		return $this;
	}
	
	
	/**
	 * File name getter.
	 * @return string $this->fileName
	 */
	public function GetFileName()
	{
		return $this->fileName;
	}


	/**
	 * Set the variable for use in the template.
	 * @param string $key - variable name
	 * @param $value - variable value
	 * @return Current class instance
	 */
	public function SetVariable($key, $value)
	{
		$this->data[$key] = $value;
		return $this;
	}
	
	
	/**
	 * Get the variable to be used in the template.
	 * @param string $key - variable name
	 * @return Value stored under the given variable name or NULL if not found.
	 */
	public function GetVariable($key)
	{
		if (isset($this->data[$key]))
			return $this->data[$key];
		return null;
	}
	
	
	/**
	 * Sets variables using an associative array.
	 * @param Array $data
	 * @return Current class instance
	 */
	public function SetDataArray($data)
	{
		if (is_array($data))
			$this->data = array_merge($this->data, $data);
		return $this;
	}

	
	/**
	 * Data array getter.
	 * @return Array
	 */
	public function GetDataArray()
	{
		return $this->data;
	}
	
	
	/**
	 * Magic method to set unknown properties.
	 * It can be used to pass variables to the template.
	 * @example $view->Message = "Hello, World!";
	 */
	public function __set($key, $value)
	{
		$this->SetVariable($key, $value);
	}

	
	/**
	 * Magic method to get unknown properties.
	 * It can be used to retrieve template variables.
	 * @example echo $view->Message;
	 */
	public function __get($key)
	{
		return $this->GetVariable($key);
	}
	
	
	/**
	 * Magic method to automatically render when instance is used as a string.
	 * @example echo $view;
	 */
	public function __toString()
	{
		return $this->Render();
	}
	
}
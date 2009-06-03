<?php
/**
 * A simple view class for the MVC pattern.
 * 
 * @author Eugeniy Kalinin
 * @license http://pandabytes.info/license/
 * @link http://pandabytes.info/vinculum/
 */
class View {
	
	protected static $directory = './views';
	protected $fileName = null;
	protected $data = array();

	/**
	 * Set the view and load view data.
	 * @param  string  view file name
	 * @param  array   array of variables
	 * @return void
	 */
	public function __construct($fileName = null, $data = null)
	{
		if ( ! empty($fileName))
			$this->SetFileName($fileName);
		
		if (is_array($data) AND ! empty($data))
			$this->Set($data);
	}

	/**
	 * Create a new view.
	 * @example echo View::Factory("checkout.php");
	 * @param   string  view file name
	 * @param   array   array of variables
	 * @return  object  class instance
	 */
	public static function Factory($fileName = null, $data = null)
	{
		return new View($fileName, $data);
	}
	
	/**
	 * Render the view.
	 * @return  string  view data
	 */
	public function Render()
	{
		// Start capturing the output
		ob_start();
		
		if ( $this->GetFilePath() )
		{
			// Import variables into the namespace
			extract($this->data, EXTR_SKIP);
			// Include the view, allow access to class instance
			include $this->GetFilePath();
		}

		// Dump the buffer and return the output
		return ob_get_clean();
	}
	
	/**
	 * Set the path to directory containing view files.
	 * @example View::SetDirectory("templates");
	 * @param   string  directory path
	 * @return  void
	 */
	public static function SetDirectory($directory)
	{
		self::$directory = realpath($directory);
	}
	
	/**
	 * Get a full path to the view file.
	 * @return  string  view file path
	 * @return  bool    FALSE if the file is not found or unreadable
	 */
	public function GetFilePath()
	{
		$path = self::$directory.'/'.$this->fileName;
		
		if (is_file($path) AND is_readable($path))
			return $path;
		
		return false;
	}
	
	/**
	 * Set a view file name.
	 * @param  string  file name
	 * @return object  current class instance
	 */
	public function SetFileName($fileName)
	{
		$this->fileName = $fileName;
		return $this;
	}
	
	/**
	 * Set a view variable.
	 * @example $view->Set('pandaName', 'Bobby');
	 * @example $view->Set(array('title'=>'Panda World', 'year'=>2009));
	 * @param   string|array  name of a variable or an array of variables
	 * @param   mixed         value for a single variable
	 * @return  object        current class instance
	 */
	public function Set($key, $value = null)
	{
		if (is_array($key))
			$this->data = array_merge($this->data, $key);
		
		else $this->__set($key, $value);
		
		return $this;
	}
	
	/**
	 * Magic method to set a view variable.
	 * @example $view->Message = "Hello, World!";
	 * @param   string  variable key
	 * @param   mixed   variable value
	 * @return  void
	 */
	public function __set($key, $value)
	{
		$this->data[$key] = $value;
	}

	/**
	 * Magic method to get a view variable.
	 * @example echo $view->Message;
	 * @param   string  variable key
	 * @return  mixed   variable value if the key is found
	 * @return  bool    FALSE if the key is not found
	 */
	public function __get($key)
	{
		if (isset($this->data[$key]))
			return $this->data[$key];
		return false;
	}
	
	/**
	 * Magic method to convert a class instance into a string.
	 * @example echo $view;
	 * @return  string
	 */
	public function __toString()
	{
		return (string) $this->Render();
	}
}
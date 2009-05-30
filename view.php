<?php
/**
 * 
 * A simple view class for the MVC pattern.
 * 
 * @author Eugeniy Kalinin
 *
 */
class View {
	
	private $fileName;
	private $data;

	/**
	 * Constructor.
	 * @param $fileName
	 * @param $data
	 * @return unknown_type
	 */
	function __construct($fileName, $data = null)
	{
		if (is_string($fileName) AND is_readable($fileName))
		{
			$this->SetFileName($fileName);
		}
	}
	

	/**
	 * File name setter.
	 * @param string $fileName
	 */
	public function SetFileName($fileName)
	{
		$this->fileName = $fileName;
	}
	
	/**
	 * File name getter.
	 * @return string $this->fileName
	 */
	public function GetFileName()
	{
		return $this->fileName;
	}
	
	
	
	
/*	public function Render()
	{
		// Start capturing the output
		ob_start();
		
		if (isset($this->_file))
		{
			// Import variables into the namespace
			extract($this->_data, EXTR_SKIP);
			// Include the view, allow access to class instance
			include $this->_file;
		}
		// Dump the buffer and return the output
		return ob_get_clean();
	}*/
	
}
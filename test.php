<?php

/**
 * A few bloated tests, these should be reorganized and updated.
 */

require_once 'view.php';

class ViewTest extends UnitTestCase {
	
	
	private $TestTemplateFileName = 'test_template_file.txt';
	
	
	function setUp()
	{
		// View class requires a template file, so create an empty file.
		@touch($this->TestTemplateFileName);
	}

	function tearDown()
	{
		@unlink($this->TestTemplateFileName);
	}
	
	
	function testCreateTestFile()
	{
		$this->assertTrue(is_readable($this->TestTemplateFileName));
		
		@unlink($this->TestTemplateFileName);
		$this->assertFalse(file_exists($this->TestTemplateFileName));
	}
	
	
	function testConstructorSetFileName()
	{
		$view = new View($this->TestTemplateFileName);
		$this->assertTrue($view->GetFileName() == $this->TestTemplateFileName);
	}
	

	function testDataArrayGetSet()
	{
		$view = new View();
		$this->assertTrue(is_array($view->GetDataArray()));
		
		$view->SetDataArray("this shouldn't work");
		$this->assertTrue(is_array($view->GetDataArray()));
		
		$view->SetDataArray(array('test'=>'variable'));
		$this->assertTrue($view->GetDataArray() == array('test'=>'variable'));
		$this->assertTrue($view->test == 'variable');
	}
	

	function testVariableSetAndGet()
	{
		$view = new View($this->TestTemplateFileName);
		
		$view->SetVariable('testMessage', 'Hello, World!');
		$this->assertTrue($view->GetVariable('testMessage') == 'Hello, World!');
		
		// Test existing variables
		$this->assertFalse($view->GetVariable('fileName') == $this->TestTemplateFileName);
		$this->assertFalse($view->fileName == $this->TestTemplateFileName);
		$this->assertFalse(isset($view->fileName));
		$view->fileName = 'test';
		$this->assertTrue($view->fileName == 'test');
		$this->assertTrue($view->GetFileName() == $this->TestTemplateFileName);
		
		$view->SetVariable('testNumeric', 123);
		$this->assertTrue($view->GetVariable('testNumeric') === 123);
		
		// Alternative syntax with magic methods
		$this->assertTrue($view->testNumeric === 123);
		$view->SomeTestVariable = 'FooBar';
		$this->assertTrue($view->GetVariable('SomeTestVariable') == 'FooBar');
		$this->assertTrue($view->SomeTestVariable == 'FooBar');
		
		$view->SetVariable(456, 'Goats eat grass');
		$this->assertTrue($view->GetVariable(456) == 'Goats eat grass');
		
		$view->SetVariable(null, 'Null variable');
		$this->assertTrue($view->GetVariable(null) == 'Null variable');
		$this->assertTrue($view->GetVariable('') == 'Null variable');
		$this->assertFalse($view->GetVariable(0) == 'Null variable');

		$anotherView = new View($this->TestTemplateFileName);
		
		$view->SetVariable('testObject', $anotherView);
		$this->assertTrue(is_object($view->GetVariable('testObject')));
	}
	
	function testRenderTemplate()
	{
		$view = new View($this->TestTemplateFileName);
		$view->TestVariable = 'bar';
		
		$this->assertTrue($view->Render() == '');
		
		file_put_contents($this->TestTemplateFileName, 'Some template data');
		$this->assertTrue($view->Render() == 'Some template data');
		
		file_put_contents($this->TestTemplateFileName, 'Foo <?php echo $this->FakeVar; ?> data');
		$this->assertTrue($view->Render() == 'Foo  data');
		
		file_put_contents($this->TestTemplateFileName, 'Foo <?php echo $TestVariable; ?> data');
		$this->assertTrue($view->Render() == 'Foo bar data');
		
		file_put_contents($this->TestTemplateFileName, 'Foo <?php echo $this->TestVariable; ?> data');
		$this->assertTrue($view->Render() == 'Foo bar data');
		
		// Alternative syntax with magic method
		$this->assertTrue($view == 'Foo bar data');
		
		
		@unlink($this->TestTemplateFileName);
		$this->assertTrue($view == '');
		
		$view->SetFileName(null);
		$this->assertTrue($view == '');
		
		$view->SetFileName('');
		$this->assertTrue($view == '');
		
	}
	

}

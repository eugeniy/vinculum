<?php

require_once 'view.php';

class ViewTest extends UnitTestCase {
	
	private $TestTemplateDirectory = '.';
	private $TestTemplateFileName = 'test_template_file.txt';
	
	private $TestData = array();
	

	function setUp()
	{
		View::SetDirectory($this->TestTemplateDirectory);
		
		// View class requires a template file, so create an empty file.
		@touch($this->TestTemplateFileName);
		
		$this->TestData['bear'] = 'teddy';
		$this->TestData['kitten'] = 'fluffy';
		$this->TestData['pig'] = 'piglet';
	}

	
	function tearDown()
	{
		$this->TestData = array();
		@unlink($this->TestTemplateFileName);
	}
	
	
	function testCreateTestFile()
	{
		$this->assertTrue(is_readable($this->TestTemplateFileName));
		
		@unlink($this->TestTemplateFileName);
		$this->assertFalse(file_exists($this->TestTemplateFileName));
	}

	
	function testVariablesAccessors()
	{
		$view = new View();
		$returnArray = $view->Set($this->TestData);
		
		$this->assertTrue($view->bear == 'teddy');
		$this->assertTrue($view->kitten == 'fluffy');
		$this->assertFalse($view->bunny);
		$this->assertIsA($returnArray, 'View');

		$returnSingle = $view->Set('bunny', 'floppy');
		$this->assertTrue($view->bunny == 'floppy');
		$this->assertIsA($returnSingle, 'View');
		
		$view->dog = 'buddy';
		$this->assertTrue($view->dog == 'buddy');
		
		$view->Set('_testMessage', 'Hello, World!');
		$this->assertTrue($view->_testMessage == 'Hello, World!');
		
		$view->Set('testNumeric', 123);
		$this->assertTrue($view->testNumeric === 123);
		
		$anotherView = new View();
		$view->Set('testObject', $anotherView);
		$this->assertTrue(is_object($view->testObject));
	}

	function testExistingPropertiesConflicts()
	{
		$view = new View();
		$this->assertFalse($view->fileName == $this->TestTemplateFileName);
		$this->assertFalse(isset($view->fileName));
		$view->fileName = 'test';
		$this->assertTrue($view->fileName == 'test');
		$this->assertFalse(isset($view->data));
		$this->assertFalse(isset($view->directory));
	}
	
	function testGetFilePath()
	{
		$view = new View();
		$this->assertTrue($view->GetFilePath() === false);
		
		$view->SetFileName($this->TestTemplateFileName);
		$this->assertFalse($view->GetFilePath() === false);
		$this->assertTrue($view->GetFilePath() == $this->TestTemplateDirectory.'/'.$this->TestTemplateFileName);
		
		$view->SetDirectory('');
		$this->assertTrue($view->GetFilePath() == $this->TestTemplateFileName);
		
		$view->SetDirectory('foobar');
		$this->assertFalse($view->GetFilePath());
		
		$view->SetDirectory('./');
		$this->assertTrue(file_exists($view->GetFilePath()));
		$view->SetDirectory('.////////');
		$this->assertTrue(file_exists($view->GetFilePath()));
	}
	
	function testFactory()
	{
		file_put_contents($this->TestTemplateFileName, 'Hello <?php echo $bear; ?>');
		$this->assertTrue(View::Factory($this->TestTemplateFileName, $this->TestData) == 'Hello teddy');
		$this->assertTrue(View::Factory()->Set('bear', 'vinnie')->SetFileName($this->TestTemplateFileName) == 'Hello vinnie');
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
		
		$this->assertTrue($view == 'Foo bar data');
		
		@unlink($this->TestTemplateFileName);
		$this->assertTrue($view == '');
		
		$view->SetFileName('');
		$this->assertTrue($view == '');
	}
	
}

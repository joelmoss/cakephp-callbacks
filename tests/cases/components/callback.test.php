<?php
/**
* Callback Component Test Case
*/
class CallbackTestCase extends CakeTestCase
{
    function setup()
    {
        App::build(array(
            'views' => array(
                dirname(dirname(dirname(__FILE__))) . DS . 'test_app' . DS . 'views' . DS
            ),
            'controllers' => array(
                dirname(dirname(dirname(__FILE__))) . DS . 'test_app' . DS . 'controllers' . DS
            )
        ));        
    }
    
	function testCallbacks()
	{
	    App::import('Controller', 'FirstCallback');
	    $this->Controller = new FirstCallbackController($this);
	    $this->Controller->actionForTest('/first_callback');
	    
	    $expected = array(
	        'beforeFilter' => true,
	        'beforeRender' => true,
	        'afterFilter' => true
	    );
	    $this->assertEqual($expected, $this->Controller->callbackResults);
	}
	
    function testCallbacksAsStrings()
    {
	    App::import('Controller', 'SecondCallback');
	    $this->Controller = new SecondCallbackController($this);
        $this->Controller->actionForTest('/second_callback');
        
        $expected = array(
            'beforeFilter' => true,
            'beforeRender' => true,
            'afterFilter' => true
        );
        $this->assertEqual($expected, $this->Controller->callbackResults);
    }
    
    function testMultipleCallbacks()
    {
	    App::import('Controller', 'ThirdCallback');
	    $this->Controller = new ThirdCallbackController($this);
        $this->Controller->actionForTest('/third_callback');
        
        $expected = array(
            'beforeFilter' => true,
            'beforeFilterTwo' => true,
            'beforeRender' => true,
            'beforeRenderTwo' => true,
            'afterFilter' => true,
            'afterFilterTwo' => true
        );
        $this->assertEqual($expected, $this->Controller->callbackResults);
    }
    
    function testRecursiveCallbacks()
    {
	    App::import('Controller', 'FourthCallback');
	    $this->Controller = new FourthCallbackController($this);
        $this->Controller->actionForTest('/fourth_callback');
    
        $expected = array(
            'beforeFilterApp' => true,
            'beforeFilter' => true,
            'beforeRenderApp' => true,
            'beforeRender' => true,
            'afterFilterApp' => true,
            'afterFilter' => true
        );
        $this->assertEqual($expected, $this->Controller->callbackResults);
    }
        
    function testRecursiveCallbacksAsStrings()
    {
	    App::import('Controller', 'FifthCallback');
	    $this->Controller = new FifthCallbackController($this);
        $this->Controller->actionForTest('/fifth_callback');
    
        $expected = array(
            'beforeFilterApp' => true,
            'beforeFilter' => true,
            'beforeRenderApp' => true,
            'beforeRender' => true,
            'afterFilterApp' => true,
            'afterFilter' => true,
            'afterFilterTwo' => true
        );
        $this->assertEqual($expected, $this->Controller->callbackResults);
    }
        
    function testCallbackExcept()
    {
	    App::import('Controller', 'SixthCallback');
	    $this->Controller = new SixthCallbackController($this);
        $this->Controller->actionForTest('/sixth_callback');
    
        $expected = array(
            'beforeRender' => true,
        );
        $this->assertEqual($expected, $this->Controller->callbackResults);
    }
        
    function testCallbackOnly()
    {
	    App::import('Controller', 'SixthCallback');
	    $this->Controller = new SixthCallbackController($this);
        $this->Controller->actionForTest('/sixth_callback/view');
    
        $expected = array(
            'beforeFilter' => true,
            'beforeRender' => true,
            'afterFilter' => true
        );
        $this->assertEqual($expected, $this->Controller->callbackResults);
    }
        
    function testCallbackIfUnless()
    {
	    App::import('Controller', 'SeventhCallback');
	    $this->Controller = new SeventhCallbackController($this);
        $this->Controller->actionForTest('/seventh_callback');
    
        $expected = array(
            'beforeRender' => true,
            'afterFilter' => true
        );
        $this->assertEqual($expected, $this->Controller->callbackResults);
    }
        
}
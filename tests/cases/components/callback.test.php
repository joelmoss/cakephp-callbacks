<?php
/**
* Callback Component Test Case
* 
*/
class CallbackTestCase extends ControllerTestCase
{
    function setup()
    {
        Configure::write('controllerPaths', array(dirname(dirname(dirname(__FILE__))) . DS . 'test_app' . DS . 'controllers' . DS));
        Configure::write('viewPaths', array(dirname(dirname(dirname(__FILE__))) . DS . 'test_app' . DS . 'views' . DS));
    }
    
	function testCallbacks()
	{
	    $this->initAction('/first_callback', array('return' => 'vars'));
	    
	    $expected = array(
	        'beforeFilter' => true,
	        'beforeRender' => true,
	        'afterFilter' => true
	    );
	    $this->assertSame($expected, $this->Controller->callbackResults);
	}
	
	function testCallbacksAsStrings()
	{
	    $this->initAction('/second_callback', array('return' => 'vars'));
	    
	    $expected = array(
	        'beforeFilter' => true,
	        'beforeRender' => true,
	        'afterFilter' => true
	    );
	    $this->assertSame($expected, $this->Controller->callbackResults);
	}
	
	function testMultipleCallbacks()
	{
	    $this->initAction('/third_callback', array('return' => 'vars'));
	    
	    $expected = array(
	        'beforeFilter' => true,
	        'beforeFilterTwo' => true,
	        'beforeRender' => true,
	        'beforeRenderTwo' => true,
	        'afterFilter' => true,
	        'afterFilterTwo' => true
	    );
	    $this->assertSame($expected, $this->Controller->callbackResults);
	}
	
    function testRecursiveCallbacks()
    {
        App::import('Controller', 'CallbackApp');
        $this->initAction('/fourth_callback', array('return' => 'vars'));

	    $expected = array(
	        'beforeFilterApp' => true,
	        'beforeFilter' => true,
	        'beforeRenderApp' => true,
	        'beforeRender' => true,
	        'afterFilterApp' => true,
	        'afterFilter' => true
	    );
	    $this->assertSame($expected, $this->Controller->callbackResults);
    }
    
    function testRecursiveCallbacksAsStrings()
    {
        App::import('Controller', 'CallbackTwoApp');
        $this->initAction('/fifth_callback', array('return' => 'vars'));

	    $expected = array(
	        'beforeFilterApp' => true,
	        'beforeFilter' => true,
	        'beforeRenderApp' => true,
	        'beforeRender' => true,
	        'afterFilterApp' => true,
	        'afterFilter' => true,
	        'afterFilterTwo' => true
	    );
	    $this->assertSame($expected, $this->Controller->callbackResults);
    }
    
    function testCallbackExcept()
    {
        $this->initAction('/sixth_callback', array('return' => 'vars'));

	    $expected = array(
	        'beforeRender' => true,
	    );
	    $this->assertSame($expected, $this->Controller->callbackResults);
    }
    
    function testCallbackOnly()
    {
        $this->initAction('/sixth_callback/view', array('return' => 'vars'));

	    $expected = array(
	        'beforeFilter' => true,
	        'beforeRender' => true,
	        'afterFilter' => true
	    );
	    $this->assertSame($expected, $this->Controller->callbackResults);
    }
    
    function testCallbackIfUnless()
    {
        $this->initAction('/seventh_callback', array('return' => 'vars'));

	    $expected = array(
	        'beforeRender' => true,
	        'afterFilter' => true
	    );
	    $this->assertSame($expected, $this->Controller->callbackResults);
    }
}
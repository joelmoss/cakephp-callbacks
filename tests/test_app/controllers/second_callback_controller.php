<?php
App::import('Controller', 'Test');
/**
* Test Controller
*/
class SecondCallbackController extends TestController
{
    var $uses = null;
    var $components = array('Callback.Callback');
    var $layout = false;
    
    var $beforeFilter = 'beforeFilter';
    var $afterFilter = 'afterFilter';
    var $beforeRender = 'beforeRender';
    
    var $callbackResults = array();
    
    function _beforeFilter()
    {
        $this->callbackResults['beforeFilter'] = true;
    }
    function _afterFilter()
    {
        $this->callbackResults['afterFilter'] = true;
    }
    function _beforeRender()
    {
        $this->callbackResults['beforeRender'] = true;
    }
    
    function index() {
        $this->render('../global');
    }
}
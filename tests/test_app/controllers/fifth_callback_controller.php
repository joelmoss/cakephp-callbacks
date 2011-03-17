<?php
App::import('Controller', 'CallbackTwoApp');
/**
* Test Controller
*/
class FifthCallbackController extends CallbackTwoAppController
{
    var $uses = null;
    var $components = array('Callback.Callback');
    var $layout = false;
    
    var $beforeFilter = 'beforeFilter';
    var $afterFilter = array('afterFilter', 'afterFilterTwo');
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
    function _afterFilterTwo()
    {
        $this->callbackResults['afterFilterTwo'] = true;
    }
    function _beforeRender()
    {
        $this->callbackResults['beforeRender'] = true;
    }
    
    function index() {
        $this->render('../global');
    }
}
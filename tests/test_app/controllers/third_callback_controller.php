<?php
/**
* Test Controller
*/
class ThirdCallbackController extends Controller
{
    var $uses = null;
    var $components = array('Callback.Callback');
    
    var $beforeFilter = array(
        'beforeFilter',
        'beforeFilterTwo'
    );
    var $afterFilter = array(
        'afterFilter',
        'afterFilterTwo'
    );
    var $beforeRender = array(
        'beforeRender',
        'beforeRenderTwo'
    );
    
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
    function _beforeFilterTwo()
    {
        $this->callbackResults['beforeFilterTwo'] = true;
    }
    function _afterFilterTwo()
    {
        $this->callbackResults['afterFilterTwo'] = true;
    }
    function _beforeRenderTwo()
    {
        $this->callbackResults['beforeRenderTwo'] = true;
    }
    
    function index() {
        $this->render('../global');
    }
}
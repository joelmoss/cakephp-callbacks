<?php
/**
* Test Controller
*/
class FirstCallbackController extends Controller
{
    var $uses = null;
    var $components = array('Callback.Callback');
    
    var $beforeFilter = array(
        'beforeFilter'
    );
    var $afterFilter = array(
        'afterFilter'
    );
    var $beforeRender = array(
        'beforeRender'
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
    
    function index() {
        $this->render('../global');
    }
}
<?php
/**
* Test Controller
*/
class SixthCallbackController extends Controller
{
    var $uses = null;
    var $components = array('Callback.Callback');
    
    var $beforeFilter = array(
        'beforeFilter' => array(
            'except' => array('index')
        )
    );
    var $afterFilter = array(
        'afterFilter' => array(
            'only' => 'view'
        )
    );
    var $beforeRender = array(
        'beforeRender' => array(
            'only' => array('view', 'index')
        )
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
    function view() {
        $this->render('../global');
    }
}
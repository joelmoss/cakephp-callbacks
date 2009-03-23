<?php
/**
* Test Controller
*/
class SeventhCallbackController extends Controller
{
    var $uses = null;
    var $components = array('Callback.Callback');
    
    var $beforeFilter = array(
        'beforeFilter' => array(
            'if' => 'isTrue',
            'only' => 'view'
        )
    );
    var $afterFilter = array(
        'afterFilter' => array(
            'unless' => 'isFalse'
        )
    );
    var $beforeRender = array(
        'beforeRender' => array(
            'if' => 'isTrue'
        )
    );
    
    var $callbackResults = array();
    
    function _isTrue()
    {
        return true;
    }
    function _isFalse()
    {
        return false;
    }
    
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
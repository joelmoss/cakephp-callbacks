<?php
App::import('Controller', 'Test');
/**
* Test Controller for extending
*/
class CallbackAppController extends TestController
{
    var $beforeFilter = array(
        'beforeFilterApp'
    );
    var $afterFilter = array(
        'afterFilterApp'
    );
    var $beforeRender = array(
        'beforeRenderApp'
    );
    
    function _beforeFilterApp()
    {
        $this->callbackResults['beforeFilterApp'] = true;
    }
    function _afterFilterApp()
    {
        $this->callbackResults['afterFilterApp'] = true;
    }
    function _beforeRenderApp()
    {
        $this->callbackResults['beforeRenderApp'] = true;
    }
}

<?php
App::import('Controller', 'Test');
/**
* Test Controller for extending
*/
class CallbackTwoAppController extends TestController
{
    var $beforeFilter = 'beforeFilterApp';
    var $afterFilter = 'afterFilterApp';
    var $beforeRender = 'beforeRenderApp';
    
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

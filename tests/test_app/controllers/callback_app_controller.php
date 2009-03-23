<?php
/**
* Test Controller for extending
*/
class CallbackAppController extends Controller
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

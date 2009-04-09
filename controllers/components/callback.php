<?php
/**
* Callback Component
* 
* This component extends the CakePHP callbacks architecture, by supporting
* callbacks in class properties.
* 
* Simply define any of the three callbacks defined in CallbackComponent::__callbacks
* like this:
* 
*   class MyController extends AppController {
*       var $beforeFilter = array('myCallback');
*       function _myCallback() {
*           # do something here
*       }
*   }
* 
* You can declare as many callbacks as you wish:
* 
*   class MyController extends AppController {
*       var $beforeFilter = array('myCallback', 'anotherCallback', 'andAnotherOne');
* 
* Callbacks also support a number of options, which are passed to the callback:
* 
*   'only' An array of controller actions that this callback should be called on.
*   'except' An array of controller actions that the callback will NOT be called on.
*   'if' a method name, which returns true|false. Callback will only be run if method is true.
*   'unless' a method name, which returns true|false. Callback will not be run if method is true.
* 
*   Example:
* 
*   class MyController extends AppController {
*       var $beforeFilter = array(
*           'myCallback' => array(
*               'only' => array('index', 'view'),
*               'if' => 'ifMethod'
*           )
*       );
*/
class CallbackComponent extends Object
{
    private $__callbacks = array(
        'beforeFilter',
        'beforeRender',
        'afterFilter'
    );
    
    public function initialize($controller, $settings = array())
    {
		foreach ($this->__callbacks as $var) {
		    if (!isset($controller->{$var})) {
		        $controller->{$var} = array();
	        } else {
	            $controller->{$var} = (array)$controller->{$var};
	        }
	    }
        
        $this->__mergeVars($controller);
    }
/**
 * Calls callbacks defined in properties of controllers.
 *
 * @access private
 */
	private function __callbacks($controller, $callbackName)
	{
	    $callbacks = $controller->$callbackName;
	    
        foreach ((array)$callbacks as $callback => $conditionals) {
            $ok = true;
            if (is_array($conditionals)) {
                if (!empty($conditionals)) {
                    if (isset($conditionals['only'])) {
                        if (!in_array($controller->action, (array)$conditionals['only'])) {
                            $ok = false;
                            break;
                        }
                    }
                    if (isset($conditionals['except'])) {
                        if (in_array($controller->action, (array)$conditionals['except'])) {
                            $ok = false;
                            break;
                        }
                    }
                    if (isset($conditionals['if'])) {
                        foreach ((array)$conditionals['if'] as $method) {
                            if (!$controller->dispatchMethod("_$method")) {
                                $ok = false;
                                break;
                            }
                        }
                    }
                    if (isset($conditionals['unless'])) {
                        foreach ((array)$conditionals['unless'] as $method) {
                            if ($controller->dispatchMethod("_$method")) {
                                $ok = false;
                                break;
                            }
                        }
                    }
                }
            } else {
                $callback = $conditionals;
            }
            
            if ($ok) {
                $controller->dispatchMethod('_' . $callback);
            }
        }
	}
/**
 * Merge callbacks props from AppController and PluginAppController.
 * (Idea taken from Controller)
 *
 * @return void
 * @access protected
 */
	function __mergeVars($controller)
	{
	    $parent = get_parent_class($controller);
	    
	    if ($controller->plugin) {
	        $pluginVars = get_class_vars($parent);
	        $appVars = get_class_vars(get_parent_class($parent));
	        
    		foreach ($this->__callbacks as $var) {
    			if (!empty($pluginVars[$var])) {
    			    $pluginVars[$var] = (array)$pluginVars[$var];
    			    $diff = array_diff($pluginVars[$var], $controller->{$var});
    				$controller->{$var} = Set::merge($diff, $controller->{$var});
    			}
    		}
	    } else {
	        $appVars = get_class_vars($parent);
	    }

		foreach ($this->__callbacks as $var) {
			if (!empty($appVars[$var])) {
			    $appVars[$var] = (array)$appVars[$var];
			    $diff = array_diff($appVars[$var], $controller->{$var});
				$controller->{$var} = Set::merge($diff, $controller->{$var});
			}
		}
	}
/**
 * beforeFilter
 */
    function startup($controller)
    {
        if (isset($controller->beforeFilter)) {
            return $this->__callbacks($controller, 'beforeFilter');
        }
    }
/**
 * beforeRender
 */
    function beforeRender($controller)
    {
        if (isset($controller->beforeRender)) {
            return $this->__callbacks($controller, 'beforeRender');
        }
    }
/**
 * afterFilter
 */
    function shutdown($controller)
    {
        if (isset($controller->afterFilter)) {
            return $this->__callbacks($controller, 'afterFilter');
        }
    }
}

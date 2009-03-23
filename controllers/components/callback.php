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
        $this->controller = $controller;
        
		foreach ($this->__callbacks as $var) {
		    if (!isset($this->controller->{$var})) {
		        $this->controller->{$var} = array();
	        } else {
	            $this->controller->{$var} = (array)$this->controller->{$var};
	        }
	    }
        
        $this->__mergeVars();
    }
/**
 * Calls callbacks defined in properties of controllers.
 *
 * @access private
 */
	private function __callbacks($callbacks)
	{
        foreach ((array)$callbacks as $callback => $conditionals) {
            $ok = true;
            if (is_array($conditionals)) {
                if (!empty($conditionals)) {
                    if (isset($conditionals['only'])) {
                        if (!in_array($this->controller->action, (array)$conditionals['only'])) {
                            $ok = false;
                            break;
                        }
                    }
                    if (isset($conditionals['except'])) {
                        if (in_array($this->controller->action, (array)$conditionals['except'])) {
                            $ok = false;
                            break;
                        }
                    }
                    if (isset($conditionals['if'])) {
                        foreach ((array)$conditionals['if'] as $method) {
                            if (!$this->controller->dispatchMethod("_$method")) {
                                $ok = false;
                                break;
                            }
                        }
                    }
                    if (isset($conditionals['unless'])) {
                        foreach ((array)$conditionals['unless'] as $method) {
                            if ($this->controller->dispatchMethod("_$method")) {
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
                $this->controller->dispatchMethod('_' . $callback);
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
	function __mergeVars()
	{
	    $parent = get_parent_class($this->controller);
	    
	    if ($this->controller->plugin) {
	        $pluginVars = get_class_vars($parent);
	        $appVars = get_class_vars(get_parent_class($parent));
	        
    		foreach ($this->__callbacks as $var) {
    			if (!empty($pluginVars[$var])) {
    			    $pluginVars[$var] = (array)$pluginVars[$var];
    			    $diff = array_diff($pluginVars[$var], $this->controller->{$var});
    				$this->controller->{$var} = Set::merge($diff, $this->controller->{$var});
    			}
    		}
	    } else {
	        $appVars = get_class_vars($parent);
	    }

		foreach ($this->__callbacks as $var) {
			if (!empty($appVars[$var])) {
			    $appVars[$var] = (array)$appVars[$var];
			    $diff = array_diff($appVars[$var], $this->controller->{$var});
				$this->controller->{$var} = Set::merge($diff, $this->controller->{$var});
			}
		}
	}
/**
 * beforeFilter
 */
    function startup()
    {
        if (isset($this->controller->beforeFilter)) {
            return $this->__callbacks($this->controller->beforeFilter);
        }
    }
/**
 * beforeRender
 */
    function beforeRender()
    {
        if (isset($this->controller->beforeRender)) {
            return $this->__callbacks($this->controller->beforeRender);
        }
    }
/**
 * afterFilter
 */
    function shutdown()
    {
        if (isset($this->controller->afterFilter)) {
            return $this->__callbacks($this->controller->afterFilter);
        }
    }
}

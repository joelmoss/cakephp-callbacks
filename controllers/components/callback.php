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
        $this->__order($controller);
    }
/**
 * Merge callbacks props from Components, AppController and PluginAppController.
 * (Idea taken from Controller)
 *
 * @param object $controller
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
    			    $diff = array_diff_assoc($pluginVars[$var], $controller->{$var});
    				$controller->{$var} = Set::merge($diff, $controller->{$var});
    			}
    		}
		} else {
	        $appVars = get_class_vars($parent);
	    }
	    
        if (get_parent_class(get_parent_class($parent)) == 'Controller') {
		    $appAppVars = get_class_vars(get_parent_class($parent));
		    
    		foreach ($this->__callbacks as $var) {
    			if (!empty($appAppVars[$var])) {
    			    $appAppVars[$var] = (array)$appAppVars[$var];
    			    $diff = array_diff_assoc($appAppVars[$var], $controller->{$var});
    				$controller->{$var} = Set::merge($diff, $controller->{$var});
    			}
    		}
	    }
	    
		foreach ($this->__callbacks as $var) {
			if (!empty($appVars[$var])) {
			    $appVars[$var] = (array)$appVars[$var];
			    $diff = array_diff_assoc($appVars[$var], $controller->{$var});
				$controller->{$var} = Set::merge($diff, $controller->{$var});
			}
		}
		
	    foreach ($controller->Component->_loaded as $component) {
	        foreach ($this->__callbacks as $var) {
    			if (isset($component->$var) && !empty($component->$var)) {
    			    $component->$var = (array)$component->$var;
    			    $diff = array_diff_assoc($component->$var, $controller->{$var});
    				$controller->{$var} = Set::merge($diff, $controller->{$var});
    			}
            }
	    }
	}   
/**
 * Orders the callbacks according to the 'order' attribute
 * 
 * @param object $controller
 */
    private function __order($controller)
    {
        foreach ($this->__callbacks as $var) {
            if (isset($controller->{$var})) {
                $callbacks = $lastCallbacks = $firstCallbacks = array();
                foreach ((array)$controller->{$var} as $k => $v) {
                    if (is_array($v)) {
                        if (isset($v['order']) && $v['order'] == 'last') {
                            unset($v['order']);
                            $lastCallbacks[$k] = $v;
                        } elseif (isset($v['order']) && $v['order'] == 'first') {
                            unset($v['order']);
                            $firstCallbacks[$k] = $v;
                        } else {
                            $callbacks[$k] = $v;
                        }
                    } else {
                        $callbacks[$v] = array();
                    }
                }

                $i = count($firstCallbacks) + 1;
                foreach ($callbacks as $cb => $d) {
                    if (!isset($callbacks[$cb]['order'])) {
                        $callbacks[$cb]['order'] = $i;
                    }
                    $i++;
                }
                
                $i = count($firstCallbacks);
                foreach (array_reverse($firstCallbacks) as $cb => $d) {
                    $d['order'] = $i;
                    $callbacks = $callbacks + array($cb => $d);
                    $i--;
                }
                
                $i = count($firstCallbacks) + count($callbacks);
                foreach ($lastCallbacks as $cb => $d) {
                    $d['order'] = $i;
                    $callbacks = $callbacks + array($cb => $d);
                    $i++;
                }
                
                uasort($callbacks, array($this, '_cmp'));
                $controller->{$var} = $callbacks;
            }
        }
    }
    
    private function _cmp($a, $b)
    {
        if ($a['order'] == $b['order']) {
            return 0;
        } elseif ($a['order'] < $b['order']) {
            return -1;
        } elseif ($a['order'] > $b['order']) {
            return 1;
        }
    }
/**
 * Calls callbacks defined in properties of components, AppController, and
 * controller - in that order.
 * 
 * @param object $controller
 * @param string $callbackName The name of the callback
 * @access private
 */
	private function __callbacks($controller, $callbackName)
	{
	    $callbacks = $controller->$callbackName;
	    
        foreach ((array)$callbacks as $callback => $conditionals) {
            $ok = true;
            if (!empty($conditionals)) {
                if (isset($conditionals['only'])) {
                    if (!in_array($controller->action, (array)$conditionals['only'])) {
                        $ok = false;
                        continue;
                    }
                }
                if (isset($conditionals['except'])) {
                    if (in_array($controller->action, (array)$conditionals['except'])) {
                        $ok = false;
                        continue;
                    }
                }
                if (isset($conditionals['if'])) {
                    foreach ((array)$conditionals['if'] as $method) {
                        if (!$controller->dispatchMethod("_$method")) {
                            $ok = false;
                            break;
                        }
                    }
                    if (!$ok) continue;
                }
                if (isset($conditionals['unless'])) {
                    foreach ((array)$conditionals['unless'] as $method) {
                        if ($controller->dispatchMethod("_$method")) {
                            $ok = false;
                            break;
                        }
                    }
                    if (!$ok) continue;
                }
            }
            
            if ($ok) {
                if (method_exists($controller, '_' . $callback)) {
                    $controller->dispatchMethod('_' . $callback);
                } else {
                    foreach ($controller->Component->_loaded as $component) {
                        if (method_exists($component, '_' . $callback)) {
                            $component->dispatchMethod('_' . $callback);
                        }
                    }
                }
            }
        }
	}
/**
 * beforeFilter
 * 
 * @param object $controller
 */
    function startup($controller)
    {
        if (isset($controller->beforeFilter)) {
            return $this->__callbacks($controller, 'beforeFilter');
        }
    }
/**
 * beforeRender
 * 
 * @param object $controller
 */
    function beforeRender($controller)
    {
        if (isset($controller->beforeRender)) {
            return $this->__callbacks($controller, 'beforeRender');
        }
    }
/**
 * afterFilter
 * 
 * @param object $controller
 */
    function shutdown($controller)
    {
        if (isset($controller->afterFilter)) {
            return $this->__callbacks($controller, 'afterFilter');
        }
    }
}

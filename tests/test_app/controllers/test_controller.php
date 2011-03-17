<?php
define('TESTS_RUNNING', true);

/**
* Test Controller
*/
class TestController extends Controller
{

    public function __construct($testCase)
    {
        $this->_testCase = $testCase;
        parent::__construct();
    }
    
/**
 * Starts the process for the given $url.
 * 
 * @param string $url Requested URL
 * @param array $params Settings array ("bare", "return") which is
 *              melded with the GET and POST params
 * @return mixed The results of the called action
 */
    public function startActionForTest($url, $params = array())
    {
		$default = array(
			'fixturize' => false,
			'data' => array(),
			'method' => 'get',
			'connection' => 'default'
		);
		$params = array_merge($default, $params);

		$toSave = array(
			'case' => null,
			'group' => null,
			'app' => null,
			'output' => null,
			'show' => null,
			'plugin' => null
		);
		$this->__savedGetData = (empty($this->__savedGetData))
				? array_intersect_key($_GET, $toSave)
				: $this->__savedGetData;

		$data = !empty($params['data']) ? $params['data'] : array();

		if (strtolower($params['method']) == 'get') {
			$_GET = array_merge($this->__savedGetData, $data);
			$_POST = array();
		} else {
			$_POST = array('data' => $data);
			$_GET = $this->__savedGetData;
		}
		
        $_SERVER['REQUEST_METHOD'] = strtoupper($params['method']);
		$params = array_diff_key($params, array('data' => null, 'method' => null));

        $Dispatcher = new Dispatcher();
		$url = $Dispatcher->getUrl($url);
		$this->params = array_merge($Dispatcher->parseParams($url), $params);
		$this->here = $this->base . '/' . $url;
		
		Router::setRequestInfo(array(
			$this->params, array(
			    'base' => $this->base,
			    'here' => $this->here,
			    'webroot' => $this->webroot
			)
		));
		
		$this->base = $this->base;
		$this->here = $this->here;
		$this->plugin = isset($this->params['plugin']) ? $this->params['plugin'] : null;
		$this->action =& $this->params['action'];
		$this->passedArgs = array_merge($this->params['pass'], $this->params['named']);

		if (!empty($this->params['data'])) {
			$this->data =& $this->params['data'];
		} else {
			$this->data = null;
		}
		if (!empty($this->params['bare'])) {
			$this->autoLayout = false;
		}

		if (isset($this->_testCase) && method_exists($this->_testCase, 'startController')) {
			$this->_testCase->startController($this, $this->params);
		}

        unset($_SESSION);

        $this->constructClasses();
        $this->startupProcess();        
    }
        
/**
 * Picks up where startActionForTest() left off and actually calls the
 * controller action.
 */
    public function callActionForTest()
    {
        $output = call_user_func_array(array($this, $this->params['action']), $this->params['pass']);

		if ($this->autoRender) {
			$this->output = $this->render();
		} elseif (empty($this->output)) {
			$this->output = $output;
		}
		$this->shutdownProcess();

		return $this->output;
    }
    
    public function actionForTest($url, $params = array())
    {
        $this->startActionForTest($url, $params);
        return $this->callActionForTest();
    }
    

/**
 * Overrides the redirect() method to allow us to test when a redirect happens
 * without actually allowing it to redirect.
 */
    public function redirect($url, $status = null, $exit = true)
    {
        if (defined('TESTS_RUNNING') && TESTS_RUNNING) {
            throw new RedirectException($url);
        } else {
            return parent::redirect($url, $status = null, $exit = true);
        }
    }
 
/**
 * Overrides the render() method so we can test actions that use it, without
 * having to deal with piles of HTML.
 */
    public function render($action = null, $layout = null, $file = null)
    {
        if (defined('TESTS_RUNNING') && TESTS_RUNNING === true) {
            $this->renderedAction = is_null($action) ? $this->action : $action;
        }

        return parent::render($action, $layout, $file);
    }
 
/**
 * Overrides _stop() method so it no longer halts script execution.
 */
    public function _stop($status = 0)
    {
        if (defined('TESTS_RUNNING') && TESTS_RUNNING) {
            return $this->stopped = $status;
        } else {
            return parent::_stop($status = 0);
        }
    }
}

/**
* RedirectException
*/
class RedirectException extends Exception { }
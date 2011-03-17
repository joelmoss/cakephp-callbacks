CakePHP Callback Component
==========================

This component extends the CakePHP callbacks architecture, by supporting callbacks in class properties.

Simply define any of the three callbacks defined in CallbackComponent::__callbacks like this:

    class MyController extends AppController
    {
        public $beforeFilter = array('myCallback');
  
        protected function _myCallback()
        {
            # do something here
        }
    }

You can declare as many callbacks as you wish:

    class MyController extends AppController
    {
        public $beforeFilter = array(
          'myCallback',
          'anotherCallback',
          'andAnotherOne'
        );
    }

Callbacks also support a number of options, which are passed to the callback:

  - 'only' An array of controller actions that this callback should be called on.
  - 'except' An array of controller actions that the callback will NOT be called on.
  - 'if' a method name, which returns true|false. Callback will only be run if method is true.
  - 'unless' a method name, which returns true|false. Callback will not be run if method is true.

Example:

    class MyController extends AppController
    {
        public $beforeFilter = array(
            'myCallback' => array(
                'only' => array('index', 'view'),
                'if' => 'ifMethod'
            )
        );
    }

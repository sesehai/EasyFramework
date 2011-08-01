<?php
/** RouterRouteAbstract */
require_once(ROOT_PATH.'/core/router/route/Abstract.php');

/**
 * Module Route
 *
 * Default route for module functionality
 */
class RouterRouteModule extends RouterRouteAbstract
{
    /**
     * URI delimiter
     */
    const URI_DELIMITER = '/';

    /**
     * Default values for the route (ie. module, controller, action, params)
     * @var array
     */
    protected $_defaults;

    protected $_values      = array();
    protected $_moduleValid = false;
    protected $_keysSet     = false;

    /**#@+
     * Array keys to use for module, controller, and action. Should be taken out of request.
     * @var string
     */
    protected $_moduleKey     = 'mod';
    protected $_controllerKey = 'ctl';
    protected $_actionKey     = 'act';
    /**#@-*/

    /**
     * @var RequestAbstract
     */
    protected $_request;

    public function getVersion() {
        return 1;
    }

    /**
     * Instantiates route based 
     */
    public static function getInstance()
    {
        $defs = array();
        return new self($defs);
    }

    /**
     * Constructor
     *
     * @param array $defaults Defaults for map variables with keys as variable names
     * @param RequestAbstract $request Request object
     */
    public function __construct(array $defaults = array(),
                RequestAbstract $request = null)
    {
        $this->_defaults = $defaults;

        if (isset($request)) {
            $this->_request = $request;
        }
    }

    /**
     * Set request keys based on values in request object
     *
     * @return void
     */
    protected function _setRequestKeys()
    {
        if (null !== $this->_request) {
            $this->_moduleKey     = $this->_request->getModuleKey();
            $this->_controllerKey = $this->_request->getControllerKey();
            $this->_actionKey     = $this->_request->getActionKey();
        }

        $this->_keysSet = true;
    }

    /**
     * Matches a user submitted path. Assigns and returns an array of variables
     * on a successful match.
     *
     * If a request object is registered, it uses its setModuleName(),
     * setControllerName(), and setActionName() accessors to set those values.
     * Always returns the values as an array.
     *
     * @param string $path Path used to match against this routing map
     * @return array An array of assigned values or a false on a mismatch
     */
    public function match($path)
    {
        $this->_setRequestKeys();

        $values = array();
        $params = array();
        $path   = trim($path, self::URI_DELIMITER);

        if ($path != '') {

            $path = explode(self::URI_DELIMITER, $path);

            if (count($path) && !empty($path[0])) {
                $values[$this->_moduleKey] = array_shift($path);
                $this->_moduleValid = true;
            }

            if (count($path) && !empty($path[0])) {
                $values[$this->_controllerKey] = array_shift($path);
            }

            if (count($path) && !empty($path[0])) {
                $values[$this->_actionKey] = array_shift($path);
            }

            if ($numSegs = count($path)) {
                for ($i = 0; $i < $numSegs; $i = $i + 2) {
                    $key = urldecode($path[$i]);
                    $val = isset($path[$i + 1]) ? urldecode($path[$i + 1]) : null;
                    $params[$key] = (isset($params[$key]) ? (array_merge((array) $params[$key], array($val))): $val);
                }
            }
        }

        $this->_values = $values + $params;

        return $this->_values + $this->_defaults;
    }

    /**
     * Assembles user submitted parameters forming a URL path defined by this route
     *
     * @param array $data An array of variable and value pairs used as parameters
     * @param bool $reset Weither to reset the current params
     * @return string Route path with user submitted parameters
     */
    public function assemble($data = array(), $reset = false, $encode = true)
    {
        if (!$this->_keysSet) {
            $this->_setRequestKeys();
        }

        $params = (!$reset) ? $this->_values : array();

        foreach ($data as $key => $value) {
            if ($value !== null) {
                $params[$key] = $value;
            } elseif (isset($params[$key])) {
                unset($params[$key]);
            }
        }

        $params += $this->_defaults;

        $url = '';

        if ($this->_moduleValid || array_key_exists($this->_moduleKey, $data)) {
            if ($params[$this->_moduleKey] != $this->_defaults[$this->_moduleKey]) {
                $module = $params[$this->_moduleKey];
            }
        }
        unset($params[$this->_moduleKey]);

        $controller = $params[$this->_controllerKey];
        unset($params[$this->_controllerKey]);

        $action = $params[$this->_actionKey];
        unset($params[$this->_actionKey]);

        foreach ($params as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $arrayValue) {
                    if ($encode) $arrayValue = urlencode($arrayValue);
                    $url .= '/' . $key;
                    $url .= '/' . $arrayValue;
                }
            } else {
                if ($encode) $value = urlencode($value);
                $url .= '/' . $key;
                $url .= '/' . $value;
            }
        }

        if (!empty($url) || $action !== $this->_defaults[$this->_actionKey]) {
            if ($encode) $action = urlencode($action);
            $url = '/' . $action . $url;
        }

        if (!empty($url) || $controller !== $this->_defaults[$this->_controllerKey]) {
            if ($encode) $controller = urlencode($controller);
            $url = '/' . $controller . $url;
        }

        if (isset($module)) {
            if ($encode) $module = urlencode($module);
            $url = '/' . $module . $url;
        }

        return ltrim($url, self::URI_DELIMITER);
    }

    /**
     * Return a single parameter of route's defaults
     *
     * @param string $name Array key of the parameter
     * @return string Previously set default
     */
    public function getDefault($name) {
        if (isset($this->_defaults[$name])) {
            return $this->_defaults[$name];
        }
    }

    /**
     * Return an array of defaults
     *
     * @return array Route defaults
     */
    public function getDefaults() {
        return $this->_defaults;
    }

}

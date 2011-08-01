<?php

require_once(ROOT_PATH.'/core/router/Abstract.php');

class RouterRewrite extends RouterAbstract
{

    /**
     * Whether or not to use default routes
     * @var boolean
     */
    protected $_useDefaultRoutes = true;

    /**
     * Array of routes to match against
     * @var array
     */
    protected $_routes = array();

    /**
     * Currently matched route
     * @var RouterRouteInterface
     */
    protected $_currentRoute = null;

    /**
     * Global parameters given to all routes
     * 
     * @var array
     */
    protected $_globalParams = array();
    
    /**
     * Add default routes which are used to mimic basic router behaviour
     */
    public function addDefaultRoutes( RequestAbstract $request )
    {
        if (!$this->hasRoute('default')) {
            require_once(ROOT_PATH.'/core/router/route/Module.php');
            $compat = new RouterRouteModule(array() , $request);
            $this->_routes = array_merge(array('default' => $compat), $this->_routes);
        }
    }

    /**
     * Add route to the route chain
     * 
     * @param string $name Name of the route
     * @param RouterRouteInterface Route
     */
    public function addRoute($name, RouterRouteInterface $route,RequestAbstract $request) 
    {
        if (method_exists($route, 'setRequest')) {
            $route->setRequest($request);
        }
        
        $this->_routes[$name] = $route;
        
        return $this;
    }

    /**
     * Add routes to the route chain
     *
     * @param array $routes Array of routes with names as keys and routes as values
     */
    public function addRoutes($routes) {
        foreach ($routes as $name => $route) {
            $this->addRoute($name, $route);
        }
        return $this;
    }

    /**
     * Remove a route from the route chain
     *
     * @param string $name Name of the route
     * @throws RouterException
     */
    public function removeRoute($name) {
        if (!isset($this->_routes[$name])) {
            require_once(ROOT_PATH.'/core/router/Exception.php');
            throw new RouterException("Route $name is not defined");
        }
        unset($this->_routes[$name]);
        return $this;
    }

    /**
     * Remove all standard default routes
     *
     * @param RouterRouteInterface Route
     */
    public function removeDefaultRoutes() {
        $this->_useDefaultRoutes = false;
        return $this;
    }

    /**
     * Check if named route exists
     *
     * @param string $name Name of the route
     * @return boolean
     */
    public function hasRoute($name)
    {
        return isset($this->_routes[$name]);
    }

    /**
     * Retrieve a named route
     *
     * @param string $name Name of the route
     * @throws RouterException
     * @return RouterRouteInterface Route object
     */
    public function getRoute($name)
    {
        if (!isset($this->_routes[$name])) {
            require_once(ROOT_PATH.'/core/router/Exception.php');
            throw new RouterException("Route $name is not defined");
        }
        return $this->_routes[$name];
    }

    /**
     * Retrieve a currently matched route
     *
     * @throws RouterException
     * @return RouterRouteInterface Route object
     */
    public function getCurrentRoute()
    {
        if (!isset($this->_currentRoute)) {
            require_once(ROOT_PATH.'/core/router/Exception.php');
            throw new RouterException("Current route is not defined");
        }
        return $this->getRoute($this->_currentRoute);
    }

    /**
     * Retrieve a name of currently matched route
     *
     * @throws RouterException
     * @return RouterRouteInterface Route object
     */
    public function getCurrentRouteName()
    {
        if (!isset($this->_currentRoute)) {
            require_once(ROOT_PATH.'/core/router/Exception.php');
            throw new RouterException("Current route is not defined");
        }
        return $this->_currentRoute;
    }

    /**
     * Retrieve an array of routes added to the route chain
     *
     * @return array All of the defined routes
     */
    public function getRoutes()
    {
        return $this->_routes;
    }

    /**
     * Find a matching route to the current PATH_INFO and inject
     * returning values to the Request object.
     *
     * @throws RouterException
     * @return RequestAbstract Request object
     */
    public function route(RequestAbstract $request)
    {

        if (!$request instanceof RequestHttp) {
            require_once(ROOT_PATH.'/core/router/Exception.php');
            throw new RouterException('RouterRewrite requires a RequestHttp-based request object');
        }

        if ($this->_useDefaultRoutes) {
            $this->addDefaultRoutes($request);
        }

        /** Find the matching route */
        foreach (array_reverse($this->_routes) as $name => $route) {
            
            // TODO: Should be an interface method. Hack for 1.0 BC  
            if (!method_exists($route, 'getVersion') || $route->getVersion() == 1) {
                $match = $request->getPathInfo();
            } else {
                $match = $request;
            }
                        
            if ($params = $route->match($match)) {
                $this->_setRequestParams($request, $params);
                $this->_currentRoute = $name;
                break;
            }
        }

        return $request;

    }

    protected function _setRequestParams($request, $params)
    {
    	$request->setQuery($params);
        foreach ($params as $param => $value) {

            $request->setParam($param, $value);

            if ($param === $request->getModuleKey()) {
                $request->setModuleName($value);
            }
            if ($param === $request->getControllerKey()) {
                $request->setControllerName($value);
            }
            if ($param === $request->getActionKey()) {
                $request->setActionName($value);
            }

        }
    }

    /**
     * Generates a URL path that can be used in URL creation, redirection, etc.
     * 
     * @param  array $userParams Options passed by a user used to override parameters
     * @param  mixed $name The name of a Route to use
     * @param  bool $reset Whether to reset to the route defaults ignoring URL params
     * @param  bool $encode Tells to encode URL parts on output
     * @throws RouterException
     * @return string Resulting absolute URL path
     */ 
    public function assemble($userParams, $name = null, $reset = false, $encode = true)
    {
        if ($name == null) {
            try {
                $name = $this->getCurrentRouteName();
            } catch (RouterException $e) {
                $name = 'default';
            }
        }
        
        $params = array_merge($this->_globalParams, $userParams);
        
        $route = $this->getRoute($name);
        $url   = $route->assemble($params, $reset, $encode);

        return $url;
    }
    
    /**
     * Set a global parameter
     * 
     * @param  string $name
     * @param  mixed $value
     * @return RouterRewrite
     */
    public function setGlobalParam($name, $value)
    {
        $this->_globalParams[$name] = $value;
    
        return $this;
    }
}
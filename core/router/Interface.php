<?php
/**
 */
interface RouterInterface
{
    /**
     * Processes a request and sets its controller and action.  If
     * no route was possible, an exception is thrown.
     *
     * @param  RequestAbstract
     * @throws RouterException
     * @return RequestAbstract|boolean
     */
    public function route(RequestAbstract $request);

    /**
     * Generates a URL path that can be used in URL creation, redirection, etc. 
     * 
     * May be passed user params to override ones from URI, Request or even defaults. 
     * If passed parameter has a value of null, it's URL variable will be reset to
     * default. 
     * 
     * If null is passed as a route name assemble will use the current Route or 'default'
     * if current is not yet set.
     * 
     * Reset is used to signal that all parameters should be reset to it's defaults. 
     * Ignoring all URL specified values. User specified params still get precedence.
     * 
     * Encode tells to url encode resulting path parts.     
     *
     * @param  array $userParams Options passed by a user used to override parameters
     * @param  mixed $name The name of a Route to use
     * @param  bool $reset Whether to reset to the route defaults ignoring URL params
     * @param  bool $encode Tells to encode URL parts on output
     * @throws RouterException
     * @return string Resulting URL path
     */
    public function assemble($userParams, $name = null, $reset = false, $encode = true);
    
    /**
     * Add or modify a parameter with which to instantiate any helper objects
     *
     * @param string $name
     * @param mixed $param
     * @return RouterInterface
     */
    public function setParam($name, $value);

    /**
     * Set an array of a parameters to pass to helper object constructors
     *
     * @param array $params
     * @return RouterInterface
     */
    public function setParams(array $params);

    /**
     * Retrieve a single parameter from the controller parameter stack
     *
     * @param string $name
     * @return mixed
     */
    public function getParam($name);

    /**
     * Retrieve the parameters to pass to helper object constructors
     *
     * @return array
     */
    public function getParams();

    /**
     * Clear the controller parameter stack
     *
     * By default, clears all parameters. If a parameter name is given, clears
     * only that parameter; if an array of parameter names is provided, clears
     * each.
     *
     * @param null|string|array single key or array of keys for params to clear
     * @return RouterInterface
     */
    public function clearParams($name = null);
    
}

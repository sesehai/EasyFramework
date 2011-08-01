<?php
/** RouterRouteInterface */
require_once(ROOT_PATH.'/core/router/route/Interface.php');

/**
 * Abstract Route
 *
 * Implements interface and provides convenience methods
 */
abstract class RouterRouteAbstract implements RouterRouteInterface
{

    public function getVersion() {
        return 2;
    }
    

}

<?php

/**
 */
interface RouterRouteInterface {
    public function match($path);
    public function assemble($data = array(), $reset = false, $encode = false);
    public static function getInstance();
}


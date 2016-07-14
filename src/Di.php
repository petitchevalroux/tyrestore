<?php

namespace TyreStore;

/**
 * @property \ConfigurationFactory\Factory $config configuration factory
 */
class Di extends \Di\Di {

    protected function __construct() {
        parent::__construct();
        $this->setDependenciesDirectory(__DIR__ . DIRECTORY_SEPARATOR . 'Dependencies');
    }

}

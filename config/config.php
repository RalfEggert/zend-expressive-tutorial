<?php

use Zend\Expressive\ConfigManager\ConfigManager;
use Zend\Expressive\ConfigManager\PhpFileProvider;

$configManager = new ConfigManager([
    \Zend\Hydrator\ConfigProvider::class,
    \Zend\Db\ConfigProvider::class,
    new PhpFileProvider('config/autoload/{{,*.}global,{,*.}local}.php'),
]);

return new ArrayObject($configManager->getMergedConfig());

<?php

use Zend\Expressive\ConfigManager\ConfigManager;
use Zend\Expressive\ConfigManager\PhpFileProvider;

$configManager = new ConfigManager([
    \Zend\Hydrator\ConfigProvider::class,
    \Zend\Db\ConfigProvider::class,
    Zend\Filter\ConfigProvider::class,
    Zend\I18n\ConfigProvider::class,
    Zend\Router\ConfigProvider::class,
    Zend\Validator\ConfigProvider::class,
    new PhpFileProvider('config/autoload/{{,*.}global,{,*.}local}.php'),
]);

return new ArrayObject($configManager->getMergedConfig());

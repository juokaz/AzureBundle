<?php

namespace Bundle\AzureBundle\Session;

use Symfony\Component\HttpFoundation\SessionStorage\NativeSessionStorage;

class AzureSessionStorage extends NativeSessionStorage
{
    protected $handler = null;

    public function __construct(\Microsoft_WindowsAzure_SessionHandler $handler, array $options = array())
    {
        // Register session handler
        $handler->register();

        // load class to make it available in write stage at end of request
        // @todo remove this hack
        new \Microsoft_WindowsAzure_Storage_DynamicTableEntity();

        parent::__construct($options);
    }
}

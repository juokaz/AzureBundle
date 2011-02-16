<?php

namespace Bundle\AzureBundle\Doctrine;

use Doctrine\Common\Cache\AbstractCache;
use Bundle\AzureBundle\Cache\AzureCache;

class AzureCacheAdapter extends AbstractCache
{
    protected $cache = null;

    public function __construct(AzureCache $cache = null)
    {
        if ($cache) {
            $this->cache = $cache;
        } else {
            // @todo temporary hack to overcome DoctrineBundle limitation
            global $kernel;
            $this->cache = $kernel->getContainer()->get('azure.cache');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getIds()
    {
        throw new \Exception("Not implemented");
    }

    /**
     * {@inheritdoc}
     */
    protected function _doFetch($id)
    {
        return unserialize($this->cache->get($this->id($id)));
    }

    /**
     * {@inheritdoc}
     */
    protected function _doContains($id)
    {
        return $this->cache->get($this->id($id)) !== false;
    }

    /**
     * {@inheritdoc}
     */
    protected function _doSave($id, $data, $lifeTime = 0)
    {
        return $this->cache->store($this->id($id), serialize($data), $lifeTime);
    }

    /**
     * {@inheritdoc}
     */
    protected function _doDelete($id)
    {
        return $this->cache->delete($this->id($id));
    }

    /**
     * Get filtered ID supported by this adapter
     *
     * @param  $id
     * @return string
     */
    protected function id($id)
    {
        return str_replace(array('/', '\\', '$', '#'), array('*', '-', '+', '@'), $id);
    }
}
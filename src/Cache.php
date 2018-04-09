<?php

namespace snewer\cache;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use Psr\SimpleCache\CacheInterface;

/**
 * Class Cache
 * @package snewer\cache
 * @see https://www.php-fig.org/psr/psr-16/
 */
class Cache extends Component implements CacheInterface
{

    /**
     * @var string
     */
    public $baseCache = 'cache';

    /**
     * @throws InvalidConfigException
     * @return \yii\caching\CacheInterface
     */
    protected function _getBaseCache()
    {
        $component = Yii::$app->get($this->baseCache, true);
        if (!$component instanceof \yii\caching\Cache) {
            throw new InvalidConfigException('Component \'' . $this->baseCache . '\' must implement \yii\caching\CacheInterface interface.');
        }
        return $component;
    }

    /**
     * @inheritdoc
     */
    public function get($key, $default = null)
    {
        $value = $this->_getBaseCache()->get($key);
        return ($value === false) ? $default : $value;
    }

    /**
     * @inheritdoc
     */
    public function set($key, $value, $ttl = null)
    {
        return $this->_getBaseCache()->set($key, $value, $ttl);
    }

    /**
     * @inheritdoc
     */
    public function delete($key)
    {
        return $this->_getBaseCache()->delete($key);
    }

    /**
     * @inheritdoc
     */
    public function has($key)
    {
        return $this->_getBaseCache()->exists($key);
    }

    /**
     * @inheritdoc
     */
    public function getMultiple($keys, $default = null)
    {
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $this->get($key, $default);
        }
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function setMultiple($values, $ttl = null)
    {
        $success = true;
        foreach ($values as $key => $value) {
            $success = $success && $this->set($key, $value, $ttl);
        }
        return $success;
    }

    /**
     * @inheritdoc
     */
    public function deleteMultiple($keys)
    {
        $success = true;
        foreach ($keys as $key) {
            $success = $success && $this->delete($key);
        }
        return $success;
    }

    /**
     * @inheritdoc
     */
    public function clear()
    {
        return $this->_getBaseCache()->flush();
    }

}
<?php

namespace ATC;

class Config
{
    private static $instance;
    protected $originalContents = '';
    protected $settings;
    protected $adapter;

    protected function __construct() {}
    private function __clone() {}

    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new static();
            static::$instance->adapter = new ConfigAdapter\Ini('config');

            static::$instance->originalContents = $values = static::$instance->adapter->read();
            if (isset($values[APPLICATION_ENV]) && $values[APPLICATION_ENV] != 'production') {
                $values = array_merge($values['production'], $values[APPLICATION_ENV]);
            } else {
                $values = $values['production'];
            }
            static::$instance->settings = $values;
        }

        return static::$instance;
    }

    public function __get($name) {
        if (!isset($this->settings[$name])) {
            return '';
        }

        return $this->settings[$name];
    }

    public function get($name, $default = '') {
        if (!isset($this->settings[$name])) {
            return $default;
        }

        return $this->settings[$name];
    }

    public function updateSettings($settings) {
        $this->originalContents[APPLICATION_ENV] = array_merge($this->originalContents[APPLICATION_ENV], $settings);
        $this->adapter->write($this->originalContents);
    }
}
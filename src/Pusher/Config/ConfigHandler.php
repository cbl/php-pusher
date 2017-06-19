<?php

namespace PhpPusher\Config;

trait ConfigHandler
{

    protected $config;

    private $defaults;

    protected function setConfig($config) {
        $this->config = [];
        $this->setDefaults($config);
    }

    private function loadDefaults() {
        $this->defaults = require(__DIR__.'/defaults.php');
    }

    private function genConfig($config) {
        foreach($this->defaults as $type => $options) {
            $this->config[$type] = (isset($config[$type])) ? $config[$type] : [];
        }
    }

    private function setDefaults($config) {
        $this->loadDefaults();
        $this->genConfig($config);
        foreach($this->config as $type => $events) {
            foreach($events as $event => $options) {
                $this->loopDefaults($type, $event, $options);
            }
        }
    }

    private function loopDefaults($type, $event, $options) {
        foreach($this->defaults[$type] as $option => $value) {
            if(!isset($options[$option]))
                $options[$option] = $value;
        }
        $this->config[$type][$event] = $options;
    }
}

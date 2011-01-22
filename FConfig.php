<?php
/**
 * Author: Karagodin E.S. (ekaragodin@gmail.com)
 * Date: 22.01.11
 * Time: 13:58
 */

class FConfig extends CBehavior {

    public $configDir = 'application.config';
    public $configs = array();
    public $currentConfig = '';

    public function loadConfigure() {
        $app = $this->getOwner();

        if (isset($this->configs[$this->currentConfig])) {
            $config = require(Yii::getPathOfAlias($this->configDir . '.' . $this->currentConfig) . '.php');

            if (is_array($config)) {
                foreach ($config as $key => $value)
                    $app->$key = $value;
            }

        }

        return $app;
    }

}

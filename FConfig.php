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

    protected $chainConfigs = array();

    /**
     * Load configs from files and replace current configuration.
     * @return CApplication
     */
    public function loadConfigure() {
        $app = $this->getOwner();

        if (isset($this->configs[$this->currentConfig])) {
            $config = $this->loadChain($this->currentConfig);

            foreach ($config as $key => $value)
                $app->$key = $value;
        }

        return $app;
    }

    /**
     * Recursively loads the configuration files.
     * @param  $name
     * @return array
     */
    protected function loadChain($name) {
        if (isset($this->chainConfigs[$name])) {
            throw new CException('Config "' . $name . '" already load!');
        }

        $this->chainConfigs[$name] = $this->loadFromFile($name);
        $result = $this->chainConfigs[$name];

        if (isset($this->configs[$name]['parent'])) {
            $parentConfig = $this->loadChain($this->configs[$name]['parent']);
            $result = CMap::mergeArray($result, $parentConfig);
        }

        return $result;
    }

    /**
     * @param string $name file configuration.
     * @return array
     */
    protected function loadFromFile($name) {
        $config = require(Yii::getPathOfAlias($this->configDir . '.' . $name) . '.php');
        if (is_array($config))
            return $config;
        else
            return array();
    }

}

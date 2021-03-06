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
     * @param string|array $name
     * @return array
     */
    protected function loadChain($name) {
        $resultConfig = array();

        if (is_array($name)) {
            foreach($name as $n) {
                $resultConfig = CMap::mergeArray($resultConfig, $this->loadChain($n));
            }
            return $resultConfig;
        }

        if (isset($this->chainConfigs[$name])) {
            throw new CException(Yii::t('fconfig', 'Config "{name}" already load!',
                array('{name}' => $name)));
        }

        $this->chainConfigs[$name] = $this->loadFromFile($name);
        $resultConfig = $this->chainConfigs[$name];

        $localConfig = $this->loadFromFile($name . '_local');
        $resultConfig = CMap::mergeArray($resultConfig, $localConfig);

        if (isset($this->configs[$name]['parent'])) {
            $parentConfig = $this->loadChain($this->configs[$name]['parent']);
            $resultConfig = CMap::mergeArray($parentConfig, $resultConfig);
        }

        return $resultConfig;
    }

    /**
     * @param string $name file configuration.
     * @return array
     */
    protected function loadFromFile($name) {
        $path = Yii::getPathOfAlias($this->configDir . '.' . $name) . '.php';

        if (file_exists($path)) {
            $config = require($path);
            if (is_array($config))
                return $config;
        }

        return array();
    }

}

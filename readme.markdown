# Flexible Config

## Description

The extension is designed for flexible application configuration.
Represents the behavior for the СApplication class. Adds an object with one method - loadConfigure().

## Features

* Easy connection configuration file;
* Inheritance of configuration files;
* Automatic load config from *_local.php;

## Settings

* string FConfig::configDir - path to the configuration file, defaults to 'application.config';
* array FConfig::configs - array with a list of configurations, key - the name of the configuration, value - an array of configuration settings;
* string FConfig::currentConfig - the name of the current configuration;

Each configuration has a property 'parent' - is a string with the name of the parent configuration.

## Example

I need to have two different application configuration mode for the development and production server. Most of the settings they have the same, and some do not.
To do this, I have three files in the folder config: main.php - contains general settings for all configurations; dev.php - contains specific design-time configuration; production.php - contains settings specific to the production.

index.php file I have contains the following code:

    <?php

    $yii = 'yii/framework/yii.php';

    // remove the following lines when in production mode
    defined('YII_DEBUG') or define('YII_DEBUG', true);
    // specify how many levels of call stack should be shown in each log message
    defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', 3);

    require_once($yii);
    Yii::createWebApplication(array(
        'behaviors' => array(
            'fconfig' => array(
                'class' => 'ext.FlexibleConfig.FConfig',
                'currentConfig' => 'dev',
                'configs' => array(
                    'dev' => array(
                        'parent' => 'main',
                    ),
                    'production' => array(
                        'parent' => 'main',
                    ),
                ),
            ),
        ),
    ))->loadConfigure()->run();

In addition to the config folder is a file dev_local.php, which is automatically loaded after dev.php and overrides the necessary local settings.




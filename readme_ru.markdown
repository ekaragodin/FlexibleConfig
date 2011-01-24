# Flexible Config

## Описание

Расширение предназначено для гибкой конфигурации приложения.
Представляет собой поведение для класса CApplication. Добавляет объекту один метод - loadConfigure().

## Возможности

* Простое подключение файла конфигурации;
* Наследование файлов конфигурации;
* Автоматическое подключение конфигов вида *_local.php;

## Настройки

* string FConfig::configDir - путь к директории с файлами конфигураций, по умолчанию имеет значение 'application.config';
* array FConfig::configs - массив со списком конфигураций, ключ - это название конфигурации, значение - массив с настройками конфигурации;
* string FConfig::currentConfig - название текущей конфигурации;

Каждая конфигурация имеет свойство 'parent' - это строка с названием родительской конфигурации.

### Пример использования 1

Мне нужно иметь две разные конфигурации приложения для режима разработки и для рабочего сервера. Большая часть настроек у них совпадает, а часть нет.
Для этого у меня есть три файла в папке config: main.php - содержит общие для всех конфигураций настройки; dev.php - содержит специфичные для режима разработки настройки; production.php - содержит настройки специфичные для рабочего режима.

Файл index.php у меня содержит следующий код:


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


Кроме этого в папке config есть файл dev_local.php, который автоматически загружается после dev.php и переопределяет необходимые локальные настройки.

### Пример использования 2

index.php:


    <?php

    $yii='yii/framework/yii.php';
    $config=include(dirname(__FILE__).'/protected/config/main.php');

    require_once($yii);
    Yii::createWebApplication($config)->loadConfigure()->run();

config/main.php:


    <?php

    if (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
        defined('YII_DEBUG') or define('YII_DEBUG',true);
        defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);
        define('YII_ENV', 'dev');
    } else {
        defined('YII_DEBUG') or define('YII_DEBUG',false);
        defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',1);
        define('YII_ENV', 'production');
    }

    return array(
        'name'=>'My Web Application',

        'behaviors' => array(
            'fconfig' => array(
                'class' => 'ext.FlexibleConfig.FConfig',
                'currentConfig' =>  YII_ENV,
                'configs' => array(
                    'dev' => array(),
                    'production' => array(),
                    'test' => array(),
                ),
            ),
        ),

        'params'=>array(
            'adminEmail'=>'ekaragodin@gmail.com',
        ),
    );



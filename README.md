# API Server

## Описание

APIServer - это библиотекка для создания структурированных API Server на PHP. Также эта библиотека используеться во всех проектах RuJect, где есть **Application Programming Interface (API)**

## Установка

Если у вас есть composer, то просто установите библиотеку используя команду:

```cmd
composer require ruject/apiserver
```

## Примеры

### Наипростейший API Server

```PHP
use Ruject\APIServer\API;

$api = new API("MainAPI", "release", "1.0");

class EchoMethod extends APIMethod {
    public static string $name = "EchoMethod";
    public static string $description = "";
    public static string $version = "";
    public static array  $params = [
        ["text", "hello world", true, "string"]
    ];
    public static array  $synonyms = ["Echo_Method"];

    public static function call(...$params): APIResponse
    {
        return new APIResponse($params['text']);
    }
}

$api->run();
```

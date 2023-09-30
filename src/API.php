<?php

namespace RuJect\APIServer;

use RuJect\APIServer\APIMethod;
use RuJect\APIServer\APIParameter;
use RuJect\APIServer\APIResponse;

/** Класс для инициализации API сервера */
class API
{
    /** Имя сервера */
    public string $name    = "{API->NAME}";
    /** Тип версии сервера */
    public string $type    = "{API->TYPE}";
    /** Версия сервера */
    public string $version = "{API->VERSION}";

    /** Параметер, который должен быть передан в запросе для вызова метода. `$_REQUEST[$method_parameter]`*/
    public string $method_parameter = "method";
    /** Метод вызываемый, когда `$method_parameter` не передан */
    public string $method_default = "";
    /** Методы которые нельзя вызывать */
    public array  $methods_blocked = ["Method_Exists", "Method_Info"];

    public bool $is_debug = false;


    /**
     * Инициализирует API сервер
     * @param string $name Имя API сервера
     * @param string $type Тип версии API сервера. Например: `stable`, `beta`, `alpha`
     * @param string $version Версия API сервера. Например: `1.0`, `1`, `0x0AB489F`, `30092023`
     * @return API
     */
    public function __construct($name, $type, $version, $debug = false)
    {
        $this->name    = $name;
        $this->type    = $type;
        $this->version = $version;
    }

    public function run()
    {
        if (!$this->is_debug) {
            error_reporting(0);
        }
        $this->execute($_REQUEST);
    }

    /** Выполняет запрос
     * @param array $REQUEST Массив параметров. Например: `$_REQUEST`, `$_GET`, `$_POST`
     */
    public function execute($REQUEST)
    {
        if (!isset($REQUEST[$this->method_parameter])) {
            if (isset($this->method_default)) {
                $response = ($this->method_default)::call();
            } else {
                $response = new APIResponse(false, "Method not passed");
            }
        } else {
            $method = $REQUEST[$this->method_parameter];
            unset($REQUEST[$this->method_parameter]);
            $params = $REQUEST;

            if ($this->method_exists($method)) {
                $response = self::method_call($method, $params);
            } else {
                $response = new APIResponse(false, "Method not found");
            }
        }

        // Проверка спец параметров
        //  Формат возвращаемых данных
        if (isset($params['_format'])) {
            $format = strtolower($params['_format']);
            unset($params['_format']);
        } else {
            $format = "json";
        }
        
        //  Данные вернуться в виде файла
        if (isset($params['_download'])) {
            $download = (bool)$params['_download'];
            unset($params['_download']);
        }
        else {
            $download = false;
        }

        // Выполнение метода
        switch ($format) {
            case "json":
                header("Content-Type: application/json");
                $result = $response->toJSON();
                break;
            case "php":
                header("Content-Type: text/plain");
                $result = $response->toPHP();
                break;
            case "xml":
                header("Content-Type: text/xml");
                $result = $response->toXML();
                break;
            default:
                $result = $response->toJSON();
                break;
        }

        if ($download) {
            header("Content-Disposition: attachment; filename=\"{$this->name}.{$format}\"");
            header("Content-Length: " . strlen($result));
            echo $result;
        }
        else {
            echo $result;
        }
    }

    /**
     * Вызывает указанный метод. Где метод должен быть дочерним классом APIMethod
     * @param string $name Имя вызываемого метода
     * @param array  $parameters_input Параметры вызываемого метода
     * @return APIResponse
     */
    public function method_call(string $name, array $parameters_input): APIResponse
    {
        if (!$this->method_exists($name) and !in_array($name, $this->methods_blocked)) {
            return new APIResponse(false, "Method not found");
        }
        $parameters_converted = [];
        $method = $this->method_get($name);
        foreach ($method::$params as [$name, $default, $optional, $type]) {
            if (!isset($parameters_input[$name])) {
                if (!$optional) {
                    return new APIResponse(false, "Missing parameter '" . $name . "'");
                }
                $parameters_converted[$name] = $default;
            } else {
                $parameters_converted[$name] = APIParameter::convert($parameters_input[$name], $type);
            }
        }

        foreach ($parameters_input as $key => $value) {
            if (!isset($parameters_converted[$key]) && !str_starts_with($key, "_")) {
                return new APIResponse(false, "Extra parameter '" . $key . "'");
            }
        }

        return $method::call(...$parameters_converted);
    }

    /** Делает поиск класса метода
     * @param string $name Имя или синоним искомого метода
     * @return string|bool Имя класса метода, или если он не найден то `false`
     */
    public function method_get(string $name): string|bool
    {
        foreach (get_declared_classes() as $class) {
            if (get_parent_class($class) === 'APIMethod' and in_array($name, $class::$synonyms)) {
                return $class;
            }
        }
        return false;
    }

    /** Проверяет наличие метода
     * @param string $name Имя метода
     * @return bool `true` если метод найден, иначе `false`
     */
    public function method_exists(string $name): bool
    {
        foreach (get_declared_classes() as $class) {
            if (get_parent_class($class) === 'APIMethod' and in_array($name, $class::$synonyms)) {
                return true;
            }
        }
        return false;
    }

    /** Возвращает информацию о методе
     * @param string $name Имя метода
     * @return array Информация о методе
     */
    public function method_info(string $name): array
    {
        if (!$this->method_exists($name)) {
            return [];
        } else {
            $method = $this->method_get($name);
            $result = [
                "name" => $method::$name,
                "description" => $method::$description,
                "version" => $method::$version,
                "params" => [],
                "synonyms" => $method::$synonyms,
            ];
            foreach ($method::$params as [$name, $default, $optional, $type]) {
                $result['result']['parameters'][] = [
                    "name" => $name,
                    "type" => $type->name,
                    "default_value" => $default,
                    "is_optional" => $optional,
                ];
            }
            return $result;
        }
    }

    /** Выключает режим отладки */
    public function debugDisable()
    {
        $this->is_debug = false;
    }

    /** Включает режим отладки */
    public function debugEnable()
    {
        $this->is_debug = true;
    }
}

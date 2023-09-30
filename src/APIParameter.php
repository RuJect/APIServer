<?php

namespace RuJect\APIServer;

/** Класс для параметров метода */
class APIParameter
{
    /** Имя параметра */
    public string $name;
    /** Значение по умолчанию. Если `null` то значение по умолчанию не задано */
    public mixed $default = null;
    /** Является ли параметр обязательным. `true` - не обязательно, `false` - обязательно */
    public bool $optional = false;
    /** Тип параметра */
    public string $type = "string";

    public static array $types = ["string", "int", "float", "bool", "array"];

    /**
     * @param string $name Имя параметра
     * @param mixed $default Значение по умолчанию. Если `null` то значение по умолчанию не задано
     * @param bool $optional Является ли параметр обязательным. `true` - не обязательно, `false` - обязательно
     * @param string $type Тип параметра
     */
    public function __construct(string $name, mixed $default, bool $optional, string $type)
    {
        $this->name = $name;
        $this->default = $default;
        $this->optional = $optional;
        if (in_array($type, self::$types)) {
            $this->type = $type;
        }
    }

    /** Конвертировать параметр из строкового типа в нужный тип
     * @param string $value Строковое значение
     * @param string $type Нужный тип
    */
    public static function convert(string $value, string $type): mixed
    {
        switch ($type) {
            case "string":
                if (is_string((string)$value)) {
                    return (string)$value;
                }
                return "";
            case "int":
                if (is_int((int)$value)) {
                    return (int)$value;
                }
                return 0;
            case "float":
                if (is_float((float)$value)) {
                    return (float)$value;
                }
                return 0.0;
            case "bool":
                if (is_bool((bool)$value)) {
                    return (bool)$value;
                }
                return false;
            case "array":
                if (is_array(json_decode($value))) {
                    return json_decode($value);
                }
                return [];
            default:
                return $value;
        }
    }
}


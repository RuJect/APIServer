<?php

namespace RuJect\APIServer;

/** Класс для создания методов */
abstract class APIMethod
{
    /** Имя метода */
    public static string $name = "";
    /** Описание метода */
    public static string $description = "";
    /** Версия метода */
    public static string $version = "";
    /** Параметры метода в формате: `[string $name, mixed $default_value, bool $is_optional, string $type] */
    public static array  $params = [];
    /** Синонимы метода, которыми можно его вызвать через `(new API)->run()` */
    public static array  $synonyms = [];

    /** Функция для вызова метода */
    abstract public static function call(...$params): APIResponse;
}
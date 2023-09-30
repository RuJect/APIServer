<?php

namespace RuJect\APIServer;

use SimpleXMLElement;

/** Класс для генерации ответов сервера */
class APIResponse
{
    /** Статус ответа. true - все ок, false - ошибка */
    protected bool $status;
    /** Сообщение. Например может содержать описание результата или его тип */
    protected string $message;
    /** Результат */
    protected mixed $result;

    /**
     * @param bool $status Статус ответа. true - все ок, false - ошибка
     * @param string $message Сообщение. Например может содержать описание результата или его тип
     * @param mixed $result результат
     */
    public function __construct(bool $status, string $message, mixed $result = null)
    {
        $this->status = $status;
        $this->message = $message;
        $this->result = $result;
    }

    /** Возвращает ответ в виде массива
     * @return array Массив с результатом
     */
    public function get(): array
    {
        return ["status" => $this->status, "message" => $this->message, "result" => $this->result];
    }

    /** Возвращает ответ в JSON формате */
    public function toJSON(): string
    {
        return json_encode($this->get());
    }

    /** Возвращает ответ в PHP формате */
    public function toPHP(): string
    {
        return var_export($this->get(), true);
    }

    /** Возвращает ответ в XML формате */
    public function toXML(): string
    {
        $xml_data = new SimpleXMLElement('<xml/>');
        $this->array_to_xml($this->get(), $xml_data);
        return $xml_data->asXML();
    }

    /** Внутренняя функция для преобразования массива в XML
     * @param array $data Массив для преобразования
     * @param SimpleXMLElement $xml_data XML дерево
     */
    protected function array_to_xml($data, &$xml_data)
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                if (is_numeric($key)) {
                    $key = 'item' . $key; //dealing with <0/>..<n/> issues
                }
                $subnode = $xml_data->addChild($key);
                $this->array_to_xml($value, $subnode);
            } else {
                $xml_data->addChild("$key", htmlspecialchars("$value"));
            }
        }
    }

    public function __toString()
    {
        return $this->toJSON();
    }
}

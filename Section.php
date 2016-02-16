<?php

namespace apexwire\parser\file1c;

/**
 * Class Section Родительский класс для секций документа 1С
 * @package apexwire\parser\file1c
 */

class Section
{
    /** Константа начала секции */
    const START = '';
    /** Константа конца секции */
    const FINISH = '';

    /** Свойства */
    /** @type array Массив секций */
    public static $sections = [
        'apexwire\parser\file1c\section\СheckingAccount',
        'apexwire\parser\file1c\section\Document',
    ];
    /** @type array массив ошибок */
    protected $errors = [];
    /** @type array Массив свойств стандартных и нестандартных */
    protected $properties = [];
    /** @type array Список возможных свойств */
    protected static $propertiesSettings = [];
    /** @type bool Состояние секции */
    protected $success = true;

    /**
     * Создаем объект секции
     *
     * @param $name
     * @param $data
     * @return Section
     */
    public static function create($name, $data)
    {
        try {
            $obj = new $name($data);
        } catch (\Exception $e) {
            $obj = new self($data);
            $obj->addError('Класса ' . $name . ' не существует!');
        }

        return $obj;
    }

    /**
     * Возвращаем имя класса объекта сессии по строке начала сессии
     *
     * @param $start
     * @return mixed
     */
    public static function getNameByStartStr($start)
    {
        foreach (self::$sections as $section) {
            if ($section::START == $start) {
                return $section;
            }
        }

        return false;
    }

    /**
     * Возвращаем имя класса объекта сессии по строке конца сессии
     *
     * @param $finish
     * @return mixed
     */
    public static function getNameByFinishStr($finish)
    {
        foreach (self::$sections as $section) {
            if ($section::FINISH == $finish) {
                return $section;
            }
        }

        return false;
    }

    /**
     * Section constructor.
     * @param $data
     */
    protected function __construct($data)
    {
        $this->init($data);
    }

    /**
     * Инициализация
     *
     * @param $data
     */
    protected function init($data)
    {
        //добавляем свойства
        foreach ($data as $key => $value) {
            $this->addProperty($key, $value);
        }

        //выполняем проверку свойств
        foreach (static::$propertiesSettings as $propertyName => $propertySettings) {
            // проверка на обязательность
            if ($propertySettings['required'] AND !isset($this->properties['standart'][$propertyName])) {
                $this->addError("В секции не обнаружено обязательное поле '$propertyName'");
            }
        }
    }

    /**
     * Добавление свойства
     *
     * @param $key
     * @param $value
     */
    protected function addProperty($key, $value)
    {
        $list = static::$propertiesSettings;
        $label = isset($list[$key]) ? 'standart' : 'additional';
        $this->properties[$label][$key] = $value;
    }

    /**
     * Добавление сообщения об ошибке
     *
     * @param $text
     */
    protected function addError($text)
    {
        $this->success = false;
        $this->errors[] = $text;
    }

    /**
     * Функции для предоставления информации об объекте и его свойствах
     *
     * @return mixed
     */
    public function getStandartProperties()
    {
        return $this->properties['standart'];
    }

    /**
     * Получаем значение свойства
     *
     * @param $name
     * @return mixed
     */
    public function getProperty($name)
    {
        if (isset(static::$propertiesSettings[$name])) {
            return $this->properties['standart'][$name];
        }
        //TODO: сделать обработку отсутствия запроашиваемого свойства в конфиге
    }

    /**
     * Возвращает массив со значениями свойств и новыми ключами
     *
     * @param array $arKeys
     * @return array
     */
    public function getCorrespondingProperties(array $arKeys)
    {
        if (empty($arKeys)) {
            return [];
        }
        $arProperties = [];
        foreach ($arKeys as $propertyKey => $propertyValue) {
            $arProperties[$propertyKey] = $this->getProperty($propertyValue);
        }

        return $arProperties;
    }

    /**
     * Возвращаем список ошибок
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Информация о состоянии объекта документа
     *
     * @return bool
     */
    public function isSuccessful()
    {
        return $this->success;
    }
}
<?php

namespace apexwire\parser1c;

use apexwire\parser1c\section\Section;

/**
 * Class Parser
 * @package apexwire\parser1c
 */
abstract class Parser
{
    /** Название свойтсва отвечающее за кодировку данных */
    const ENCODING = 'Кодировка';
    /** Строка обозначающая конец файла */
    const END_FILE = 'КонецФайла';

    /** Свойства */
    /** @type string Заголовок файла */
    private $header = '';
    /** @type string Конец файла */
    private $end = '';
    /** @type string Кодировка файла */
    private $encoding = '';
    /** @type string Свойство "кодировка" в кодировке файла */
    private $encodingLabel = '';


    /** @type array Массив ошибок */
    private $errors = [];
    /** @type bool Успешность обработки документа */
    private $success = true;
    /** @type array Свойства документа */
    private $properties = [];
    /** @type array объекты секций */
    private $sections = [];
    /** @type bool Флаг начала секции для обработки */
    private $sectionFlag = false;

    /** @type array Временный массив с данными секции */
    private $tmpSection = [];

    /** @type array Массив свойств */
    private static $propertiesSettings = [
        'ВерсияФормата' => [
            'required' => true,
            'type' => 'строка',
        ],
        'Кодировка' => [
            'required' => true,
            'type' => 'строка',
        ],
        'Отправитель' => [
            'required' => false,
            'type' => 'строка',
        ],
        'Получатель' => [
            'required' => true,
            'type' => 'строка',
        ],
        'ДатаСоздания' => [
            'required' => false,
            'type' => 'дд.мм.гггг',
        ],
        'ВремяСоздания' => [
            'required' => false,
            'type' => 'чч:мм:сс',
        ],
        'ДатаНачала' => [
            'required' => true,
            'type' => 'дд.мм.гггг',
        ],
        'ДатаКонца' => [
            'required' => true,
            'type' => 'дд.мм.гггг',
        ],
        'РасчСчет' => [
            'required' => true,
            'type' => '20',
        ]
    ];

    /**
     * Parser constructor.
     * @param $value
     */
    protected function __construct($value)
    {
        $this->header = $this->getHeader();
        list($this->encodingLabel, $this->encoding) = $this->getEncoding();

        $this->end = $this->convert($this->getEnd());

        $this->validation();
        $this->parse();
        $this->checkProperty();
    }

    /**
     * Получаем заголовок файла
     *
     * @return mixed
     */
    abstract protected function getHeader();

    /**
     * Получаем кодировку файла
     *
     * @return mixed
     */
    abstract protected function getEncoding();

    /**
     * Получаем последную строку файла
     *
     * @return mixed
     */
    abstract protected function getEnd();

    /**
     * Проверка
     *
     * @throws \Exception
     */
    private function validation()
    {
        // TODO: валидация всего файла

        //Проверка первой строки файла
        if ($this->header !== '1CClientBankExchange') {
            throw new \Exception('Требуется указать заголовок файла "1CClientBankExchange" в строке №1!');
        }

        if ($this->encoding !== 'Windows' AND $this->encoding !== 'DOS') {
            throw new \Exception('Требуется указать корректную кодировку в строке №3!');
        }

        //Проверка последней строки файла
        if ($this->end !== self::END_FILE) {
            throw new \Exception('Требуется указать признак конца файла!');
        }
    }

    /**
     * Парсинг данных
     *
     * @return mixed
     */
    abstract protected function parse();

    /**
     * Проверка свойств
     */
    private function checkProperty()
    {
        //TODO: выполняем проверку объекта документа
        // выполняем валидацию свойств
        foreach (self::$propertiesSettings as $propertyName => $propertySettings) {
            // проверка на обязательность
            if ($propertySettings['required'] AND !isset($this->properties['standart'][$propertyName])) {
                $this->addError('В документе не обнаружено обязательное поле ' . $propertyName);
            }
        }
        //TODO: специальные проверки свойств документа (версия, кодировка)
        //TODO: проверка соответствия секций документа
        //TODO: проверка соответствия списка рассчётных счетов
    }


    /**
     * Возвращаем строку в utf-8
     *
     * @param $text
     * @return string
     */
    protected function convert($text)
    {
        if ($this->encoding === 'Windows'
            AND (mb_convert_encoding($this->encodingLabel, 'utf-8', 'windows-1251') === self::ENCODING)
        ) {
            $text = mb_convert_encoding($text, 'utf-8', 'windows-1251');
        } elseif ($this->encoding === 'DOS'
            AND (mb_convert_encoding($this->encodingLabel, 'utf-8', 'cp866') === self::ENCODING)
        ) {
            $text = mb_convert_encoding($text, 'utf-8', 'cp866');
        }


        return $text;
    }

    /**
     * Обработка строки
     *
     * @param $str
     */
    protected function processing($str)
    {
        $arr = explode('=', $this->convert($str));
        $key = $arr[0];
        $value = '';
        if (isset($arr[1])) {
            $value = $arr[1];
        }

        if ($this->sectionFlag) {// если обрабатываем секцию
            if (!$this->checkSectionEnd($key)) { // проверяем секции на закрытие
                // иначе добавляем в массив секции
                $this->tmpSection[$key] = $value;
            }
        } else { // если обрабатываем строку вне секции
            if (!$this->checkSectionStart($key)) { // проверяем её на начало секции
                // иначе пытаемся добавить её в свойство документа
                $this->addProperty($key, $value);
            }
            else
            {
                $this->tmpSection[$key] = $value;
            }
        }
    }

    /**
     * Добавление сообщения об ошибке
     *
     * @param $text
     */
    private function addError($text)
    {
        $this->success = false;
        $this->errors[] = $text;
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
     * Добавление свойства
     *
     * @param $key
     * @param $value
     */
    protected function addProperty($key, $value)
    {
        $label = isset(self::$propertiesSettings[$key]) ? 'standart' : 'additional';
        $this->properties[$label][$key] = $value;
    }

    /**
     * Функции секции
     */

    /**
     * Проверка начала секции
     *
     * @param $key
     * @return bool
     */
    private function checkSectionStart($key)
    {
        if (Section::getNameByStartStr($key) !== false) { // если в настройках есть такая открывающая строка
            // устанавливаем флаг обработки секции
            $this->sectionFlag = true;

            // TODO: добавить возможность выбора вида документа
            return true;
        }

        return false;
    }

    /**
     * Проверка окончания секции
     *
     * @param $key
     * @return bool
     */
    private function checkSectionEnd($key)
    {
        if (($sectionClass = Section::getNameByFinishStr($key)) !== false) {
            $this->addSectionObject($sectionClass, $this->tmpSection);
            // сбрасываем флаг обработки секции
            $this->sectionFlag = false;
            // освобождаем временный массив секции
            $this->tmpSection = [];

            return true;
        }

        return false;
    }

    /**
     * Добавление объекта секции
     *
     * @param string $sectionClass
     * @param array $sectionData
     */
    private function addSectionObject($sectionClass, $sectionData)
    {
        $sectionObject = Section::create($sectionClass, $sectionData);
        $this->sections[get_class($sectionObject)][] = $sectionObject;
        // если у объекта имеются ошибки, добавить их к объекту документа
        if (!empty($sectionErrors = $sectionObject->getErrors())) {
            foreach ($sectionErrors as $sectionError) {
                $this->addError('Сообщение секции ' . get_class($sectionObject) . ': ' . $sectionError);
            }
        }
    }

    /**
     * Возвращаем массив секций
     *
     * @param $type
     * @return array
     */
    public function getSections($type = null)
    {
        if ($type !== null) {
            if (array_search($type, Section::$sections)) {
                return $this->sections[$type];
            } else {
                return [];
            }
        } else {
            return $this->sections;
        }
    }

    public function getProperties()
    {
        return $this->properties;
    }
}

<?php

namespace apexwire\parser\file1c;

/**
 * Class Parser
 * @package apexwire\parser\file1c
 */
class Parser
{
    /** Свойства */
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

    /** @type array Временный массив с данными секции */
    private $tmpSection = [];

    /**
     * Возвращаем объект на основе данных из файла
     *
     * @param $path
     * @return Parser
     */
    public static function createFromFile($path)
    {
        $text = file_get_contents($path);

        return self::create($text);
    }

    /**
     * Возвращаем объект на основе текста
     *
     * @param $text
     * @return Parser
     */
    public static function create($text)
    {
        //парсим "шапку", узнаем кодировку файла
        $rows = explode("\n", str_replace("\r\n", "\n", $text));
        $tmpInput = [];
        foreach ($rows as $row) {
            if ($row !== '') {
                $tmpInput[] = explode('=', $row);
            }

            if (count($tmpInput) > 2) {
                break;
            }
        }

        $error = '';
        // работаем с кодировкой
        if (($tmpInput[2][1] !== 'Windows') AND $tmpInput[2][1] !== 'DOS') {
            $error = 'Требуется указать корректную кодировку в строке №3';
        } else {
            if ($tmpInput[2][0] !== 'Кодировка') {
                if ($tmpInput[2][1] === 'Windows'
                    AND (mb_convert_encoding($tmpInput[2][0], 'utf-8', 'windows-1251') === 'Кодировка')
                ) {
                    $text = mb_convert_encoding($text, 'utf-8', 'windows-1251');
                } elseif ($tmpInput[2][1] === 'DOS'
                    AND (mb_convert_encoding($tmpInput[2][0], 'utf-8', 'cp866') === 'Кодировка')
                ) {
                    $text = mb_convert_encoding($text, 'utf-8', 'cp866');
                } else {
                    $error = 'Кодировка файла указана неверно';
                }
            } else {

            }
        }


        $doc = new self($text);
        if ($error != '') {
            $doc->addError($error);
        }

        return $doc;
    }

    private function __construct($text)
    {
        $rows = $this->init($text);
        $this->validation($rows);
    }

    /**
     * Инициализация
     *
     * @param string $text
     * @return array
     */
    private function init($text)
    {
        // TODO: определение переноса строки
        // создаём для каждой строки массив
        $text = str_replace("\r\n", "\n", $text);
        $rows = explode("\n", $text);
        $input = [];
        foreach ($rows as $row) {
            if ($row !== '') {
                $input[] = explode('=', $row);
            }
        }

        // построчный разбор содержимого документа
        for ($i = 1; $i < count($input) - 1; ++$i) {
            if ($this->sectionFlag) {// если обрабатываем секцию
                if (!$this->checkSectionEnd($input[$i])) { // проверяем секции на закрытие
                    // иначе добавляем в массив секции
                    $this->tmpSection[$input[$i][0]] = $input[$i][1];
                }
            } else { // если обрабатываем строку вне секции
                if (!$this->checkSectionStart($input[$i])) { // проверяем её на начало секции
                    // иначе пытаемся добавить её в свойство документа
                    $this->addProperty($input[$i][0], $input[$i][1]);
                }
            }
        }

        return $input;
    }

    /**
     * Проверка текста
     *
     * @param array $rows Массив строк файла
     */
    private function validation($rows)
    {
        // TODO: валидация всего файла

        //Проверка первой строки файла
        if ($rows[0][0] !== '1CClientBankExchange') {
            $this->addError('Требуется заголовок файла 1CClientBankExchange');
        }

        //Проверка последней строки файла
        if ($rows[count($rows) - 1][0] !== 'КонецФайла') {
            $this->addError('Требуется указать признак конца файла');
        }


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
     * Проверка начала секции
     *
     * @param $row
     * @return bool
     */
    private function checkSectionStart($row)
    {
        if (Section::getNameByStartStr($row[0]) !== false) { // если в настройках есть такая открывающая строка
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
     * @param $row
     * @return bool
     */
    private function checkSectionEnd($row)
    {
        if (($sectionClass = Section::getNameByFinishStr($row[0])) !== false) {
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
}
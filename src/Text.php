<?php

namespace apexwire\parser1c;

/**
 * Class Text Парсим данные, переданные в текстовом виде
 * @package apexwire\parser1c
 */
class Text extends Parser
{
    /** @type array Массив строк */
    private static $rows = [];

    /**
     * Text constructor.
     * @param $text
     */
    public function __construct($text)
    {
        //TODO: определение переноса строки
        self::$rows = explode("\n", str_replace("\r\n", "\n", $text));

        parent::__construct($text);
    }

    /**
     * Получаем заголовок файла
     *
     * @return string
     * @throws \Exception
     */
    protected function getHeader()
    {
        return trim(current(self::$rows));
    }

    /**
     * Получаем кодировку файла
     *
     * @return string
     * @throws \Exception
     */
    protected function getEncoding()
    {
        return explode("=", trim(self::$rows[2]));
    }

    /**
     * Получаем последную строку файла
     *
     * @return mixed
     */
    protected function getEnd()
    {
        return trim(end(self::$rows));
    }

    /**
     * Парсинг данных
     *
     * @return mixed
     */
    protected function parse()
    {
        foreach (self::$rows as $row) {
            $this->processing(trim($row));
        }
    }
}
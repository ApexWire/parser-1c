<?php

namespace apexwire\parser1c;

/**
 * Class File Парсим данные из файла
 * @package apexwire\parser1c\parser
 */
class File extends Parser
{
    /** @type string Полный путь к файлу */
    private $path = '';

    /**
     * File constructor.
     * @param $value
     */
    public function __construct($value)
    {
        $this->path = $value;

        parent::__construct($value);
    }

    /**
     * Получаем заголовок файла
     *
     * @return string
     * @throws \Exception
     */
    protected function getHeader()
    {
        $header = '';
        $handle = @fopen($this->path, "r");
        if ($handle) {
            while (($buffer = fgets($handle)) !== false) {
                $header = trim($buffer);
                break;
            }
            fclose($handle);
        }

        return $header;
    }

    /**
     * Получаем кодировку файла
     *
     * @return string
     * @throws \Exception
     */
    protected function getEncoding()
    {
        $i = 0;
        $encoding = ['', ''];
        $handle = @fopen($this->path, "r");
        if ($handle) {
            while (($buffer = fgets($handle, 4096)) !== false) {

                if ($i == 2) {

                    $encoding = explode('=', trim($buffer));
                    break;
                }

                $i++;
            }
            fclose($handle);
        }

        return $encoding;
    }

    /**
     * Получаем последную строку файла
     *
     * @return mixed
     */
    protected function getEnd()
    {
        $end = '';
        $handle = @fopen($this->path, "r");
        if ($handle) {

            $stat = fstat($handle);
            fseek($handle, $stat['size'] - strlen(self::END_FILE));

            while (($buffer = fgets($handle)) !== false) {

                $end = trim($buffer);
            }
            fclose($handle);
        }

        return $end;
    }

    /**
     * Парсинг данных
     *
     * @return mixed
     */
    protected function parse()
    {
        $handle = @fopen($this->path, "r");
        if ($handle) {
            while (($buffer = fgets($handle)) !== false) {
                $this->processing(trim($buffer));
            }
            if (!feof($handle)) {
                echo "Error: unexpected fgets() fail\n";
            }
            fclose($handle);
        }
    }
}
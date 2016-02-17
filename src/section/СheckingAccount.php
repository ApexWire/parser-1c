<?php

namespace apexwire\parser1c\section;

/**
 * Class СheckingAccount Секция "СекцияРасчСчет"
 * @package apexwire\parser1c\section
 */
class СheckingAccount extends Section
{
    /** Константа начала секции */
    const START = 'СекцияРасчСчет';
    /** Константа конца секции */
    const FINISH = 'КонецРасчСчет';

    /** @type array Список возможных свойств */
    protected static $propertiesSettings = [
        'ДатаНачала' => [
            'required' => true,
            'type' => 'дд.мм.гггг',
        ],
        'ДатаКонца' => [
            'required' => false,
            'type' => 'дд.мм.гггг',
        ],
        'РасчСчет' => [
            'required' => true,
            'type' => '20',
        ],
        'НачальныйОстаток' => [
            'required' => true,
            'type' => 'руб[.коп]',
        ],
        'ВсегоПоступило' => [
            'required' => false,
            'type' => 'руб[.коп]',
        ],
        'ВсегоСписано' => [
            'required' => false,
            'type' => 'руб[.коп]',
        ],
        'КонечныйОстаток' => [
            'required' => false,
            'type' => 'руб[.коп]',
        ],
    ];
}
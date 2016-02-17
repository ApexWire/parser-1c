<?php

namespace apexwire\parser1c\section;

/**
 * Class Document Секция "СекцияДокумент"
 * @package apexwire\parser1c\section
 */
class Document extends Section
{
    /** Константа начала секции */
    const START = 'СекцияДокумент';
    /** Константа конца секции */
    const FINISH = 'КонецДокумента';

    /** @type array Список возможных свойств */
    protected static $propertiesSettings = [
        // Шапка платежного документа
        'Номер' => [
            'required' => true,
            'type' => 'строка',
        ],
        'Дата' => [
            'required' => true,
            'type' => 'дд.мм.гггг',
        ],
        'Сумма' => [
            'required' => true,
            'type' => 'руб[.коп]',
        ],

        // Квитанция по платежному документу
        'КвитанцияДата' => [
            'required' => false,
            'type' => 'дд.мм.гггг',
        ],
        'КвитанцияВремя' => [
            'required' => false,
            'type' => 'чч:мм:сс',
        ],
        'КвитанцияСодержание' => [
            'required' => false,
            'type' => 'строка',
        ],

        // Реквизиты плательщика
        'ПлательщикСчет' => [
            'required' => true,
            'type' => '20',
        ],
        'ДатаСписано' => [
            'required' => true,
            'type' => 'дд.мм.гггг',
        ],
        'Плательщик' => [
            'required' => false,
            'type' => 'строка',
        ],
        'ПлательщикИНН' => [
            'required' => true,
            'type' => '12',
        ],

        // В случае непрямых расчетов:
        'Плательщик1' => [
            'required' => false,
            'type' => 'строка',
        ],
        'Плательщик2' => [
            'required' => false,
            'type' => 'строка',
        ],
        'Плательщик3' => [
            'required' => false,
            'type' => 'строка',
        ],
        'Плательщик4' => [
            'required' => false,
            'type' => 'строка',
        ],
        'ПлательщикРасчСчет' => [
            'required' => false,
            'type' => '20',
        ],
        'ПлательщикБанк1' => [
            'required' => false,
            'type' => 'строка',
        ],
        'ПлательщикБанк2' => [
            'required' => false,
            'type' => 'строка',
        ],
        'ПлательщикБИК' => [
            'required' => false,
            'type' => '9',
        ],
        'ПлательщикКорсчет' => [
            'required' => false,
            'type' => '20',
        ],

        // Реквизиты получателя
        'ПолучательСчет' => [
            'required' => true,
            'type' => '20',
        ],
        'ДатаПоступило' => [
            'required' => true,
            'type' => 'дд.мм.гггг',
        ],
        'Получатель' => [
            'required' => false,
            'type' => 'строка',
        ],
        'ПолучательИНН' => [
            'required' => true,
            'type' => '12',
        ],

        // В случае непрямых расчетов:
        'Получатель1' => [
            'required' => false,
            'type' => 'строка',
        ],
        'Получатель2' => [
            'required' => false,
            'type' => 'строка',
        ],
        'Получатель3' => [
            'required' => false,
            'type' => 'строка',
        ],
        'Получатель4' => [
            'required' => false,
            'type' => 'строка',
        ],
        'ПолучательРасчСчет' => [
            'required' => false,
            'type' => '20',
        ],
        'ПолучательБанк1' => [
            'required' => false,
            'type' => 'строка',
        ],
        'ПолучательБанк2' => [
            'required' => false,
            'type' => 'строка',
        ],
        'ПолучательБИК' => [
            'required' => false,
            'type' => '9',
        ],
        'ПолучательКорсчет' => [
            'required' => false,
            'type' => '20',
        ],

        // Реквизиты платежа
        'ВидПлатежа' => [
            'required' => false,
            'type' => 'строка',
        ],
        'ВидОплаты' => [
            'required' => false,
            'type' => '2',
        ],
        'Код' => [
            'required' => false,
            'type' => 'Макс. 25',
        ],
        'НазначениеПлатежа' => [
            'required' => false,
            'type' => 'строка',
        ],
        'НазначениеПлатежа 1' => [
            'required' => false,
            'type' => 'строка',
        ],
        'НазначениеПлатежа 2' => [
            'required' => false,
            'type' => 'строка',
        ],
        'НазначениеПлатежа 3' => [
            'required' => false,
            'type' => 'строка',
        ],
        'НазначениеПлатежа 4' => [
            'required' => false,
            'type' => 'строка',
        ],
        'НазначениеПлатежа 5' => [
            'required' => false,
            'type' => 'строка',
        ],
        'НазначениеПлатежа 6' => [
            'required' => false,
            'type' => 'строка',
        ],

        // Дополнительные реквизиты для платежей в бюджетную систему Российской Федерации
        'СтатусСоставителя' => [
            'required' => true,
            'type' => '2',
        ],
        'ПлательщикКПП' => [
            'required' => true,
            'type' => '9',
        ],
        'ПолучательКПП' => [
            'required' => true,
            'type' => '9',
        ],
        'ПоказательКБК' => [
            'required' => true,
            'type' => '20',
        ],
        'ОКАТО' => [
            'required' => true,
            'type' => '11',
        ],
        'ПоказательОснования' => [
            'required' => true,
            'type' => '2',
        ],
        'ПоказательПериода' => [
            'required' => true,
            'type' => '10',
        ],
        'ПоказательНомера' => [
            'required' => true,
            'type' => 'строка',
        ],
        'ПоказательДаты' => [
            'required' => true,
            'type' => 'дд.мм.гггг',
        ],
        'ПоказательТипа' => [
            'required' => true,
            'type' => '2',
        ],

        // Дополнительные реквизиты для отдельных видов документов
        'Очередность' => [
            'required' => false,
            'type' => '2',
        ],
        'СрокАкцепта' => [
            'required' => false,
            'type' => 'число',
        ],
        'ВидАккредитива' => [
            'required' => false,
            'type' => 'строка',
        ],
        'СрокПлатежа' => [
            'required' => false,
            'type' => 'дд.мм.гггг',
        ],
        'УсловиеОплаты1' => [
            'required' => false,
            'type' => 'строка',
        ],
        'УсловиеОплаты2' => [
            'required' => false,
            'type' => 'строка',
        ],
        'УсловиеОплаты3' => [
            'required' => false,
            'type' => 'строка',
        ],
        'ПлатежПоПредст' => [
            'required' => false,
            'type' => 'строка',
        ],
        'ДополнУсловия' => [
            'required' => false,
            'type' => 'строка',
        ],
        'НомерСчетаПоставщика' => [
            'required' => false,
            'type' => 'строка',
        ],
        'ДатаОтсылкиДок' => [
            'required' => false,
            'type' => 'дд.мм.гггг',
        ],
    ];
}
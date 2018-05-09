<?php

use Phinx\Migration\AbstractMigration;
use Bitrix\Main\Loader;

class CreateSecondIblock extends AbstractMigration
{

    static $iblockType = CreateTypeIblock::idTypeIblock; // Тип инфоблока
    static $siteId = "s1"; // ID сайта
    static $codeName = "second_iblock"; // Код инфоблока
    static $nameIblock = "Второй инфоблок"; // Имя инфоблока

    public function up()
    {
        if (!Loader::includeModule('iblock')) {
            echo "Модуль Инфоблок не подключен!";
            return false;
        }

        $ib = new CIBlock;

        //==========================//
        // Создаем воторой инфоблок //
        //==========================//

        // Настройка доступа
        $arAccess = array(
            "2" => "R", // Все пользователи
        );

        $arFields = Array(
            "ACTIVE" => "Y",
            "NAME" => self::$nameIblock,
            "CODE" => self::$codeName,
            "IBLOCK_TYPE_ID" => self::$iblockType,
            "TIMESTAMP_X" => new DateTime(),
            "SITE_ID" => self::$siteId,
            "SORT" => "5",
            "GROUP_ID" => $arAccess, // Права доступа
            "DESCRIPTION_TYPE" => "html",
            "RSS_ACTIVE" => "N",
            "RSS_TTL" => 24,
            "RSS_FILE_ACTIVE" => "N",
            "RSS_YANDEX_ACTIVE" => "N",
            "INDEX_ELEMENT" => "Y",
            "INDEX_SECTION" => "N",
            "WORKFLOW" => "N",
            "BIZPROC" => "N",
            "VERSION" => 1,

            "ELEMENT_NAME" => "Элемент",
            "ELEMENTS_NAME" => "Элементы",
            "ELEMENT_ADD" => "Добавить элемент",
            "ELEMENT_EDIT" => "Изменить элемент",
            "ELEMENT_DELETE" => "Удалить элемент",
            "SECTION_NAME" => "Раздел",
            "SECTIONS_NAME" => "Разделы",
            "SECTION_ADD" => "Добавить раздел",
            "SECTION_EDIT" => "Изменить раздел",
            "SECTION_DELETE" => "Удалить раздел",

        );

        $ID = $ib->Add($arFields);

        if ($ID > 0)
        {
            echo "Инфоблок \"".self::$nameIblock."\" успешно создан!\n";
        }
        else
        {
            echo "Ошибка создания инфоблока \"".self::$nameIblock."!\"\n";
            return false;
        }


    }

    public function down()
    {
        if (!Loader::includeModule('iblock')) {
            echo "Ошибка, модуль информационных блоков не подключен!\n";
            return false;
        }

        $ib = new CIBlock();

        $obIblock = $ib->GetList(
            array(),
            array("CODE" => self::$codeName),
            array()
        );

        $res = array();

        if ($arIblock = $obIblock->Fetch()) {
            $res = $ib->Delete($arIblock["ID"]);
        }

        if ($res) {
            echo "Инфоблок \"$arIblock[NAME]\" успешно удален!\n";
        } else {
            echo "Ошибка удаления инфоблока \"$arIblock[NAME]\"!\n";
            return false;
        }
    }
}

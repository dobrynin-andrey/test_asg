<?php

use Phinx\Migration\AbstractMigration;
use Bitrix\Main\Loader;
use Bitrix\Highloadblock\HighloadBlockTable as HL;

class CreatePropForFirstIbHandbook extends AbstractMigration
{
    const codeProperty = "HANDBOOK";
    const hlTableName = 'handbook';

    public function up()
    {

        if (!Loader::includeModule('iblock')) {
            echo "Модуль Инфоблок не подключен!\n";
            return false;
        }


        // Берем ID первого инфоблока, если он создан
        $dbFirstIblock = CIBlock::GetList(
            array(),
            array(
                "CODE" => CreateFirstIblock::$codeName
            )
        );

        $idFirstIblock = $dbFirstIblock->Fetch()['ID'];

        if ($idFirstIblock < 0) {
            echo "Инфоблок с кодом " . CreateFirstIblock::$codeName . " не создан!\n";
            return false;
        }

        // Определяем, есть ли у первого инфоблока свойства
        $dbPropertiesFirstIblock = CIBlockProperty::GetList(
            array(),
            array(
                "IBLOCK_ID" => $idFirstIblock
            )
        );

        // Создаем highloadblock для справочника
        if (!Loader::includeModule('highloadblock')) {
            echo "Модуль highloadblock не подключен!\n";
            return false;
        }

        //создание hl-блока
        $result = HL::add(array(
            'NAME' => 'Handbook',//должно начинаться с заглавной буквы и состоять только из латинских букв и цифр
            'TABLE_NAME' => self::hlTableName,//должно состоять только из строчных латинских букв, цифр и знака подчеркивания
        ));
        if (!$result->isSuccess()) {
            echo "Модуль highloadblock ".self::hlTableName." не создан!\n";
            return false;
        } else {
            // Создаем поля highloadblock
            $highLoadBlockId = $result->getId();
            $userTypeEntity    = new CUserTypeEntity();

            $userTypeData["UF_NAME"]    = array(
                'ENTITY_ID'         => 'HLBLOCK_'.$highLoadBlockId,
                'FIELD_NAME'        => 'UF_NAME',
                'USER_TYPE_ID'      => 'string',
                'XML_ID'            => 'XML_ID_NAME',
                'SORT'              => 500,
                'MULTIPLE'          => 'N',
                'MANDATORY'         => 'Y',
                'SHOW_FILTER'       => 'Y',
                'SHOW_IN_LIST'      => 'Y',
                'EDIT_IN_LIST'      => 'Y',
                'IS_SEARCHABLE'     => 'Y',
                'SETTINGS'          => array(
                    'DEFAULT_VALUE' => '',
                    'SIZE'          => '20',
                    'ROWS'          => '1',
                    'MIN_LENGTH'    => '0',
                    'MAX_LENGTH'    => '0',
                    'REGEXP'        => '',
                ),
                'EDIT_FORM_LABEL'   => array(
                    'ru'    => 'Название',
                    'en'    => 'Name',
                ),
                'LIST_COLUMN_LABEL' => array(
                    'ru'    => 'Название',
                    'en'    => 'Name',
                ),
                'LIST_FILTER_LABEL' => array(
                    'ru'    => 'Название',
                    'en'    => 'Name',
                ),
                'ERROR_MESSAGE'     => array(
                    'ru'    => 'Ошибка при заполнении пользовательского свойства <Название>',
                    'en'    => 'An error in completing the user field <Name>',
                ),
                'HELP_MESSAGE'      => array(
                    'ru'    => '',
                    'en'    => '',
                ),
            );

            $userTypeData["UF_ELEMENT_ID"] = array(
                'ENTITY_ID'         => 'HLBLOCK_'.$highLoadBlockId,
                'FIELD_NAME'        => 'UF_XML_ID',
                'USER_TYPE_ID'      => 'string',
                'XML_ID'            => 'XML_ID_ELEMENT_ID',
                'SORT'              => 500,
                'MULTIPLE'          => 'Y',
                'MANDATORY'         => 'Y',
                'SHOW_FILTER'       => 'Y',
                'SHOW_IN_LIST'      => 'Y',
                'EDIT_IN_LIST'      => 'Y',
                'IS_SEARCHABLE'     => 'N',
                'SETTINGS'          => array(
                    'DEFAULT_VALUE' => '',
                ),
                'EDIT_FORM_LABEL'   => array(
                    'ru'    => 'ID элемента',
                    'en'    => 'Element ID',
                ),
                'LIST_COLUMN_LABEL' => array(
                    'ru'    => 'ID элемента',
                    'en'    => 'Element ID',
                ),
                'LIST_FILTER_LABEL' => array(
                    'ru'    => 'ID элемента',
                    'en'    => 'Element ID',
                ),
                'ERROR_MESSAGE'     => array(
                    'ru'    => 'Ошибка при заполнении пользовательского свойства <ID элемента>',
                    'en'    => 'An error in completing the user field <Element ID>',
                ),
                'HELP_MESSAGE'      => array(
                    'ru'    => '',
                    'en'    => '',
                ),
            );

            foreach ($userTypeData as $itemData) {
                $userTypeEntity->Add($itemData);
            }
        }


        $ibp = new CIBlockProperty;

        $arFields = Array(
            "NAME" => "Справочник",
            "ACTIVE" => "Y",
            "MULTIPLE" => "Y",
            "LIST_TYPE" => "L",
            "SORT" => 500,
            "CODE" => self::codeProperty,
            "MULTIPLE_CNT" => 5, // Количество свойств, предлагаемых по умолчанию
            "PROPERTY_TYPE" => "S",
            "USER_TYPE" => "directory", // Справочник
            "IBLOCK_ID" => $idFirstIblock,
            "USER_TYPE_SETTINGS" => array (
                "size" => "1",
                "width" => "0",
                "group" => "N",
                "multiple" => "Y",
                "TABLE_NAME" => self::hlTableName
            ),
        );

        $propId = $ibp->Add($arFields);

        if ($propId > 0) {
            echo "Добавлено свойство \"" . $arFields["NAME"] . "\"\n";
        } else {
            echo "Ошибка добавления свойства \"" . $arFields["NAME"] . "\"\n";
        }

    }

    public function down()
    {
        if (!Loader::includeModule('iblock')) {
            echo "Модуль Инфоблок не подключен!\n";
            return false;
        }

        $dbProperty = CIBlockProperty::GetList(
            array(),
            array(
                "CODE" => self::codeProperty
            )
        );

        if ($idProperty = $dbProperty->Fetch()['ID']) {
            $res = CIBlockProperty::Delete($idProperty);

            if ($res) {
                echo "Свойство \"" . self::codeProperty . "\" успешно удалено!\n";
            } else {
                echo "Ошибка удаления свойства \"" . self::codeProperty . "\"\n";
            }

        }

        // Создаем highloadblock для справочника
        if (!Loader::includeModule('highloadblock')) {
            echo "Модуль highloadblock не подключен!\n";
            return false;
        }

        $hlId = HL::getList(array(
            'filter' => array(
                '=NAME' => self::hlTableName
            )
        ))->fetch()['ID'];

        if ($hlId > 0) {

            $resultDeleteHl = HL::delete($hlId);

            if ($resultDeleteHl->isSuccess()) {
                echo "Highloadblock " . self::hlTableName . " успешно удален!\n";
            } else {
                echo "Highloadblock " . self::hlTableName . " удалить не удалось!\n";
            }

        } else {
            echo "Highloadblock " . self::hlTableName . " не создан!\n";
            return false;
        }

    }
}

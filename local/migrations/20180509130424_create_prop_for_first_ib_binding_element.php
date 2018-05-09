<?php

use Phinx\Migration\AbstractMigration;
use Bitrix\Main\Loader;

class CreatePropForFirstIbBindingElement extends AbstractMigration
{

    const codeProperty = "BINDING_ELEMENT";

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
            die();
        }

        // Определяем, есть ли у первого инфоблока свойства
        $dbPropertiesFirstIblock = CIBlockProperty::GetList(
            array(),
            array(
                "IBLOCK_ID" => $idFirstIblock
            )
        );

        // Если нет, создаем
        if (!$dbPropertiesFirstIblock->Fetch()) {

            // Берем ID воторого инфоблока, если он создан
            $dbSecondIblock = CIBlock::GetList(
                array(),
                array(
                    "CODE" => CreateSecondIblock::$codeName
                )
            );

            if ($idSecondIblock = $dbSecondIblock->Fetch()['ID']) {

                $ibp = new CIBlockProperty;

                $arFields = Array(
                    "NAME" => "Множественная привязка к элементам другого инфоблока",
                    "ACTIVE" => "Y",
                    "MULTIPLE" => "Y",
                    "SORT" => 500,
                    "CODE" => self::codeProperty,
                    "MULTIPLE_CNT" => 5, // Количество свойств, предлагаемых по умолчанию
                    "PROPERTY_TYPE" => "E", // Привязка к элементам инфоблока
                    "IBLOCK_ID" => $idFirstIblock,
                    "LINK_IBLOCK_ID" => $idSecondIblock,
                );

                $propId = $ibp->Add($arFields);

                if ($propId > 0) {
                    echo "Добавлено свойство \"" . $arFields["NAME"] . "\"\n";
                } else {
                    echo "Ошибка добавления свойства \"" . $arFields["NAME"] . "\"\n";
                }

            } else {
                echo "Инфоблок с кодом " . CreateSecondIblock::$codeName . " не создан!\n";
                return false;
            }

        } else {
            echo "Инфоблок с кодом " . CreateFirstIblock::$codeName . " не создан!\n";
            return false;
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

    }
}

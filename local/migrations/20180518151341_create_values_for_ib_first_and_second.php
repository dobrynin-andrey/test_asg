<?php

use Phinx\Migration\AbstractMigration;

use Bitrix\Iblock\ElementTable;
use Bitrix\Main\Loader;
use Bitrix\Iblock\IblockTable;
use Bitrix\Highloadblock\HighloadBlockTable as HL;

class CreateValuesForIbFirstAndSecond extends AbstractMigration
{
    public function up()
    {
        if (!Loader::includeModule('iblock')) {
            echo "Модуль Инфоблок не подключен!\n";
            return false;
        }

        // Массив элементов для второго инфоблока
        $arValueSecondIblock = array(
            0 => array(
                'NAME' => '1 элемент второго инфоблока'
            ),
            1 => array(
                'NAME' => '2 элемент второго инфоблока'
            ),
            2 => array(
                'NAME' => '3 элемент второго инфоблока'
            ),
            3 => array(
                'NAME' => '4 элемент второго инфоблока'
            ),
            4 => array(
                'NAME' => '5 элемент второго инфоблока'
            )
        );

        // Получим ID второго инфоблока по его коду
        $idSecondIblock = IblockTable::getList(
            array(
            'select' => array('ID'),
            'filter' => array('CODE' => CreateSecondIblock::$codeName)
            )
        )->fetch()['ID'];

        $ie = new CIBlockElement();

        $arIdsFirstIblock = array();
        // Добавим новые элементы
        foreach ($arValueSecondIblock as $itemValueSecondIblock) {
            $itemValueSecondIblock = array_merge($itemValueSecondIblock, array('IBLOCK_ID' => $idSecondIblock));

            $idAddedToSecondIblock = $ie->Add(
                $itemValueSecondIblock
            );

            if (!$idAddedToSecondIblock) {
                echo "Ошибка! Не удалось добавить элемент \"$itemValueSecondIblock[NAME]\"!\n";
                return false;
            } else {
                $arIdsFirstIblock[] = $idAddedToSecondIblock;
            }
        }


        $arValueFirstIblock = array(
            0 => array(
                'NAME' => '1 элемент первого инфоблока',
            ),
            1 => array(
                'NAME' => '2 элемент первого инфоблока'
            ),
            2 => array(
                'NAME' => '3 элемент первого инфоблока'
            ),
            3 => array(
                'NAME' => '4 элемент первого инфоблока'
            ),
            4 => array(
                'NAME' => '5 элемент первого инфоблока'
            ),
            5 => array(
                'NAME' => '6 элемент первого инфоблока'
            ),
            6=> array(
                'NAME' => '7 элемент первого инфоблока'
            ),
            7 => array(
                'NAME' => '8 элемент первого инфоблока'
            ),
            8 => array(
                'NAME' => '9 элемент первого инфоблока'
            ),
            9 => array(
                'NAME' => '10 элемент первого инфоблока'
            ),
            10 => array(
                'NAME' => '11 элемент первого инфоблока'
            ),
            11 => array(
                'NAME' => '12 элемент первого инфоблока'
            ),
        );

        // Получим ID первого инфоблока по его коду
        $idFirsIblock = IblockTable::getList(
            array(
                'select' => array('ID'),
                'filter' => array('CODE' => CreateFirstIblock::$codeName)
            )
        )->fetch()['ID'];


        $propertyValues = array(

        );

        // Создаем highloadblock для справочника
        if (!Loader::includeModule('highloadblock')) {
            echo "Модуль highloadblock не подключен!\n";
            return false;
        }


        $idHlBlock = HL::getList(
            array(
                'select' => array('*'),
                'filter' => array('TABLE_NAME' => CreatePropForFirstIbHandbook::hlTableName)
            )
        )->fetch()['ID'];

        $entityHl = HL::compileEntity($idHlBlock);
        $entityDataClass = $entityHl->getDataClass();

        $arHlElementsIds = $entityDataClass::getList(array(
            "select" => array("ID"),
        ))->fetchAll();


        // Запись элементов в первый инфоблок
        foreach ($arValueFirstIblock as $itemValueFirstIblock) {
            $itemValueFirstIblock = array_merge($itemValueFirstIblock,
                array(
                    'IBLOCK_ID' => $idFirsIblock,
                    'PROPERTY_VALUES' => array(
                        CreatePropForFirstIbBindingElement::codeProperty => array(
                            $arIdsFirstIblock[rand(0, count($arIdsFirstIblock) - 1)],
                            $arIdsFirstIblock[rand(0, count($arIdsFirstIblock) - 1)],
                            $arIdsFirstIblock[rand(0, count($arIdsFirstIblock) - 1)]
                        ),
                        CreatePropForFirstIbHandbook::codeProperty => array(
                            $arHlElementsIds[rand(0, count($arHlElementsIds) - 1)]['ID'],
                            $arHlElementsIds[rand(0, count($arHlElementsIds) - 1)]['ID'],
                            $arHlElementsIds[rand(0, count($arHlElementsIds) - 1)]['ID']
                        )
                    )
                )
            );

            $resultAddedToFirstIblock = $ie->Add(
                $itemValueFirstIblock
            );

            if (!$resultAddedToFirstIblock) {
                echo "Ошибка! Не удалось добавить элемент \"$itemValueFirstIblock[NAME]\"!\n";
                return false;
            }
        }

    }
}

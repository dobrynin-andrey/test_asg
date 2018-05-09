<?php

use Phinx\Migration\AbstractMigration;
use Bitrix\Main\Loader;

class CreateTypeIblock extends AbstractMigration
{

    const idTypeIblock = 'content';

    public function up()
    {

        if (!Loader::includeModule('iblock')) {
            echo "Ошибка, модуль информационных блоков не подключен!\n";
            return false;
        }

        $arFields = Array(
            'ID' => self::idTypeIblock,
            'SECTIONS' => 'Y',
            'IN_RSS' => 'N',
            'SORT' => 500,
            'LANG' => Array(
                'ru' => Array(
                    'NAME' => 'Контент',
                    'SECTION_NAME' => 'Разделы',
                    'ELEMENT_NAME' => 'Элементы'
                )
            )
        );

        $obBlockType = new CIBlockType;

        $res = $obBlockType->Add($arFields);
        if ($res) {
            echo "Тип информационного блока: \"".self::idTypeIblock."\", успешно создан!\n";
        }
        else {
            echo "Ошибка: ".$obBlockType->LAST_ERROR."\n";
        }

    }

    public function down()
    {
        if (!Loader::includeModule('iblock')) {
            echo "Ошибка, модуль информационных блоков не подключен!\n";
            return false;
        }

        if(CIBlockType::Delete(self::idTypeIblock))
        {
            echo "Тип информационного блока: \"".self::idTypeIblock."\", успешно удален! \n";
        }
        else {
            echo "Ошибка при удалении типа ифнормационного блока!\n";
        }

    }
}

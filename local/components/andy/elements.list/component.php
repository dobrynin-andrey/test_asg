<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var CBitrixComponent $this */
/** @var array $arParams */
/** @var array $arResult */
/** @var string $componentPath */
/** @var string $componentName */
/** @var string $componentTemplate */
/** @global CDatabase $DB */
/** @global CUser $USER */
/** @global CMain $APPLICATION */

/** @global CIntranetToolbar $INTRANET_TOOLBAR */
global $INTRANET_TOOLBAR;

use Bitrix\Main\Context,
	Bitrix\Main\Type\DateTime,
	Bitrix\Main\Loader,
	Bitrix\Iblock,
    Bitrix\Iblock\IblockTable;
use Bitrix\Highloadblock\HighloadBlockTable as HL;

CPageOption::SetOptionString("main", "nav_page_in_session", "N");

if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 36000000;


$arParams["IBLOCK_ID"] = trim($arParams["IBLOCK_ID_FIRST"]);


$arParams["SORT_BY1"] = trim($arParams["SORT_BY1"]);
if(strlen($arParams["SORT_BY1"])<=0)
	$arParams["SORT_BY1"] = "ACTIVE_FROM";
if(!preg_match('/^(asc|desc|nulls)(,asc|,desc|,nulls){0,1}$/i', $arParams["SORT_ORDER1"]))
	$arParams["SORT_ORDER1"]="DESC";

if(strlen($arParams["SORT_BY2"])<=0)
	$arParams["SORT_BY2"] = "SORT";
if(!preg_match('/^(asc|desc|nulls)(,asc|,desc|,nulls){0,1}$/i', $arParams["SORT_ORDER2"]))
	$arParams["SORT_ORDER2"]="ASC";

if(strlen($arParams["FILTER_NAME"])<=0 || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["FILTER_NAME"]))
{
	$arrFilter = array();
}
else
{
	$arrFilter = $GLOBALS[$arParams["FILTER_NAME"]];
	if(!is_array($arrFilter))
		$arrFilter = array();
}


$arParams["NEWS_COUNT"] = intval($arParams["NEWS_COUNT"]);
if($arParams["NEWS_COUNT"]<=0)
	$arParams["NEWS_COUNT"] = 20;

$arParams["CACHE_FILTER"] = $arParams["CACHE_FILTER"]=="Y";
if(!$arParams["CACHE_FILTER"] && count($arrFilter)>0)
	$arParams["CACHE_TIME"] = 0;


$arParams["ACTIVE_DATE_FORMAT"] = trim($arParams["ACTIVE_DATE_FORMAT"]);
if(strlen($arParams["ACTIVE_DATE_FORMAT"])<=0)
	$arParams["ACTIVE_DATE_FORMAT"] = 'j F Y';


$arParams["DISPLAY_TOP_PAGER"] = $arParams["DISPLAY_TOP_PAGER"]=="Y";
$arParams["DISPLAY_BOTTOM_PAGER"] = $arParams["DISPLAY_BOTTOM_PAGER"]!="N";
$arParams["PAGER_TITLE"] = trim($arParams["PAGER_TITLE"]);
$arParams["PAGER_SHOW_ALWAYS"] = $arParams["PAGER_SHOW_ALWAYS"]=="Y";
$arParams["PAGER_TEMPLATE"] = trim($arParams["PAGER_TEMPLATE"]);
$arParams["PAGER_DESC_NUMBERING"] = $arParams["PAGER_DESC_NUMBERING"]=="Y";
$arParams["PAGER_DESC_NUMBERING_CACHE_TIME"] = intval($arParams["PAGER_DESC_NUMBERING_CACHE_TIME"]);
$arParams["PAGER_SHOW_ALL"] = $arParams["PAGER_SHOW_ALL"]=="Y";
$arParams["CHECK_PERMISSIONS"] = $arParams["CHECK_PERMISSIONS"]!="N";

if($arParams["DISPLAY_TOP_PAGER"] || $arParams["DISPLAY_BOTTOM_PAGER"])
{
	$arNavParams = array(
		"nPageSize" => $arParams["NEWS_COUNT"],
		"bDescPageNumbering" => $arParams["PAGER_DESC_NUMBERING"],
		"bShowAll" => $arParams["PAGER_SHOW_ALL"],
	);
	$arNavigation = CDBResult::GetNavParams($arNavParams);
	if($arNavigation["PAGEN"]==0 && $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"]>0)
		$arParams["CACHE_TIME"] = $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"];
}
else
{
	$arNavParams = array(
		"nTopCount" => $arParams["NEWS_COUNT"],
		"bDescPageNumbering" => $arParams["PAGER_DESC_NUMBERING"],
	);
	$arNavigation = false;
}

if (empty($arParams["PAGER_PARAMS_NAME"]) || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["PAGER_PARAMS_NAME"]))
{
	$pagerParameters = array();
}
else
{
	$pagerParameters = $GLOBALS[$arParams["PAGER_PARAMS_NAME"]];
	if (!is_array($pagerParameters))
		$pagerParameters = array();
}

$arParams["USE_PERMISSIONS"] = $arParams["USE_PERMISSIONS"]=="Y";
if(!is_array($arParams["GROUP_PERMISSIONS"]))
	$arParams["GROUP_PERMISSIONS"] = array(1);

$bUSER_HAVE_ACCESS = !$arParams["USE_PERMISSIONS"];
if($arParams["USE_PERMISSIONS"] && isset($GLOBALS["USER"]) && is_object($GLOBALS["USER"]))
{
	$arUserGroupArray = $USER->GetUserGroupArray();
	foreach($arParams["GROUP_PERMISSIONS"] as $PERM)
	{
		if(in_array($PERM, $arUserGroupArray))
		{
			$bUSER_HAVE_ACCESS = true;
			break;
		}
	}
}

if($this->startResultCache(false, array(($arParams["CACHE_GROUPS"]==="N"? false: $USER->GetGroups()), $bUSER_HAVE_ACCESS, $arNavigation, $arrFilter, $pagerParameters)))
{
	if(!Loader::includeModule("iblock"))
	{
		$this->abortResultCache();
		ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
		return;
	}
    $arParams["IBLOCK_ID"] = 'first_iblock';
	if(is_numeric($arParams["IBLOCK_ID"]))
	{
        $rsIBlock = IblockTable::getList(
            array(
                'filter' => array('ID' => $arParams["IBLOCK_ID"])
            )
        );

	}
	else
	{
        $rsIBlock = IblockTable::getList(
            array(
                'filter' => array('CODE' => $arParams["IBLOCK_ID"])
            )
        );
	}


	$arResult = $rsIBlock->fetch();
	if (!$arResult)
	{
		$this->abortResultCache();
		Iblock\Component\Tools::process404(
			trim($arParams["MESSAGE_404"]) ?: GetMessage("T_NEWS_NEWS_NA")
			,true
			,$arParams["SET_STATUS_404"] === "Y"
			,$arParams["SHOW_404"] === "Y"
			,$arParams["FILE_404"]
		);
		return;
	}

	$arResult["USER_HAVE_ACCESS"] = $bUSER_HAVE_ACCESS;
	//SELECT
	$arSelect = array(
		"ID",
		"IBLOCK_ID",
		"NAME",
		"TIMESTAMP_X",
        "PROPERTY_*"
	);

	//WHERE
	$arFilter = array (
		"IBLOCK_ID" => $arResult["ID"],
		"IBLOCK_LID" => SITE_ID,
		"ACTIVE" => "Y",
		"CHECK_PERMISSIONS" => $arParams['CHECK_PERMISSIONS'] ? "Y" : "N",
	);


	//ORDER BY
	$arSort = array(
		$arParams["SORT_BY1"]=>$arParams["SORT_ORDER1"],
		$arParams["SORT_BY2"]=>$arParams["SORT_ORDER2"],
	);
	if(!array_key_exists("ID", $arSort))
		$arSort["ID"] = "DESC";


	$arResult["ITEMS"] = array();
	$arResult["ELEMENTS"] = array();
	$rsElement = CIBlockElement::GetList($arSort, array_merge($arFilter , $arrFilter), false, $arNavParams, $arSelect);

	while($obElement = $rsElement->GetNextElement())
	{
		$arItem = $obElement->GetFields();

		// Свойства
        $arItem["PROPERTIES"] = $obElement->GetProperties();

        $timestamp = DateTime::createFromUserTime($arItem["TIMESTAMP_X"]);
        $arItem["DATE_MODIFIED_FROM"] = CIBlockFormatProperties::DateFormat($arParams["ACTIVE_DATE_FORMAT"], $timestamp);

		$arResult["ITEMS"][] = $arItem;
		$arResult["ELEMENTS"][] = $arItem["ID"];
	}

	$navComponentParameters = array();
	if ($arParams["PAGER_BASE_LINK_ENABLE"] === "Y")
	{
		$pagerBaseLink = trim($arParams["PAGER_BASE_LINK"]);
		if ($pagerBaseLink === "")
		{
			if (
				$arResult["SECTION"]
				&& $arResult["SECTION"]["PATH"]
				&& $arResult["SECTION"]["PATH"][0]
				&& $arResult["SECTION"]["PATH"][0]["~SECTION_PAGE_URL"]
			)
			{
				$pagerBaseLink = $arResult["SECTION"]["PATH"][0]["~SECTION_PAGE_URL"];
			}
			elseif (
				isset($arItem) && isset($arItem["~LIST_PAGE_URL"])
			)
			{
				$pagerBaseLink = $arItem["~LIST_PAGE_URL"];
			}
		}

		if ($pagerParameters && isset($pagerParameters["BASE_LINK"]))
		{
			$pagerBaseLink = $pagerParameters["BASE_LINK"];
			unset($pagerParameters["BASE_LINK"]);
		}

		$navComponentParameters["BASE_LINK"] = CHTTP::urlAddParams($pagerBaseLink, $pagerParameters, array("encode"=>true));
	}

	$arResult["NAV_STRING"] = $rsElement->GetPageNavStringEx(
		$navComponentObject,
		$arParams["PAGER_TITLE"],
		$arParams["PAGER_TEMPLATE"],
		$arParams["PAGER_SHOW_ALWAYS"],
		$this,
		$navComponentParameters
	);
	$arResult["NAV_CACHED_DATA"] = null;
	$arResult["NAV_RESULT"] = $rsElement;
	$arResult["NAV_PARAM"] = $navComponentParameters;


	// Формируем фильтр по свойству второго инфоблока
    $rsElementFilter = CIBlockElement::GetList($arSort, $arFilter, false, array(), $arSelect);
    $arResultFilter = array();
    while($obElementFilter = $rsElementFilter->GetNextElement()) {
        // Свойства
        $arItemFilter["PROPERTIES"] = $obElementFilter->GetProperties();
        $arResultFilter[] = $arItemFilter;
    }


    $arFirstFilter = array();
    $idsFirstIblockElements = array();
    $arSecondFilter = array();
    $idsSecondIblockElements = array();
    foreach ($arResultFilter as $item) {
        // Получаем ключи приаязанных элементов
        foreach ($item['PROPERTIES']['BINDING_ELEMENT']['VALUE'] as $value) {
            if (!in_array($value, $idsFirstIblockElements)) {
                $idsFirstIblockElements[] = $value;
            }
        }

        // Достаем из базы название и ID
        $arFirstFilter = Iblock\ElementTable::getList(
            array(
                'select' => array('ID', 'NAME'),
                'filter' => array(
                    'ID' => $idsFirstIblockElements,
                )
            )
        )->fetchAll();



        foreach ($item['PROPERTIES']['HANDBOOK']['VALUE'] as $value) {
            if (!in_array($value, $idsSecondIblockElements)) {
                $idsSecondIblockElements[] = $value;
            }
        }


        // Создаем highloadblock для справочника
        if (!Loader::includeModule('highloadblock')) {
            $this->abortResultCache();
            ShowError("Модуль highloadblock не подключен!\n");
            return;
        }

        $tableNameHL = $item['PROPERTIES']['HANDBOOK']['USER_TYPE_SETTINGS']['TABLE_NAME'];

        $idHlBlock = HL::getList(
            array(
                'select' => array('*'),
                'filter' => array('TABLE_NAME' => $tableNameHL,
                    )
            )
        )->fetch()['ID'];

        $entityHl = HL::compileEntity($idHlBlock);
        $entityDataClass = $entityHl->getDataClass();

        $arSecondFilter = $entityDataClass::getList(array(
            "select" => array("ID", "UF_NAME"),
            "filter" => array("ID" => $idsSecondIblockElements)
        ))->fetchAll();


    }
    $arResult["FIRST_FILTER"] = $arFirstFilter;
    $arResult["SECOND_FILTER"] = $arSecondFilter;


	$this->setResultCacheKeys(array(
		"ID",
		"IBLOCK_TYPE_ID",
		"LIST_PAGE_URL",
		"NAV_CACHED_DATA",
		"NAME",
		"SECTION",
		"ELEMENTS",
		"IPROPERTY_VALUES",
		"ITEMS_TIMESTAMP_X",
	));
	if ($_REQUEST['ajax'] == 'Y') {
        $this->abortResultCache();
	    $APPLICATION->RestartBuffer();
        $this->includeComponentTemplate('ajax/template');
        d('ajax');
        die();
    } else {
        $this->includeComponentTemplate();
    }

}

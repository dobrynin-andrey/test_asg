<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var array $arCurrentValues */

if(!CModule::IncludeModule("iblock"))
	return;


$arIBlocks=array();
$db_iblock = CIBlock::GetList(array("SORT"=>"ASC"), array("SITE_ID"=>$_REQUEST["site"], "TYPE" => ($arCurrentValues["IBLOCK_TYPE"]!="-"?$arCurrentValues["IBLOCK_TYPE"]:"")));
while($arRes = $db_iblock->Fetch())
	$arIBlocks[$arRes["ID"]] = "[".$arRes["ID"]."] ".$arRes["NAME"];

$arComponentParameters = array(
	"PARAMETERS" => array(
		"IBLOCK_ID_FIRST" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("T_IBLOCK_DESC_LIST_ID_FIRST"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlocks,
			"DEFAULT" => '={$_REQUEST["IBLOCK_ID_FIRST"]}',
			"ADDITIONAL_VALUES" => "Y",
			"REFRESH" => "Y",
		),
        "IBLOCK_ID_SECOND" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("T_IBLOCK_DESC_LIST_ID_SECOND"),
            "TYPE" => "LIST",
            "VALUES" => $arIBlocks,
            "DEFAULT" => '={$_REQUEST["IBLOCK_ID_SECOND"]}',
            "ADDITIONAL_VALUES" => "Y",
            "REFRESH" => "Y",
        ),
		"NEWS_COUNT" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("T_IBLOCK_DESC_LIST_CONT"),
			"TYPE" => "STRING",
			"DEFAULT" => "20",
		),
		"FILTER_NAME" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("T_IBLOCK_FILTER"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),
		"CACHE_TIME"  =>  array("DEFAULT"=>36000000),
		"CACHE_FILTER" => array(
			"PARENT" => "CACHE_SETTINGS",
			"NAME" => GetMessage("IBLOCK_CACHE_FILTER"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"CACHE_GROUPS" => array(
			"PARENT" => "CACHE_SETTINGS",
			"NAME" => GetMessage("CP_BNL_CACHE_GROUPS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
	),
);

CIBlockParameters::AddPagerSettings(
	$arComponentParameters,
	GetMessage("T_IBLOCK_DESC_PAGER_ELEMENTS"), //$pager_title
	true, //$bDescNumbering
	true, //$bShowAllParam
	true, //$bBaseLink
	$arCurrentValues["PAGER_BASE_LINK_ENABLE"]==="Y" //$bBaseLinkEnabled
);
//
//CIBlockParameters::Add404Settings($arComponentParameters, $arCurrentValues);

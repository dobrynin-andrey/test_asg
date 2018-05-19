<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if (isset($_REQUEST['first_filter']))
    $GLOBALS['elementsFilter']['PROPERTY_BINDING_ELEMENT'] = $_REQUEST['first_filter'];

if (isset($_REQUEST['second_filter']))
    $GLOBALS['elementsFilter']['PROPERTY_HANDBOOK'] = $_REQUEST['second_filter'];


?><?$APPLICATION->IncludeComponent(
	"andy:elements.list", 
	"ajax",
	array(
		"CACHE_FILTER" => "N",
		"CACHE_GROUPS" => "Y",
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "N",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"DISPLAY_TOP_PAGER" => "N",
		"FILTER_NAME" => "elementsFilter",
		"IBLOCK_ID_FIRST" => "137",
		"IBLOCK_ID_SECOND" => "137",
		"NEWS_COUNT" => "3",
		"PAGER_BASE_LINK_ENABLE" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "N",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => ".default",
		"PAGER_TITLE" => "Элементы",
		"COMPONENT_TEMPLATE" => ".default",
		"TEMPLATE_THEME" => ".default",
	),
	false
);
die();
?>
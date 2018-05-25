<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);

if(!$arResult["NavShowAlways"])
{
	if ($arResult["NavRecordCount"] == 0 || ($arResult["NavPageCount"] == 1 && $arResult["NavShowAll"] == false))
		return;
}

$strNavQueryString = ($arResult["NavQueryString"] != "" ? $arResult["NavQueryString"]."&amp;" : "");
$strNavQueryStringFull = ($arResult["NavQueryString"] != "" ? "?".$arResult["NavQueryString"] : "");

?>
<?if($arResult["NavPageNomer"] < $arResult["NavPageCount"]):?>
    <a style="text-align: center; display: block;" class="js-ajax-button" href="?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]+1)?>">Показать еще</a>
<?endif;?>
<?CJSCore::Init(array("jquery2"));?>
    <script>
        $(document).ready(function () {

            $('.js-ajax-button').on('click', function (event) {
                var button = $(this);
                var url = button.attr('href');
                event.preventDefault();
                $.ajax({
                    url: '/ajax/'+url,
                    method: 'GET',
                    success: function(data){
                        $(data).appendTo('#result_container');
                        button.remove();
                        $('.js-ajax-button').appendTo('.show-more');
                    },
                    error: function(error){
                        console.log('error');
                        console.log(error);
                    }


                });
            });
        });
    </script>
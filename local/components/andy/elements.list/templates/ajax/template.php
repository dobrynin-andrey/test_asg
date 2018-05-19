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
?>
<?foreach($arResult["ITEMS"] as $arItem):?>
    <p class="news-item">
        <?if($arItem['DATE_MODIFIED_FROM']):?>
            <span class="news-date-time"><?echo $arItem["DATE_MODIFIED_FROM"]?></span><br>
        <?endif?>
        <?if($arParams["DISPLAY_NAME"]!="N" && $arItem["NAME"]):?>
            <b><?echo $arItem["NAME"]?></b><br />
        <?endif;?>
        <?foreach($arItem["PROPERTIES"] as $pid => $arProperty):?>
            <small>
            <?=$arProperty["NAME"]?>:&nbsp;
            <?if(is_array($arProperty["VALUE"])):?>
                <?=implode("&nbsp;/&nbsp;", $arProperty["VALUE"]);?>
            <?else:?>
                <?=$arProperty["VALUE"];?>
            <?endif?>
            </small><br />
        <?endforeach;?>
    </p>
<?endforeach;?>
<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
   <?=$arResult["NAV_STRING"]?>
<?endif;?>


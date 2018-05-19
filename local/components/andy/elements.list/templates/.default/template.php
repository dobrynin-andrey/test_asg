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
<form action="/" method="get">
    <select name="first_filter">
        <option value="">-</option>
    <? foreach ($arResult["FIRST_FILTER"] as $itemFilter): ?>
        <option value="<?= $itemFilter['ID']?>" <? if ($_REQUEST['first_filter'] == $itemFilter['ID']):?>selected<?endif;?>>
           [<?= $itemFilter['ID']?>] <?= $itemFilter['NAME']?>
        </option>
    <? endforeach; ?>
    </select>

    <select name="second_filter">
        <option value="">-</option>
        <? foreach ($arResult["SECOND_FILTER"] as $itemFilter): ?>
            <option value="<?= $itemFilter['ID']?>" <? if ($_REQUEST['second_filter'] == $itemFilter['ID']):?>selected<?endif;?>>
                <?= $itemFilter['UF_NAME']?>
            </option>
        <? endforeach; ?>
    </select>
    <button type="submit">Показать</button>
</form>

<div class="news-list">
    <div id="result_container">
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
    </div>
<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
	<p class="show-more"><?=$arResult["NAV_STRING"]?></p>
<?endif;?>
</div>




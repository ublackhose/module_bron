<?

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/ublack.iiko/prolog.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/ublack.iiko/include.php");

use CUblackIIKOLogs;

IncludeModuleLangFile(__FILE__);

$module_id = 'ublack.iiko';
$moduleAccessLevel = $APPLICATION->GetGroupRight($module_id);

if ($moduleAccessLevel < "T") {
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}


$aTabs = array(
    array("DIV" => "main", "TAB" => GetMessage("ELEMENT_TAB"), "ICON" => ""),
);

$tabControl = new CAdminTabControl("tabControl", $aTabs);


$APPLICATION->SetTitle(GetMessage("PAGE_TITLE"));

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");


$IIKO_DB = new CUblackIIKOLogs();





$tabControl->Begin();
$tabControl->BeginNextTab();

$res = $IIKO_DB->GetList();



?>

    <table class="iiko-table" style="width: 100%">
        <thead>
        <tr>
            <th>
                ID
            </th>
            <th>
                ID IIKO
            </th>
            <th>
                Дата создания
            </th>
            <th>
                Текст ошибки
            </th>
            <th>
                Полный текст ошибки
            </th>
        </tr>
        </thead>
        <tbody>
        <?

        while ($arResult = $res->GetNext()){?>
        <tr>
            <td>
                <?=$arResult['ID']?>
            </td>
            <td>
                <?=$arResult['COMAND_ID']?>
            </td>
            <td>
                <?=$arResult['DATE_CREATE']?>
            </td>
            <td>
                <?=$arResult['ERROR_TEXT']?>
            </td>
            <td>
                <?=$arResult['LOG_TEXT']?>
            </td>
        </tr>
        <?
        }
        ?>
        </tbody>
    </table>

<style>
    .adm-detail-content{
        padding: 12px 18px 10px 12px;
    }

    table.iiko-table {
        border-collapse: collapse;
        border: 2px solid rgb(140 140 140);
        font-family: sans-serif;
        font-size: 0.8rem;
        letter-spacing: 1px;
    }
    .iiko-table thead,
    .iiko-table tfoot {
        background-color: rgb(228 240 245);
    }

    .iiko-table th,
    .iiko-table td {
        border: 1px solid rgb(160 160 160);
        padding: 8px 10px;
    }

</style>
<?php

$tabControl->End();

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
?>
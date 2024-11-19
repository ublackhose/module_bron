<?

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/ublack.reservation_date/prolog.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/ublack.reservation_date/include.php");

IncludeModuleLangFile(__FILE__);

$module_id = 'ublack.reservation_date';
$moduleAccessLevel = $APPLICATION->GetGroupRight($module_id);

if ($moduleAccessLevel == "D") {
    $APPLICATION->AuthForm(GetMessage("UBLACK_IIKO_ACCESS_DENIED"));
}


CAdminNotify::DeleteByTag("LOGS_ARE_TOO_BIG");

$sTableID = "ublack_iiko_orders";

$oSort = new CAdminSorting($sTableID, "ID", "desc");
$arOrder = (strtoupper($by) === "ID" ? array($by => $order) : array($by => $order, "ID" => "ASC"));
$lAdmin = new CAdminUiList($sTableID, $oSort);


$MODULES_filter = [
    '' => GetMessage("UBLACK_IIKO_MODULE_SENDER_ALL"),
    'main' => GetMessage("UBLACK_IIKO_MODULE_SENDER_MAIN"),
    'form' => GetMessage("UBLACK_IIKO_MODULE_SENDER_FORM"),
    'subscribe' => GetMessage("UBLACK_IIKO_MODULE_SENDER_SUBSCRIBE"),
    'sender' => GetMessage("UBLACK_IIKO_MODULE_SENDER_SENDER")
];
$MODULES = [
    '' => GetMessage("UBLACK_IIKO_MODULE_SENDER_SYSTEM"),
    'main' => GetMessage("UBLACK_IIKO_MODULE_SENDER_MAIN"),
    'form' => GetMessage("UBLACK_IIKO_MODULE_SENDER_FORM"),
    'subscribe' => GetMessage("UBLACK_IIKO_MODULE_SENDER_SUBSCRIBE"),
    'sender' => GetMessage("UBLACK_IIKO_MODULE_SENDER_SENDER")
];


$cData = new COrdersIIKO(COption::GetOptionString($module_id, "API_KEY", "", SITE_ID));

$arFilterFields = array(
    "find_site_id",
    "find_date_create",
    "find_error_text",
    //"find_error_number",
    "find_module_id",
    "find_recipients",
    "find_sended",
);

$lAdmin->InitFilter($arFilterFields);

$arFilter = array(
    "SITE_ID" => $find_site_id,
    "DATE_CREATE" => $find_date_create,
    "?ERROR_TEXT" => $find_error_text,
    //"ERROR_NUMBER" => $find_error_number,
    "MODULE_ID" => $find_module_id,
    "RECIPIENTS" => $find_recipients,
    "SENDED" => $find_sended,
);


$rsSites = CSite::GetList($siteby = "sort", $siteorder = "asc", array());

$sites = array();
while ($arSite = $rsSites->Fetch()) {
    $sites[$arSite["LID"]] = $arSite["LID"];
}

$filterFields = array(
    array(
        "id" => "SITE_ID",
        "name" => GetMessage("UBLACK_IIKO_SITE_ID"),
        "filterable" => "",
        "type" => "list",
        "items" => $sites,
        "default" => true
    ),
    array(
        "id" => "DATE_CREATE",
        "name" => GetMessage("UBLACK_IIKO_DATE_CREATE"),
        "filterable" => "",
        "type" => "date",
        "default" => true
    ),
    array(
        "id" => "ERROR_TEXT",
        "name" => GetMessage("UBLACK_IIKO_ERROR_TEXT"),
        "filterable" => "?",
        "quickSearch" => "?",
        "default" => true
    ),
    array(
        "id" => "MODULE_ID",
        "name" => GetMessage("UBLACK_IIKO_MODULE_ID"),
        "filterable" => "",
        "type" => "list",
        "items" => $MODULES_filter,
        "default" => true
    ),
    array(
        "id" => "RECIPIENTS",
        "name" => GetMessage("UBLACK_IIKO_RECIPIENTS"),
        "filterable" => "?",
        "quickSearch" => "?",
        "default" => true
    ),
    array(
        "id" => "SENDED",
        "name" => GetMessage("UBLACK_IIKO_SENDED"),
        "type" => "checkbox",
        "filterable" => "",
        "default" => true
    ),
);

$lAdmin->AddFilter($filterFields, $arFilter);


if (($arID = $lAdmin->GroupAction()) && $moduleAccessLevel == "W") {
    if (!empty($_REQUEST["action_all_rows_" . $sTableID]) && $_REQUEST["action_all_rows_" . $sTableID] === "Y") {
        $rsData = $cData->GetList(array($by => $order), $arFilter);
        while ($arRes = $rsData->Fetch()) {
            $arID[] = $arRes['ID'];
        }
    }

    foreach ($arID as $ID) {
        switch ($_REQUEST['action']) {
            case "delete":
            {

                file_put_contents($_SERVER['DOCUMENT_ROOT']."/CloseOrderByID.log",
                    print_r($cData->CloseOrderByID($_REQUEST['ID']), true));
                break;
            }
            case "resend":
            {
                file_put_contents($_SERVER['DOCUMENT_ROOT']."/ResendOrderByID.log",
                    print_r($cData->ResendOrderByID($_REQUEST['ID']), true));
//                $cData->ResendOrderByID($_REQUEST['ID']);
                break;
            }
            case "send":
            {
                file_put_contents($_SERVER['DOCUMENT_ROOT']."/SendOrderByID.log",
                    print_r($cData->SendOrderByID($_REQUEST['ID']), true));

                break;
            }
        }
    }
}


$arHeader = array(
    array(
        "id" => "ID",
        "content" => "ID",
        "sort" => "id",
        "align" => "center",
        "default" => true,
    ),
    array(
        "id" => "PROPERTY_ID_IIKO_VALUE",
        "content" => GetMessage("UBLACK_IIKO_SITE_ID"),
        "sort" => "site_id",
        "default" => true,
    ),
    array(
        "id" => "PROPERTY_STATUS_VALUE",
        "content" => GetMessage("UBLACK_IIKO_MODULE_ID"),
        "sort" => "module_id",
        "default" => true,
    ),
    array(
        "id" => "IIKO_STATUS",
        "content" => GetMessage("UBLACK_IIKO_SUBJECT"),
        "default" => true,
    ),
    array(
        "id" => "TABLE",
        "content" => GetMessage("UBLACK_IIKO_RECIPIENTS"),
        "sort" => "recipients",
        "default" => true,
    ),

    array(
        "id" => "DATE_CREATE",
        "content" => GetMessage("UBLACK_IIKO_DATE_CREATE"),
        "sort" => "date_create",
        "default" => true,
    ),
    array(
        "id" => "ERROR_TEXT",
        "content" => GetMessage("UBLACK_IIKO_ERROR_TEXT"),
        "sort" => "error_text",
        "default" => true,
    ),
);


$lAdmin->AddHeaders($arHeader);


$rsData = $cData->GetList(array($by => $order), $arFilter, ['ID', 'PROPERTY_ID_IIKO', 'PROPERTY_status']);


foreach ($rsData as $arRes) {
    $log_view_link = "iblock_element_edit.php?IBLOCK_ID=" . COption::GetOptionString(
            $module_id,
            "IBLOCK_ID",
            "",
            SITE_ID
        ) . "&type=" . COption::GetOptionString(
            $module_id,
            "IBLOCK_TYPE",
            "",
            SITE_ID
        ) . "&lang=ru&ID=" . $arRes['ID'] . "";


    $row =& $lAdmin->AddRow($arRes['ID'], $arRes, $log_view_link, GetMessage("UBLACK_IIKO_VIEW_LOG"));


    $row->AddViewField(
        "ID",
        "<a href='" . $log_view_link . "'>" . $arRes['ID'] . "</a>"
    );

    $row->AddViewField(
        "IIKO_STATUS",
        ($arRes['PROPERTY_ID_IIKO_VALUE'] ? ($arRes['IIKO_STATUS'] == "Closed" ?
            "<span style='color: red'>Закрыт</span>"
            : "<span style='color: lawngreen'>Новый</span>") : "<span style='color: grey'>Нет в IIKO</span>")
    );

    $arActions = array();
    if ($moduleAccessLevel >= "W") {
        if ($arRes['IIKO_STATUS'] != "Closed") {
            if ($arRes['PROPERTY_ID_IIKO_VALUE']) {
                $arActions[] = array(
                    "ICON" => "delete",
                    "TEXT" => GetMessage("UBLACK_IIKO_DELETE_LOG"),
                    "ACTION" => "if(confirm('" . GetMessageJS(
                            "UBLACK_IIKO_CONFIRM_DELETING"
                        ) . "')) " . $lAdmin->ActionDoGroup(
                            $arRes['ID'],
                            "delete"
                        )
                );
            } else {
                $arActions[] = array(
                    "ICON" => "send",
                    "TEXT" => GetMessage("UBLACK_IIKO_SEND_ORDER"),
                    "ACTION" => $lAdmin->ActionDoGroup($arRes['ID'], "send")
                );
            }
        } else {
            $arActions[] = array(
                "ICON" => "resend",
                "TEXT" => GetMessage("UBLACK_IIKO_SENDED_RESEND"),
                "ACTION" => $lAdmin->ActionDoGroup($arRes['ID'], "resend")
            );
        }
    }
    $row->AddActions($arActions);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && $_REQUEST["resend"] == "Y") {
    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_js.php");
    ?>

    <script>
        CloseWaitWindow();
    </script>
    <?

    require($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/main/include/epilog_admin_js.php");
}


$APPLICATION->SetTitle(GetMessage("UBLACK_IIKO_LOGS_PAGE_TITLE"));

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/ublack.core/prolog_before.php");

?>

<div id="resend_result"></div>

<?php
$lAdmin->DisplayFilter($filterFields);
$lAdmin->DisplayList();

?>








<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
?>

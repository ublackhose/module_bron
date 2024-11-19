<?
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/ublack.iiko/prolog.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/ublack.iiko/include.php");

IncludeModuleLangFile(__FILE__);

$module_id = 'ublack.iiko';
$moduleAccessLevel = $APPLICATION->GetGroupRight($module_id);

if ($moduleAccessLevel == "D") {
    $APPLICATION->AuthForm(GetMessage("UBLACK_IIKO_ACCESS_DENIED"));
}


CAdminNotify::DeleteByTag("LOGS_ARE_TOO_BIG");

$APPLICATION->SetTitle(GetMessage("UBLACK_IIKO_LOGS_PAGE_TITLE"));


if (COption::GetOptionString($module_id, "API_KEY", "", SITE_ID)) {

    $cData = new MenuIIKO(COption::GetOptionString($module_id, "API_KEY", "", SITE_ID));

    $iblockId = COption::GetOptionString($module_id, "IBLOCK_ID_MENU", "", SITE_ID);

    $menuId = COption::GetOptionString($module_id, "ID_MENU", "", SITE_ID);

    if ($iblockId) {
        $menus = $cData->GetList($iblockId);
    }


//    echo "<pre style='background:black;color:red;border-radius:10px;border:1px solid red;padding:10px;'>";
//    print_r($cData->GetList());
//    echo "</pre>";


}


require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/ublack.core/prolog_before.php");


if ($iblockId) {


    ?>

    <div class="iiko-menu">
        <div class="iiko-menu-container">
            <h1><?=GetMessage("UBLACK_IIKO_LOGS_PAGE_TITLE")?></h1>
            <? foreach ($menus as $menu) {
                ?>
                <div class="iiko-row">
                    <div class="iiko-menu-row level-0" level="0" id="<?= $menu['ID'] ?>">
                        <p><?= $menu['NAME'] ?></p>


                        <? if (!empty($menu['CATEGORY'])) {
                            ?>
                            <svg s xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" viewBox="0 0 24 24"
                                 fill="none">
                                <path d="M7 10L12 15L17 10" stroke="#000000" stroke-width="1.5" stroke-linecap="round"
                                      stroke-linejoin="round"/>
                            </svg>
                            <?
                        } else { ?>
                            <div></div>
                        <? } ?>
                    </div>
                    <? if ($menuId == $menu['ID']) { ?>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" viewBox="0 0 24 24"
                             fill="none">
                            <path d="M4 12.6111L8.92308 17.5L20 6.5" stroke="#2dc73a" stroke-width="2"
                                  stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    <? } else { ?>
                        <input type="button" class="iiko-button-change-menu" id="<?= $menu['ID'] ?>"
                               value="Использовать в модуле">
                    <? } ?>
                </div>
                <? foreach ($menu['CATEGORY'] as $category) { ?>
                    <div style="color: <?= ($category["EXISTS"] == "Y" ? "#008000;" : "#b3462b") ?>"
                         class="iiko-menu-row level-1 d-none" parent_id="<?= $menu['ID'] ?>" id="<?= $category['ID'] ?>"
                         level="1">
                        <div class="iiko-menu-head-level-1">
                            <p><?= $category['NAME'] ?></p>

                            <? if ($category['EXISTS'] == "Y") { ?>
                                <svg style="margin-left: 10px;" xmlns="http://www.w3.org/2000/svg" width="20px"
                                     height="20px" viewBox="0 0 24 24"
                                     fill="none">
                                    <path d="M4 12.6111L8.92308 17.5L20 6.5" stroke="#2dc73a" stroke-width="2"
                                          stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            <? } ?>

                            <? if (!empty($category['ITEMS'])) {
                                ?>
                                <svg class="d-absolute" xmlns="http://www.w3.org/2000/svg" width="20px" height="20px"
                                     viewBox="0 0 24 24"
                                     fill="none">
                                    <path d="M7 10L12 15L17 10" stroke="#000000" stroke-width="1.5"
                                          stroke-linecap="round"
                                          stroke-linejoin="round"/>
                                </svg>
                                <?
                            } ?>
                        </div>
                        <table style="width: 100%">
                            <? foreach ($category['ITEMS'] as $product) { ?>
                                <tr parent_id="<?= $category['ID'] ?>" class="iiko-menu-row level-2 d-none">
                                    <td>
                                        <?= $product['NAME'] ?>
                                    </td>
                                    <td>
                                        <? if ($product['EXISTS'] == "Y") { ?>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px"
                                                 viewBox="0 0 24 24"
                                                 fill="none">
                                                <path d="M4 12.6111L8.92308 17.5L20 6.5" stroke="#2dc73a"
                                                      stroke-width="2"
                                                      stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        <? } ?>
                                    </td>
                                </tr>
                                <!--                            <div style="color: --><?php //= ($product["EXISTS"] == "Y" ? "#008000;" : "#b3462b") ?><!--" class="iiko-menu-row level-2 d-none" parent_id="--><?php //= $category['ID'] ?><!--"-->
                                <!--                                 id="--><?php //= $product['ID'] ?><!--" level="2">-->
                                <!--                                <p>-->
                                <!--                                    --><?php //= $product['NAME'] ?>
                                <!--                                </p>-->
                                <!--                                --><? // if ($product['EXISTS'] == "Y") { ?>
                                <!--                                    <svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" viewBox="0 0 24 24"-->
                                <!--                                         fill="none">-->
                                <!--                                        <path d="M4 12.6111L8.92308 17.5L20 6.5" stroke="#2dc73a" stroke-width="2"-->
                                <!--                                              stroke-linecap="round" stroke-linejoin="round"/>-->
                                <!--                                    </svg>-->
                                <!--                                --><? // } ?>
                                <!---->
                                <!--                            </div>-->
                            <? } ?>
                        </table>
                    </div>

                <? } ?>
                <?
            } ?>
        </div>
    </div>


    <script>
        $(".iiko-menu-row").on("click", function () {


            if ($('[parent_id=' + $(this).attr("id") + ']').attr("parent_id")) {

                if ($('[parent_id=' + $(this).attr("id") + ']').hasClass('d-none')) {
                    $('[parent_id=' + $(this).attr("id") + ']').removeClass('d-none');
                } else {
                    $('[parent_id=' + $(this).attr("id") + ']').addClass('d-none');

                    if ($('[parent_id=' + $(this).attr("id") + ']').attr("parent_id")) {
                        $('[parent_id=' + $('[parent_id=' + $(this).attr("id") + ']').attr("id") + ']').addClass('d-none');
                    }
                }

            }

        })


        $(".iiko-button-change-menu").on("click", function () {

            let id = $(this).attr('id');
            $.ajax({
                url: '/bitrix/tools/ublack.iiko/ajax_save_menu_id.php',         /* Куда отправить запрос */
                method: 'post',             /* Метод запроса (post или get) */
                dataType: 'html',          /* Тип данных в ответе (xml, json, script, html). */
                data: {module_id: '<?=$module_id?>', id: id, code: "ID_MENU"},     /* Данные передаваемые в массиве */
                success: function (data) {   /* функция которая будет выполнена после успешного запроса.  */
                    if (data == "true") {
                        location.reload()
                    }
                    // if (JSON.parse(data) )
                }
            });
        });
    </script>

    <style>

        .iiko-menu-row.level-0 {
            font-size: 16px;
        }

        .iiko-menu-head-level-1 {
            width: 100%;
            display: flex;
            justify-content: flex-start;
            padding: 10px;
            position: relative;
            font-size: 16px;
        }

        .iiko-menu {
            color: #5c6470;
            border-bottom: 2px #eef2f4 solid;
            background-color: #fff;
        }

        .iiko-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 900px;
        }

        .iiko-menu-container {
            padding: 11px;
        }

        .iiko-menu-row p {
            margin: 0px;
        }

        .iiko-menu-row {
            border: 1px solid #dbdbdb;
            margin-bottom: 5px;
            width: 50%;
            display: flex;
            align-content: center;
            justify-content: space-between;
            align-items: center;
            border-radius: 4px;
            padding: 15px 10px;
            cursor: pointer;
        }

        .iiko-menu-row.level-1 {
            margin-left: 20px;

            flex-direction: column;
            width: calc(100% - 40px);
        }

        .iiko-menu-head-level-1 svg.d-absolute {
            position: absolute;
            right: 23px;
        }


        .iiko-menu-row.level-2 {
            background: #ffffff;
            display: flex;
            width: -webkit-fill-available;
            font-size: 14px;
            cursor: default;
        }

        .d-none {
            display: none !important;
        }

        .ui-alert.ui-alert-primary.ui-alert-icon-info {
            padding: 10px;
        }

    </style>
    <?php

} else {

    $e = new CAdminException("");
//    $message  = new CAdminMessage("КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ КУРИТЬ", $e);
    $message = new CAdminMessage("Не настроено меню в настройках модуля", $e);
    echo $message->Show();

}


require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
?>
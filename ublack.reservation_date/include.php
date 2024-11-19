<?
IncludeModuleLangFile(__FILE__);



global $DB;

CModule::AddAutoloadClasses(
    "ublack.reservation_date",
    array(
        "UBLACK\Reservation\mainiblock" => "lib/mainiblock.php",
        "UBLACK\Reservation\Iblock\CreatinoIblock" => "lib/iblock/сreationiblock.php",
        "UBLACK\Reservation\Iblock"=>"lib/iblock.php",
        "UBLACK\Reservation\Admin\Iblock\DateTab" => "lib/admin/iblock/datetab.php",
        "UBLACK\Reservation\Admin\Iblock\TabSet" => "lib/admin/iblock/tabset.php",
        "UBLACK\Reservation\ORM\PriceDateTable"=>"lib/orm/pricedate.php",
        "UBLACK\Reservation\date\WorkingsDate"=>"lib/date/WorkingsDate.php",
    )
);
class CReservation
{
    function ShowPanel()
    {
        if ($GLOBALS["USER"]->IsAdmin() && COption::GetOptionString("main", "wizard_solution", "", SITE_ID) == "simplehotelsite")
        {
            $GLOBALS["APPLICATION"]->AddPanelButton(array(
                "HREF" => "/bitrix/admin/settings.php?mid=reservation_date&lang=".LANGUAGE_ID."&siteTabControl_active_tab=opt_site_".SITE_ID."&".bitrix_sessid_get(),
                "ID" => "demo_simplehotelsite_wizard",
                "ICON" => "bx-panel-site-wizard-icon",
                "MAIN_SORT" => 2500,
                "TYPE" => "BIG",
                "SORT" => 10,
                "ALT" => GetMessage("SIMPLET_BUTTON_DESCRIPTION"),
                "TEXT" => GetMessage("SIMPLET_BUTTON_NAME"),
                "MENU" => array(),
            ));
        }
    }
}

?>
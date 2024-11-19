<?
IncludeModuleLangFile(__FILE__);
$module_id = "ublack.reservation_date";

$moduleAccessLevel = $APPLICATION->GetGroupRight($module_id);

if($moduleAccessLevel > "D")
{
	if(!CModule::IncludeModule($module_id))
		return false;
	
	$GLOBALS['APPLICATION']->SetAdditionalCSS('/bitrix/panel/ublack.reservation_date/menu.css');
	
	$aMenuItems[] = array(
		"module_id" => $module_id,
		"icon" => "sender_menu_icon",
		"text" => GetMessage("UBLACK_SMTP_INNER_MENU_SEND_TEXT"),
		"url" => "ublack.reservation_date_bron.php?lang=".LANGUAGE_ID,
	);
	$aMenuItems[] = array(
		"module_id" => $module_id,
		"icon" => "update_marketplace",
		"text" => GetMessage("UBLACK_SMTP_INNER_MENU_LOGS_TEXT"),
		"url" => "ublack.reservation_date_logs.php?lang=".LANGUAGE_ID,
		"more_url" => array("ublack.reservation_date_log_view.php"),
	);
	if($moduleAccessLevel > "S")
	{
		$aMenuItems[] = array(
			"module_id" => $module_id,
			"icon" => "fileman_sticker_icon",
			"text" => GetMessage("UBLACK_SMTP_INNER_MENU_DEBUG_TEXT"),
			"url" => "ublack.reservation_date_debug.php?lang=".LANGUAGE_ID,
		);
	}

	$aMenu = array(
		"parent_menu" => "global_menu_ublack",
		"section" => $module_id,
		"sort" => 700,
		"text" => GetMessage("UBLACK_SMTP_MAIN_MENU_TEXT"),
		"icon" => "ublack_reservation_date",
		"page_icon" => "",
		"items_id" => "ublack_reservation_date",
		"more_url" => array(),
		"items" => $aMenuItems
	);

	return $aMenu;
}

return false;
?> 

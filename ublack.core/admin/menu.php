<?
use Bitrix\Main\Localization\Loc;

AddEventHandler('main', 'OnBuildGlobalMenu', 'OnBuildGlobalMenuHandlerUblack');

function OnBuildGlobalMenuHandlerUblack(&$arGlobalMenu, &$arModuleMenu)
{
	$module_id = 'ublack.core';
	
	$GLOBALS['APPLICATION']->SetAdditionalCss("/bitrix/panel/".$module_id."/menu/style.css");
	
	if($GLOBALS['APPLICATION']->GetGroupRight($module_id) >= 'R')
	{
//		if($GLOBALS['APPLICATION']->GetGroupRight('main') >= 'W')
//		{
//			$items = array(
//				array(
//					"module_id" => $MODULE_ID,
//					"text" => GetMessage("UBLACK_CORE_INNER_MENU_SETTINGS_TEXT"),
//					"url" => "settings.php?lang=ru&mid=ublack.core&lang=".LANGUAGE_ID,
//					"icon" => "uи til_menu_icon",
//				),
//			);
//		}
		$arMenu = array(
			'menu_id' => 'global_menu_aspro_ublack',
			'text' => Loc::getMessage('UBLACK_CORE_SUPPORT_MENU_TEXT'),
			'sort' => 0,
			'items_id' => 'global_menu_aspro_ublack_core',
			"icon" => "support_menu_icon",
			"url" => "settings.php?lang=ru&mid=ublack.core&lang=".LANGUAGE_ID,
//			"items" => $items
		);
	}
	
	$arGlobalMenu['global_menu_ublack'] = array(
		'menu_id' => 'global_menu_ublack',
		'text' => Loc::getMessage('UBLACK_CORE_GLOBAL_UBLACK_MENU_TEXT'),
		'sort' => 300,
		'items_id' => 'global_menu_ublack_items',
	);

	$arGlobalMenu['global_menu_ublack']['items'][$module_id] = $arMenu;
}
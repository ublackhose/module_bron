<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

//$module_id = 'ublack.core';
//
//$moduleAccessLevel = $APPLICATION->GetGroupRight($module_id);
//
//if ($moduleAccessLevel == "D")
//    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
//
//require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$module_id.'/include.php');
//require_once($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/'.$module_id.'/prolog.php');
//
//$arTabs = CWebprostorCoreOptions::GetTabs();
//
//$groupsMain = Array(
//	"MAIN" => GetMessage("UBLACK_CORE_OPTIONS_GROUP_MAIN"),
//	//"ADDITIONAL" => GetMessage("UBLACK_CORE_OPTIONS_GROUP_ADDITIONAL"),
//);
//$groupsSites = Array(
//	"ADDITIONAL" => GetMessage("UBLACK_CORE_OPTIONS_GROUP_ADDITIONAL"),
//);
//
//$arGroups = CWebprostorCoreOptions::GetGroups($groupsSites, $arTabs, $groupsMain);
//
//$optionsMain = Array(
//	Array(
//		'CODE' => "THIS_SITE_SUPPORT",
//		'GROUP' => "MAIN",
//		'TYPE' => 'TEXTAREA',
//		'COLS' => '60',
//		'TITLE' => GetMessage("UBLACK_CORE_OPTION_THIS_SITE_SUPPORT"),
//		'SORT' => '0',
//	),
//);
//
//$optionsMain[] = [
//	'CODE' => "HIDE_DONATION_ALERT",
//	'TITLE' => GetMessage("UBLACK_CORE_OPTION_HIDE_DONATION_ALERT"),
//	'GROUP' => "MAIN",
//	'TYPE' => 'CHECKBOX',
//	'SORT' => '10',
//];
//
//$optionsSites = [];
//
//$obUserType = new \CUserTypeEntity;
//$obEnum = new \CUserFieldEnum;
//$rsUT = $obUserType -> GetList(
//	["SORT" => "ASC"],
//	["ENTITY_ID" => "UBLACK_CORE", 'LANG' => LANGUAGE_ID]
//);
//while($arUT = $rsUT->Fetch())
//{
//	//echo '<pre>';var_dump($arUT);echo '</pre>';
//	$rows = 1;
//	$values = false;
//	switch($arUT["USER_TYPE_ID"])
//	{
//		case('boolean'):
//			$additional_type = 'CHECKBOX';
//			break;
//		case('integer'):
//			$additional_type = 'INT';
//			break;
//		case('date'):
//			$additional_type = 'CALENDAR';
//			break;
//		case('datetime'):
//			$additional_type = 'CALENDAR_DATE';
//			break;
//		case('enumeration'):
//			if($arUT["MULTIPLE"] == 'Y')
//			{
//				$additional_type = 'MSELECT';
//			}
//			else
//			{
//				$additional_type = 'SELECT';
//				$values = [
//					'REFERENCE' => [GetMessage("UBLACK_CORE_OPTION_SELECT_PLACEHOLDER")],
//					'REFERENCE_ID' => ['']
//				];
//			}
//
//			$rsEnum = $obEnum->GetList(array(), array("USER_FIELD_ID" => $arUT["ID"]));
//
//			while($arEnum = $rsEnum->Fetch())
//			{
//				//echo '<pre>';var_dump($arEnum);echo '</pre>';
//				$values['REFERENCE'][] = $arEnum["VALUE"];
//				$values['REFERENCE_ID'][] = $arEnum["XML_ID"];
//			}
//
//			break;
//		case('file'):
//			$additional_type = 'FILE';
//			break;
//		case('string'):
//			$additional_type = 'TEXT';
//			$rows = $arUT["SETTINGS"]['ROWS'];
//			if($rows > 0)
//				$cols = $arUT["SETTINGS"]['SIZE'];
//			break;
//		case('html'):
//			$additional_type = 'TEXT';
//			$rows = 3;
//			break;
//		default:
//			$additional_type = 'TEXT';
//			break;
//	}
//
//	$optionsSites[] = [
//		'CODE' => $arUT["FIELD_NAME"],
//		'GROUP' => "ADDITIONAL",
//		'TYPE' => $additional_type,
//		'SORT' => $arUT["SORT"],
//		'TITLE' => $arUT["EDIT_FORM_LABEL"] ? $arUT["EDIT_FORM_LABEL"].' ['.$arUT["FIELD_NAME"].']' : $arUT["FIELD_NAME"],
//		'ROWS' => $rows,
//		'COLS' => $cols,
//		'VALUES' => $values,
//		'SIZE' => $arUT["SETTINGS"]['SIZE'],
//		'MAXLENGTH' => $arUT["SETTINGS"]['MAXLENGTH'],
//		'USER_FIELD' => "Y",
//		'NOTES' => '\Bitrix\Main\Config\Option::get("ublack.core", "'.$arUT["FIELD_NAME"].'");',
//	];
//
//	unset($cols);
//}
//
//$optionsSites[] = Array(
//	'CODE' => "ADD_ADDITIONAL_OPTION",
//	'GROUP' => "ADDITIONAL",
//	'TYPE' => 'CUSTOM',
//	'NOTES' => GetMessage("UBLACK_CORE_OPTION_ADD_ADDITIONAL_OPTION", ["#URL#" => "/bitrix/admin/userfield_edit.php?lang=".LANGUAGE_ID."&ENTITY_ID=UBLACK_CORE&back_url=%2Fbitrix%2Fadmin%2Fsettings.php%3Flang%3D".LANGUAGE_ID."%26mid%3Dublack.core"]),
//	'SORT' => '9999',
//);
//
//$arOptions = CWebprostorCoreOptions::GetOptions($optionsSites, $arTabs, $optionsMain);
//
//$opt = new CWebprostorCoreOptions($module_id, $arTabs, $arGroups, $arOptions, $showMainTab = true, $showRightsTab = true);
//$opt->ShowHTML();
//
//if ($moduleAccessLevel >= 'R')
//{
//	if($REQUEST_METHOD=="POST" && strlen($save)>0 && check_bitrix_sessid())
//	{
//		$THIS_SITE_SUPPORT = COption::GetOptionString($module_id, "THIS_SITE_SUPPORT", '');
//		if(strlen($THIS_SITE_SUPPORT)>0)
//		{
//			$destination_dir = file_exists($_SERVER["DOCUMENT_ROOT"].'/local/php_interface/') ? 'local' : 'bitrix';
//			file_put_contents($_SERVER["DOCUMENT_ROOT"].'/'.$destination_dir.'/php_interface/this_site_support.php', $THIS_SITE_SUPPORT);
//		}
//	}
//}
?>
<?
IncludeModuleLangFile(__FILE__);
Class ublack_core extends CModule
{
	const MODULE_ID = 'ublack.core';
	var $MODULE_ID = 'ublack.core';
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $strError = '';
	var $MODULE_GROUP_RIGHTS = "Y";

	function __construct()
	{
		$arModuleVersion = array();
		include(dirname(__FILE__) . "/version.php");
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = GetMessage("UBLACK_CORE_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("UBLACK_CORE_MODULE_DESC");

		$this->PARTNER_NAME = GetMessage("UBLACK_CORE_PARTNER_NAME");
		$this->PARTNER_URI = GetMessage("UBLACK_CORE_PARTNER_URI");
	}

	function InstallDB($arParams = array())
	{
		return true;
	}

	function UnInstallDB($arParams = array())
	{
		return true;
	}

	function InstallEvents()
	{
		return true;
	}

	function UnInstallEvents()
	{
		return true;
	}

	function InstallFiles($arParams = array())
	{
		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID.'/admin'))
		{
			if ($dir = opendir($p))
			{
				while (false !== $item = readdir($dir))
				{
					if ($item == '..' || $item == '.' || $item == 'menu.php')
						continue;
					file_put_contents($file = $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/'.self::MODULE_ID.'_'.$item,
					'<'.'? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/'.self::MODULE_ID.'/admin/'.$item.'");?'.'>');
				}
				closedir($dir);
			}
		}
		CopyDirFiles($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/".self::MODULE_ID."/install/bitrix/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/", true, true);
		return true;
	}

	function UnInstallFiles()
	{
		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID.'/admin'))
		{
			if ($dir = opendir($p))
			{
				while (false !== $item = readdir($dir))
				{
					if ($item == '..' || $item == '.')
						continue;
					unlink($_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/'.self::MODULE_ID.'_'.$item);
				}
				closedir($dir);
			}
		}
		DeleteDirFilesEx('/bitrix/panel/'.self::MODULE_ID.'/');
		DeleteDirFilesEx('/bitrix/fonts/'.self::MODULE_ID.'/');
		DeleteDirFilesEx('/bitrix/css/'.self::MODULE_ID.'/');
		return true;
	}

	function DoInstall()
	{
		global $APPLICATION;
		
		if(
			$this->InstallFiles()
		)
		{
			RegisterModule(self::MODULE_ID);
			return true;
		}
		else
			return false;
	}

	function DoUninstall()
	{
		global $APPLICATION;
		
		if(
			$this->UnInstallFiles() 
		)
		{
			COption::RemoveOption(self::MODULE_ID);
			CAdminNotify::DeleteByModule(self::MODULE_ID);
			UnRegisterModule(self::MODULE_ID);
			return true;
		}
		else
			return false;
	}
}
?>

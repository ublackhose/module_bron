<?php

use \Bitrix\Main\Loader;
use \Bitrix\Main\Application;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();


class Calendar extends CBitrixComponent
{
    private function _checkModules() {
        if (   !Loader::includeModule('iblock')
        ) {
            throw new \Exception('Не загружены модули необходимые для работы модуля');
        }

        return true;
    }




}
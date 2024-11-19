<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');


use  \Bitrix\Main\Type\Date;


$request = Bitrix\Main\Application::getInstance()->getContext()->getRequest();


$arParams = json_decode($request->get('params'), true);

$result = null;


print_r(json_encode($result));
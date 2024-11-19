<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');


use  \Bitrix\Main\Type\Date;


$request = Bitrix\Main\Application::getInstance()->getContext()->getRequest();


$arParams = json_decode($request->get('actionParam'), true);




if ($request->get('action') == "CreateRoom") {
    $arSelect = array('ID', 'NAME', "PROPERTY_PRICES");
    $arFilter = array("IBLOCK_ID" => $arParams['IBLOCK_ID'], "ID" => $arParams['ELEMENT_ID'], 'ACTIVE' => 'Y');
    $res = CIBlockElement::GetList(array(), $arFilter, false, array(), $arSelect);



    while ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $price = CIBlockElement::GetByID($arFields['PROPERTY_PRICES_VALUE'])->Fetch();
        $priceValue = CIBlockElement::GetProperty($price['IBLOCK_ID'], $price['ID'], array("sort" => "asc"), array());
        $arPrice = null;
        while ($row = $priceValue->Fetch()) {
            $arPrice[$row['NAME']] = $row['VALUE'];
        }
        $arFields['PRICE'] = $arPrice;
        $result[] = $arFields;
    }
} elseif ($request->get('action') == "GetReservationDateRoom") {

    $result['PARAMS'] = $request->get('actionParam');

    $arSelect = array('ID', 'NAME', "PROPERTY_PRICES");
    $arFilter = array("ID" => $arParams['ELEMENT_ID'], 'ACTIVE' => 'Y');
    $res = CIBlockElement::GetList(array(), $arFilter, false, array(), $arSelect);


    if ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $price = CIBlockElement::GetByID($arFields['PROPERTY_PRICES_VALUE'])->Fetch();
        $priceValue = CIBlockElement::GetProperty($price['IBLOCK_ID'], $price['ID'], array("sort" => "asc"), array());
        $arPrice = null;
        while ($row = $priceValue->Fetch()) {
            $arPrice[$row['NAME']] = $row['VALUE'];
        }
        $arFields['PRICE'] = $arPrice;
        $result['PARAMS'] = $arFields;
    }


    $actionData = $request->get('actionData');
    $date_prev = $FieldDateIn = new \Bitrix\Main\Type\Date();
    $arSelect = array("ID", "IBLOCK_ID", "NAME", "PROPERTY_DATE_IN", "PROPERTY_DATE_OUT");
    $arFilter = array("IBLOCK_ID" => 26, 'PROPERTY_NOMER' => $arParams['ELEMENT_ID']);
    $res = CIBlockElement::GetList(array(), $arFilter, false, array(), $arSelect);

    while ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $FieldDateIn = new \Bitrix\Main\Type\Date($arFields['PROPERTY_DATE_IN_VALUE'] , "d.m.Y");
        $FieldDateOut = new \Bitrix\Main\Type\Date($arFields['PROPERTY_DATE_OUT_VALUE'], "d.m.Y");
        if ($FieldDateOut->format('Y.m.d') >= $date_prev->format('Y.m.d')) {
            $result['ITEMS'][] = $arFields;
        }
    }


} elseif ($request->get('action') == "GetReservationDate") {
    $actionData = $request->get('actionData');

    $date_last = $actionData['data_last']['data_last'];
    $date_prev = $actionData['data_last']['data_prev'];


    $arSelect = array("ID", "IBLOCK_ID", "NAME", "PROPERTY_DATE_IN", "PROPERTY_DATE_OUT");
    $arFilter = array("IBLOCK_ID" => $arParams['IBLOCK_ID_BRON'], 'PROPERTY_NOMER' => $actionData['id']);
    $res = CIBlockElement::GetList(array(), $arFilter, false, array(), $arSelect);


    $arReservation = null;
    while ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();


        $FieldDateIn = new \Bitrix\Main\Type\Date($arFields['PROPERTY_DATE_IN_VALUE'], "Y-m-d");
        $FieldDateOut = new \Bitrix\Main\Type\Date($arFields['PROPERTY_DATE_OUT_VALUE'], "Y-m-d");

        if ($FieldDateOut->format('Y.m.d') >= $date_prev) {
            $result[] = $arFields;
        }


    }


} elseif ($request->get('action') == "getRoom") {
    $actionDatas = $request->get('actionData');


    foreach ($actionDatas as $actionData) {


        $date = $actionData['date'];

        $bron = false;

        $arSelect = array("ID", "IBLOCK_ID", "NAME", "PROPERTY_DATE_IN", "PROPERTY_DATE_OUT");
        $arFilter = array("IBLOCK_ID" => $arParams['IBLOCK_ID_BRON'], 'PROPERTY_NOMER' => $actionData['id']);
        $res = CIBlockElement::GetList(array(), $arFilter, false, array(), $arSelect);




        $arReservation = null;
        while ($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();
            $FieldDateIn = new \Bitrix\Main\Type\Date($arFields['PROPERTY_DATE_IN_VALUE'], "Y-m-d");
            $FieldDateOut = new \Bitrix\Main\Type\Date($arFields['PROPERTY_DATE_OUT_VALUE'], "Y-m-d");

            if (($FieldDateIn->format('Y.m.d') <= $date && $FieldDateOut->format('Y.m.d') >= $date)) {
                $bron = true;
            }
        }


        $arSelect = array('ID', "PROPERTY_PRICES");
        $arFilter = array("IBLOCK_ID" => $arParams['IBLOCK_ID'],  'ID' => $actionData['id']);
        $res = CIBlockElement::GetList(array(), $arFilter, false, array(), $arSelect);

        while ($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();
            $price = CIBlockElement::GetByID($arFields['PROPERTY_PRICES_VALUE'])->Fetch();
            $priceValue = CIBlockElement::GetProperty($price['IBLOCK_ID'], $price['ID'], array("sort" => "asc"), array());
            $arPrice = null;
            while ($row = $priceValue->Fetch()) {
                $arPrice[$row['NAME']] = $row['VALUE'];
            }
            $arFields['PRICE'] = $arPrice;
            $arFields['bron'] = $bron;
            $arFields['data'] = $actionData['date'];
            $result[] = $arFields;
        }
    }
}


print_r(json_encode($result));
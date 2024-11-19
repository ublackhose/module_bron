<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');


use  \Bitrix\Main\Type\Date;


$request = Bitrix\Main\Application::getInstance()->getContext()->getRequest();


$arParams = json_decode($request->get('params'),true);


if($request->get('action') == "CreateRooms"){

    $arSelect = array('ID', 'NAME', "PROPERTY_PRICES");
    $arFilter = array("IBLOCK_ID" => $arParams['IBLOCK_ID'], 'ACTIVE'=>'Y');
    $res = CIBlockElement::GetList(array(), $arFilter, false, array(), $arSelect);

    while ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();


        $price = CIBlockElement::GetByID($arFields['PROPERTY_PRICES_VALUE'])->Fetch();
        file_put_contents($_SERVER['DOCUMENT_ROOT']."/lol.log",print_r($arFields,true));

        $priceValue = CIBlockElement::GetProperty($price['IBLOCK_ID'], $price['ID'], array("sort" => "asc"), Array());
        $arPrice = null;

        while ($row = $priceValue->Fetch())
        {
            $arPrice[$row['NAME']] = $row['VALUE'];
        }
        $arFields['PRICE'] = $arPrice;
        $result[] = $arFields;
    }



}elseif ($request->get('action') == "GetReservationDate"){

    $actionData = $request->get('actionData');

    $date_last = $actionData['data_last']['data_last'];
    $date_prev = $actionData['data_last']['data_prev'];



    $arSelect = array("ID", "IBLOCK_ID", "NAME", "PROPERTY_DATE_IN","PROPERTY_DATE_OUT");
    $arFilter = array("IBLOCK_ID" => 26,"ACTIVE"=>"Y", 'PROPERTY_NOMER'=> $actionData['id']);
    $res = CIBlockElement::GetList(array(), $arFilter, false, array(), $arSelect);


    $arReservation = null;
    while ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $FieldDateIn = new \Bitrix\Main\Type\Date($arFields['PROPERTY_DATE_IN_VALUE']);
        if(($FieldDateIn->format('Y.m.d') >= $date_prev  && $FieldDateIn->format('Y.m.d') <= $date_last)
        || ($date_prev >= $FieldDateIn->format('Y.m.d')  && $FieldDateIn->format('Y.m.d') <= $date_prev)
        ){
            $result[] = $arFields;
        }
    }


}elseif ($request->get('action') == "getRoom"){

    $actionData = $request->get('actionData');
    $date = $actionData['date'];

    $bron = false;

    $arSelect = array("ID", "IBLOCK_ID", "NAME", "PROPERTY_DATE_IN","PROPERTY_DATE_OUT");
    $arFilter = array("IBLOCK_ID" => 26,"ACTIVE"=>"Y" ,'PROPERTY_NOMER'=> $actionData['id']);
    $res = CIBlockElement::GetList(array(), $arFilter, false, array(), $arSelect);


    $arReservation = null;
    while ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $FieldDateIn = new \Bitrix\Main\Type\Date($arFields['PROPERTY_DATE_IN_VALUE']);
        $FieldDateOut = new \Bitrix\Main\Type\Date($arFields['PROPERTY_DATE_OUT_VALUE']);

        if(($FieldDateIn->format('Y.m.d') <= $date  && $FieldDateOut->format('Y.m.d') >= $date)){
            $bron = true;
        }
    }


        $arSelect = array('ID',"PROPERTY_PRICES");
        $arFilter = array("IBLOCK_ID" => $arParams['IBLOCK_ID'], 'ACTIVE'=>'Y', 'ID'=>$actionData['id']);
        $res = CIBlockElement::GetList(array(), $arFilter, false, array(), $arSelect);

        while ($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();
            $price = CIBlockElement::GetByID($arFields['PROPERTY_PRICES_VALUE'])->Fetch();
            $priceValue = CIBlockElement::GetProperty($price['IBLOCK_ID'], $price['ID'], array("sort" => "asc"), Array());
            $arPrice = null;
            while ($row = $priceValue->Fetch())
            {
                $arPrice[$row['NAME']] = $row['VALUE'];
            }
            $arFields['PRICE'] = $arPrice;
            $arFields['bron'] =$bron;
            $arFields['data'] = $actionData['date'];
            $result = $arFields;
        }


}


print_r(json_encode($result));
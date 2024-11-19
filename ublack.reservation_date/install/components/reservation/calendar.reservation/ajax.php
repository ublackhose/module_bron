<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');


use  \Bitrix\Main\Type\Date;


$request = Bitrix\Main\Application::getInstance()->getContext()->getRequest();


$arParams = json_decode($request->get('params'), true);


function getMounth($date)
{
    $mounth_name = ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'];
    return $mounth_name[intval($date->format("m")) - 1];
}


if ($request->get('action') == "getRoom") {
    $actionDatas = $request->get('actionData');
    $result = array();


    foreach ($actionDatas as $actionData) {


        $date = $actionData['date'];


        $out = \DateTime::createFromFormat('Y.m.d', $date)->format('d.m.Y');


        $d = new \Bitrix\Main\Type\Date($out);


        $bron = false;


        $r = array();

        $r['data'] = $date;

        $arSelect = array('ID', "PROPERTY_PRICES");
        $arFilter = array("IBLOCK_ID" => 21, 'ACTIVE' => 'Y');
        $res = CIBlockElement::GetList(array(), $arFilter, false, array(), $arSelect);

        while ($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();
            $arSelect_2 = array("ID", "IBLOCK_ID", "NAME", "PROPERTY_DATE_IN", "PROPERTY_DATE_OUT");
            $arFilter_2 = array("IBLOCK_ID" => 26, 'PROPERTY_NOMER' => $arFields['ID']);
            $res_2 = CIBlockElement::GetList(array(), $arFilter_2, false, array(), $arSelect_2);
            $arReservation = array();
            while ($ob_2 = $res_2->GetNextElement()) {
                $arTest = null;
                $arFields_2 = $ob_2->GetFields();
                $FieldDateIn = new \Bitrix\Main\Type\Date($arFields_2['PROPERTY_DATE_IN_VALUE']);
                $FieldDateOut = new \Bitrix\Main\Type\Date($arFields_2['PROPERTY_DATE_OUT_VALUE']);
                if (($FieldDateIn->format('Y.m.d') <= $date) && ($FieldDateOut->format('Y.m.d') >= $date)) {
                    $bron = true;
                    $arReservation[] = $arFields_2;
                }
            }


            if (empty($arReservation)) {
                $price = CIBlockElement::GetByID($arFields['PROPERTY_PRICES_VALUE'])->Fetch();
                $priceValue = CIBlockElement::GetProperty($price['IBLOCK_ID'], $price['ID'], array("sort" => "asc"), array()
                );
                $arPrice = null;

                while ($row = $priceValue->Fetch()) {
                    $arPrice[$row['NAME']] = $row['VALUE'];
                }
                $arFields['PRICE'] = $arPrice[getMounth($d)];
                $arFields['bron'] = $bron;
                $arFields['data'] = $actionData['date'];
                $r[] = $arFields;
            }
        }
        $result[] = $r;
    }


}


print_r(json_encode($result));
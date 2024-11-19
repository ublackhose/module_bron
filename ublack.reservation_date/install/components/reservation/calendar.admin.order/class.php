<?php

use \Bitrix\Main\Loader;
use \Bitrix\Main\Application;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();


class CalendarAdmin extends CBitrixComponent
{
    private function _checkModules() {
        if (   !Loader::includeModule('iblock')
        ) {
            throw new \Exception('Не загружены модули необходимые для работы модуля');
        }

        return true;
    }

    public function executeComponent(){





        if(!isset($_REQUEST['ORDER_ID'])){
            $error = "Не передан заказ.";
        }

        global $USER;
        if (!$USER->IsAdmin()) {
            $error = "У вас нет доступа к данной странице.";
        };




        if($_REQUEST['ORDER_DEL'] == 'Y'){
            if(CIBlockElement::Delete($_REQUEST['ORDER_ID'])){
                header("Location: https://".$_SERVER['HTTP_HOST']."/booking/", true, 301);
            }
        }
        else if (isset($_REQUEST['order_form_update']) && check_bitrix_sessid() && !isset($_REQUEST['send_pay_url'])) {
            $PROP = $_REQUEST;
            $ORDER_ID = $PROP['ORDER_ID'];
            $ACTIVE = $PROP['ACTIVE'];
            unset($PROP['sessid']);
            unset($PROP['order_form_update']);
            unset($PROP['ORDER_EDIT']);
            unset($PROP['ORDER_ID']);
            unset($PROP['ACTIVE']);

            $PROP['DATE_IN'] = str_replace('-', '.', $PROP['DATE_IN']);
            $PROP['DATE_OUT'] = str_replace('-', '.', $PROP['DATE_OUT']);


            $el = new CIBlockElement;
            global $USER;
            $arLoadProductArray = Array("MODIFIED_BY"    => $USER->GetID(), "ACTIVE"  => $ACTIVE);
            $el->Update($ORDER_ID, $arLoadProductArray);

            CIBlockElement::SetPropertyValuesEx($ORDER_ID, false, $PROP);
        }

        $arSelect = array("ID", "IBLOCK_ID", "NAME", "PROPERTY_*", 'ACTIVE');
        $arFilter = array("IBLOCK_ID" => 26, "ID" => $_REQUEST['ORDER_ID']);
        $res = CIBlockElement::GetList(array(), $arFilter, false, array(), $arSelect);
        while ($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();
            $arProps = $ob->GetProperties();

            $ORDER['ACTIVE'] = $arFields['ACTIVE'];
            foreach ($arProps as $arProp) {
                if ($arProp['CODE'] == 'COMMENT') {
                    if (!empty($arProp['VALUE']['TEXT'])){
                        $order_data[$arProp['CODE']] = $arProp['VALUE']['TEXT'];
                    }elseif (!empty($arProp['VALUE'])){
                        $order_data[$arProp['CODE']] = $arProp['VALUE'];
                    }
                } elseif ($arProp['CODE'] == 'TRANSFER') {
                    $ORDER[$arProp['CODE']] = $arProp['VALUE_ENUM_ID'];
                } elseif ($arProp['CODE'] == 'NOMER') {
                    $ORDER['NOMER'] = $arProp['VALUE'];
                    $res1 = CIBlockElement::GetByID($ORDER['NOMER']);
                    if ($ar_res1 = $res1->GetNext()) {
                        $ORDER['NOMER_NAME'] = $ar_res1['NAME'];
                    }
                } elseif ($arProp['CODE'] == 'SUM') {
                    $ORDER['SUM'] = $arProp['VALUE'];
                    $ORDER['SUM_UNFORMAT'] = preg_replace("/[^,.0-9]/", '', $arProp['VALUE']);
                } else {
                    $ORDER[$arProp['CODE']] = $arProp['VALUE'];
                }
            }
        }

        if(!$ORDER){
            header('Location: /booking/');
        }

        if (isset($_REQUEST['send_pay_url']) && check_bitrix_sessid() && $_REQUEST['PAY_TYP'] == 35) { //Ссылка на оплату

            if ($ORDER['AVANS'] < 1) {
                $ORDER['avans_error'] = 'Заполните аванс !';
            }
            if (isset($_GET['ORDER_ID']) && $ORDER['AVANS'] > 0) {
                $ORDER['send_pay_href'] = $_REQUEST['OTHER_PAY_URL'];
                $ORDER['ORDER_ID'] = $_GET['ORDER_ID'];
                if ($ORDER['TRANSFER'] == 29) {
                    $ORDER['TRANSFER'] = 'Да';
                } else {
                    $ORDER['TRANSFER'] = 'Нет';
                }

                CEvent::Send("send_pay_url", 's1', $ORDER); // Письмо с ссылкой на оплату
            }
        } elseif (isset($_REQUEST['send_pay_url']) && check_bitrix_sessid() && $_REQUEST['PAY_TYP'] == 34) {


            if ($ORDER['AVANS'] < 1) {
                $ORDER['avans_error'] = 'Заполните аванс !';
            }
            if (isset($_GET['ORDER_ID']) && $ORDER['AVANS'] > 0) {

                require __DIR__ . '/payment.php';
                $pay_res = CreateOrder($_GET['ORDER_ID'], $ORDER);
                $pay_res_arr = json_decode($pay_res, true);
                if (isset($pay_res_arr['formUrl'])) {
                    $ORDER['send_pay_url'] = 'Ссылка на оплату отправленна заказчику на почту';
                    $ORDER['send_pay_href'] = $pay_res_arr['formUrl'];
                    $ORDER['ORDER_ID'] = $_GET['ORDER_ID'];

                    if (isset($pay_res_arr['orderId'])) {
                        CIBlockElement::SetPropertyValuesEx($_REQUEST['ORDER_ID'], false, array('OREDER_ID_SBER' => $pay_res_arr['orderId']));
                    }

                    if ($ORDER['TRANSFER'] == 29) {
                        $ORDER['TRANSFER'] = 'Да';
                    } else {
                        $ORDER['TRANSFER'] = 'Нет';
                    }

                    CEvent::Send("send_pay_url", 's1', $ORDER); // Письмо с ссылкой на оплату сбер
                }
            }
        }
        if (isset($_REQUEST['pay_success']) && isset($_REQUEST['orderId'])) { // Произошла оплата
            if ($_REQUEST['orderId'] != '') {
                $arSelect = array("ID", "IBLOCK_ID");
                $arFilter = array("IBLOCK_ID" => 26, 'PROPERTY_OREDER_ID_SBER' => $_REQUEST['orderId']);
                $res = CIBlockElement::GetList(array(), $arFilter, false, array(), $arSelect);
                while ($ob = $res->GetNextElement()) {
                    $arFields = $ob->GetFields();
                    $arProps = $ob->GetProperties();

                    $data['EMAIL'] = $arProps['EMAIL']['VALUE'];
                    $data['ORDER_ID'] = $arFields['ID'];

                    CIBlockElement::SetPropertyValuesEx($arFields['ID'], false, array('PAYED' => 33));
                    $this->arResult['FORM'] = 6;

                    CEvent::Send("order_pay", 's1', $data); // Письмо что заказ оплачен
                }
            }
            file_put_contents(__DIR__ . '/logs/pay_success.txt', print_r($_REQUEST, true), FILE_APPEND);
        }
        if (isset($_REQUEST['pay_error'])) { // Произошла ошибка оплаты
            $this->arResult['FORM'] = 7;
            file_put_contents(__DIR__ . '/logs/pay_error.txt', print_r($_REQUEST, true), FILE_APPEND);
        }
        if (isset($_GET['ORDER_ID']) && isset($_GET['ORDER_DELETE'])) { //Отмена бронирования
            CIBlockElement::Delete($_GET['ORDER_ID']);
            file_put_contents(__DIR__ . '/logs/delete_booking.txt', print_r($_GET, true), FILE_APPEND);
            CEvent::Send("delete_booking", 's1', ['ORDER_ID' => $_GET['ORDER_ID']]); // Письмо о снятии брони
            $this->arResult['FORM'] = 8;
        }


        $this->arResult['ORDER'] = $ORDER;
        $this->arResult['ERROR'] = $error;


        $this->IncludeComponentTemplate();
    }


}
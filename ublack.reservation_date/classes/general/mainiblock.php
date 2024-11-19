<?php

namespace UBLACK\Reservation;

use Bitrix\Iblock;
use UBLACK\Reservation\Iblock\CreatinoIblock;
class mainiblock
{

    /**
     * @param array $arTypes
     * @return bool
     */
    public static function load($arTypes){

        $check = true;
        $ci = new CreatinoIblock();


        foreach ($arTypes as $arType){
            if ($ci->AddIblockType($arType['ID'], $arType['NAME'])  && $arType['IBLOCK']){
                foreach ($arType['IBLOCK'] as $iblock){
                    if ($id = $ci->AddIblock($iblock['MAIN']['CODE'],$iblock['MAIN']['IBLOCK_TYPE_ID'],$iblock['MAIN']['NAME'] ) && $iblock['PROPERTY']){
                        foreach ($iblock['PROPERTY'] as $prop){
                            if ($ci->AddProp($id,$prop['NAME'],$prop['CODE'],"N",$prop['PROPERTY_TYPE'],$prop['USER_TYPE'])){
                                $check = true;
                            }else{
                                return false;
                            }
                        }
                    }else{
                        return false;
                    }
                }
            }else{
                return false;
            }
        }

        return $check;
    }


    public static function unload($arTypes){

        $ci = new CreatinoIblock();
        foreach ($arTypes as $arType){
            $ci->DelIblocks($arType['ID']);
        }
        return true;
    }


}
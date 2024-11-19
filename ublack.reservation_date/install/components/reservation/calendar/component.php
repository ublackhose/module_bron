<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @global CMain $APPLICATION */




$arDefaultUrlTemplates = array(
    "calendar_dates" => "calendar_dates/",
    "reserved" => "index.php",
    "admin_order"=>"editorder/",
    "orderform"=>"orderform/",
    "editorder_list"=>"editorder_list/"
);


$arParams["SEF_URL_TEMPLATES"] = array(
    "calendar_dates" => "calendar_dates/",
    "reserved" => "index.php",
    "admin_order"=>"editorder/",
    "orderform"=>"orderform/",
    "editorder_list"=>"editorder_list/"
);


$arUrlTemplates =
    CComponentEngine::MakeComponentUrlTemplates($arDefaultUrlTemplates,
        $arParams["SEF_URL_TEMPLATES"]);


$engine = new CComponentEngine($this);
$arVariables = array();
$componentPage = $engine->guessComponentPath(
    "/_test/",
    $arUrlTemplates,
    $arVariables
);





$this->IncludeComponentTemplate($componentPage);
?>
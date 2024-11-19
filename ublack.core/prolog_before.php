<?
use Bitrix\Main\Config\Option;

$HIDE_DONATION_ALERT = Option::get('ublack.core', "HIDE_DONATION_ALERT");
if($HIDE_DONATION_ALERT != 'Y'){
?>
<div class="ui-alert ui-alert-primary ui-alert-icon-info">
    <span class="ui-alert-message">
		Модуль сделан для бесплатного ознакомительного просмотра
	</span>
</div>
<? } ?>
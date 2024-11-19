<?


use Bitrix\Main;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;

Loc::loadMessages(__FILE__);
IncludeModuleLangFile(__FILE__);
if (!class_exists(ublack_reservation_date::class)) {
    class ublack_reservation_date extends CModule
    {
        const PHP_MIN_VERSION = '7.2.0';
        const MODULE_ID = 'ublack.reservation_date';
        var $MODULE_ID = "ublack.reservation_date";
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
            $path = str_replace("\\", "/", __FILE__);
            $path = substr($path, 0, strlen($path) - strlen("/index.php"));
            include($path . "/version.php");
            if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)) {
                $this->MODULE_VERSION = $arModuleVersion["VERSION"];
                $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
            }
            $this->MODULE_NAME = "Модуль бронирования";
            $this->MODULE_DESCRIPTION = "После установки вы сможете пользоваться компонентом reservation:calendar";
        }


        function InstallEvents()
        {
            return true;
        }

        function UnInstallEvents()
        {
            return true;
        }


        private function RenameFileContent($filePath, $values)
        {
            $file_contents = file_get_contents($filePath);
            foreach ($values as $code => $value) {
                $file_contents = str_replace($code, $value, $file_contents);
            }
            file_put_contents($filePath, $file_contents);

            return true;
        }


        private function CheckInitFile($siteId = false, $root = false)
        {
            $php_interface_dir = (file_exists(
                $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/'
            ) ? "/local/php_interface/" : "/bitrix/php_interface/");
            if ($root) {
                $filePath = $_SERVER['DOCUMENT_ROOT'] . $php_interface_dir . 'init.php';
            } else {
                $filePath = $_SERVER['DOCUMENT_ROOT'] . $php_interface_dir . $siteId . '/init.php';
            }

            if (!is_file($filePath)) {
                CopyDirFiles(
                    $_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/" . self::MODULE_ID . "/install/init.php",
                    $filePath,
                    false
                );
                self::RenameFileContent($filePath, array("#SITE_ID#" => ($root ? "" : $siteId)));
                self::RenameFileContent(
                    $filePath,
                    array("#ADDITIONAL_HEADERS#" => ($root ? ', $additional_headers' : ""))
                );
            }
        }


        function InstallDB($arParams = array())
        {
            try {
                if (version_compare(PHP_VERSION, self::PHP_MIN_VERSION) < 0) {
                    throw new Exception(
                        GetMessage(
                            "UBLACK_INSTALL_ERROR_REQUIRED_PHP_VERSION",
                            array("#GOT#" => PHP_VERSION, "#NEED#" => self::PHP_MIN_VERSION)
                        )
                    );
                }

                Main\Loader::clearModuleCache($this->MODULE_ID);
                $includeResult = Main\Loader::includeSharewareModule($this->MODULE_ID);
                switch ($includeResult) {
                    case Main\Loader::MODULE_DEMO_EXPIRED:
                        throw new \RuntimeException(Loc::getMessage("CITRUS_INSTALL_ERROR_MODULE_DEMO_EXPIRED"));
                    case Main\Loader::MODULE_NOT_FOUND:
                        $APPLICATION->ResetException();
                        /** @var \CApplicationException[]|string $errors */
                        $errors = is_array($APPLICATION->ERROR_STACK) ? array_unique($APPLICATION->ERROR_STACK) : [];
                        if ($errors == []) {
                            throw new Main\SystemException(
                                'Loader::includeSharewareModule returned Loader::MODULE_NOT_FOUND'
                            );
                        }
                        throw new RuntimeException(implode("<br>", $errors));
                }
                $arTypes = [
                    [
                        "ID" => "ublack_catalog",
                        "SECTIONS" => "Y",
                        "SORT" => 100,
                        "LANG" => [],
                        "NAME" => "Каталог",
                        "IBLOCK" => [
                            0=>[
                                "MAIN"=>[
                                    "ACTIVE" => "Y",
                                    "CODE" => "ublack_price",
                                    "SITE_ID" => SITE_ID,
                                    "IBLOCK_TYPE_ID" => "ublack_catalog",
                                    "NAME" => "Прайс",
                                    "SORT" => "100",
                                    "GROUP_ID" => array("2" => "R"), // Права доступа
                                    "FIELDS" => array(
                                        "CODE" => array(
                                            "IS_REQUIRED" => "Y", // Обязательное
                                            "DEFAULT_VALUE" => array(
                                                "UNIQUE" => "Y", // Проверять на уникальность
                                                "TRANSLITERATION" => "Y", // Транслитерировать
                                                "TRANS_LEN" => "30", // Максмальная длина транслитерации
                                                "TRANS_CASE" => "L", // Приводить к нижнему регистру
                                                "TRANS_SPACE" => "-", // Символы для замены
                                                "TRANS_OTHER" => "-",
                                                "TRANS_EAT" => "Y",
                                                "USE_GOOGLE" => "N",
                                            ),
                                        ),
                                        "SECTION_CODE" => array(
                                            "IS_REQUIRED" => "Y",
                                            "DEFAULT_VALUE" => array(
                                                "UNIQUE" => "Y",
                                                "TRANSLITERATION" => "Y",
                                                "TRANS_LEN" => "30",
                                                "TRANS_CASE" => "L",
                                                "TRANS_SPACE" => "-",
                                                "TRANS_OTHER" => "-",
                                                "TRANS_EAT" => "Y",
                                                "USE_GOOGLE" => "N",
                                            ),
                                        ),
                                        "DETAIL_TEXT_TYPE" => array(      // Тип детального описания
                                            "DEFAULT_VALUE" => "html",
                                        ),
                                        "SECTION_DESCRIPTION_TYPE" => array(
                                            "DEFAULT_VALUE" => "html",
                                        ),
                                        "IBLOCK_SECTION" => array(         // Привязка к разделам обязательноа
                                            "IS_REQUIRED" => "N",
                                        ),
                                        "LOG_SECTION_ADD" => array("IS_REQUIRED" => "Y"), // Журналирование
                                        "LOG_SECTION_EDIT" => array("IS_REQUIRED" => "Y"),
                                        "LOG_SECTION_DELETE" => array("IS_REQUIRED" => "Y"),
                                        "LOG_ELEMENT_ADD" => array("IS_REQUIRED" => "Y"),
                                        "LOG_ELEMENT_EDIT" => array("IS_REQUIRED" => "Y"),
                                        "LOG_ELEMENT_DELETE" => array("IS_REQUIRED" => "Y"),
                                    ),

                                    "LIST_PAGE_URL" => "#SITE_DIR#/ublack_price/",
                                    "SECTION_PAGE_URL" => "#SITE_DIR#/ublack_price/",
                                    "DETAIL_PAGE_URL" => "#SITE_DIR#/ublack_price/",

                                    "INDEX_SECTION" => "Y", // Индексировать разделы для модуля поиска
                                    "INDEX_ELEMENT" => "Y", // Индексировать элементы для модуля поиска

                                    "VERSION" => 1, // Хранение элементов в общей таблице

                                    "ELEMENT_NAME" => "Цена",
                                    "ELEMENTS_NAME" => "Цены",
                                    "ELEMENT_ADD" => "Добавить цену",
                                    "ELEMENT_EDIT" => "Изменить цены",
                                    "ELEMENT_DELETE" => "Удалить цену",
                                    "SECTION_NAME" => "Категории",
                                    "SECTIONS_NAME" => "Категория",
                                    "SECTION_ADD" => "Добавить категорию",
                                    "SECTION_EDIT" => "Изменить категорию",
                                    "SECTION_DELETE" => "Удалить категорию",
                                ],
                                "PROPERTY"=>[
                                    0=>[
                                        "NAME" =>"Август",
                                        "ACTIVE" =>"Y",
                                        "SORT" =>"2",
                                        "CODE" =>"AVG",
                                        "PROPERTY_TYPE" =>"S",
                                        "USER_TYPE" =>"",
                                        "ROW_COUNT" =>1,
                                        "COL_COUNT" =>30,
                                    ],1=>[
                                        "NAME" =>"Сентябрь",
                                        "ACTIVE" =>"Y",
                                        "SORT" =>"3",
                                        "CODE" =>"SENT",
                                        "PROPERTY_TYPE" =>"S",
                                        "USER_TYPE" =>"",
                                        "ROW_COUNT" =>1,
                                        "COL_COUNT" =>30,
                                    ],2=>[
                                        "NAME" =>"Октябрь",
                                        "ACTIVE" =>"Y",
                                        "SORT" =>"4",
                                        "CODE" =>"OCT",
                                        "PROPERTY_TYPE" =>"S",
                                        "USER_TYPE" =>"",
                                        "ROW_COUNT" =>1,
                                        "COL_COUNT" =>30,
                                    ],3=>[
                                        "NAME" =>"Ноябрь",
                                        "ACTIVE" =>"Y",
                                        "SORT" =>"5",
                                        "CODE" =>"NOY",
                                        "PROPERTY_TYPE" =>"S",
                                        "USER_TYPE" =>"",
                                        "ROW_COUNT" =>1,
                                        "COL_COUNT" =>30,
                                    ],4=>[
                                        "NAME" =>"Декабрь",
                                        "ACTIVE" =>"Y",
                                        "SORT" =>"6",
                                        "CODE" =>"DEC",
                                        "PROPERTY_TYPE" =>"S",
                                        "USER_TYPE" =>"",
                                        "ROW_COUNT" =>1,
                                        "COL_COUNT" =>30,
                                    ],5=>[
                                        "NAME" =>"Январь",
                                        "ACTIVE" =>"Y",
                                        "SORT" =>"7",
                                        "CODE" =>"YAN",
                                        "PROPERTY_TYPE" =>"S",
                                        "USER_TYPE" =>"",
                                        "ROW_COUNT" =>1,
                                        "COL_COUNT" =>30,
                                    ],6=>[
                                        "NAME" =>"Февраль",
                                        "ACTIVE" =>"Y",
                                        "SORT" =>"8",
                                        "CODE" =>"FEV",
                                        "PROPERTY_TYPE" =>"S",
                                        "USER_TYPE" =>"",
                                        "ROW_COUNT" =>1,
                                        "COL_COUNT" =>30,
                                    ],7=>[
                                        "NAME" =>"Март",
                                        "ACTIVE" =>"Y",
                                        "SORT" =>"9",
                                        "CODE" =>"MART",
                                        "PROPERTY_TYPE" =>"S",
                                        "USER_TYPE" =>"",
                                        "ROW_COUNT" =>1,
                                        "COL_COUNT" =>30,
                                    ],8=>[
                                        "NAME" =>"Апрель",
                                        "ACTIVE" =>"Y",
                                        "SORT" =>"10",
                                        "CODE" =>"APR",
                                        "PROPERTY_TYPE" =>"S",
                                        "USER_TYPE" =>"",
                                        "ROW_COUNT" =>1,
                                        "COL_COUNT" =>30,
                                    ],9=>[
                                        "NAME" =>"Май",
                                        "ACTIVE" =>"Y",
                                        "SORT" =>"11",
                                        "CODE" =>"MAY",
                                        "PROPERTY_TYPE" =>"S",
                                        "USER_TYPE" =>"",
                                        "ROW_COUNT" =>1,
                                        "COL_COUNT" =>30,
                                    ],10=>[
                                        "NAME" =>"Июнь",
                                        "ACTIVE" =>"Y",
                                        "SORT" =>"12",
                                        "CODE" =>"IUN",
                                        "PROPERTY_TYPE" =>"S",
                                        "USER_TYPE" =>"",
                                        "ROW_COUNT" =>1,
                                        "COL_COUNT" =>30,
                                    ],11=>[
                                        "NAME" =>"Июль",
                                        "ACTIVE" =>"Y",
                                        "SORT" =>"13",
                                        "CODE" =>"IUL",
                                        "PROPERTY_TYPE" =>"S",
                                        "USER_TYPE" =>"",
                                        "ROW_COUNT" =>1,
                                        "COL_COUNT" =>30,
                                    ]
                                    ,12=>[
                                        "NAME" =>"Основная цена",
                                        "ACTIVE" =>"Y",
                                        "IS_REQUIRED"=>"Y",
                                        "SORT" =>"1",
                                        "CODE" =>"PRICE",
                                        "PROPERTY_TYPE" =>"S",
                                        "USER_TYPE" =>"",
                                        "ROW_COUNT" =>1,
                                        "COL_COUNT" =>30,
                                    ]
                                ]
                            ],
                            1=>[
                                "MAIN"=>[
                                    "ACTIVE" => "Y",
                                    "CODE" => "ublack_catalog",
                                    "SITE_ID" => SITE_ID,
                                    "IBLOCK_TYPE_ID" => "ublack_catalog",
                                    "NAME" => "Каталог",
                                    "SORT" => "100",
                                    "GROUP_ID" => array("2" => "R"), // Права доступа
                                    "FIELDS" => array(
                                        "CODE" => array(
                                            "IS_REQUIRED" => "Y", // Обязательное
                                            "DEFAULT_VALUE" => array(
                                                "UNIQUE" => "Y", // Проверять на уникальность
                                                "TRANSLITERATION" => "Y", // Транслитерировать
                                                "TRANS_LEN" => "30", // Максмальная длина транслитерации
                                                "TRANS_CASE" => "L", // Приводить к нижнему регистру
                                                "TRANS_SPACE" => "-", // Символы для замены
                                                "TRANS_OTHER" => "-",
                                                "TRANS_EAT" => "Y",
                                                "USE_GOOGLE" => "N",
                                            ),
                                        ),
                                        "SECTION_CODE" => array(
                                            "IS_REQUIRED" => "Y",
                                            "DEFAULT_VALUE" => array(
                                                "UNIQUE" => "Y",
                                                "TRANSLITERATION" => "Y",
                                                "TRANS_LEN" => "30",
                                                "TRANS_CASE" => "L",
                                                "TRANS_SPACE" => "-",
                                                "TRANS_OTHER" => "-",
                                                "TRANS_EAT" => "Y",
                                                "USE_GOOGLE" => "N",
                                            ),
                                        ),
                                        "DETAIL_TEXT_TYPE" => array(      // Тип детального описания
                                            "DEFAULT_VALUE" => "html",
                                        ),
                                        "SECTION_DESCRIPTION_TYPE" => array(
                                            "DEFAULT_VALUE" => "html",
                                        ),
                                        "IBLOCK_SECTION" => array(         // Привязка к разделам обязательноа
                                            "IS_REQUIRED" => "N",
                                        ),
                                        "LOG_SECTION_ADD" => array("IS_REQUIRED" => "Y"), // Журналирование
                                        "LOG_SECTION_EDIT" => array("IS_REQUIRED" => "Y"),
                                        "LOG_SECTION_DELETE" => array("IS_REQUIRED" => "Y"),
                                        "LOG_ELEMENT_ADD" => array("IS_REQUIRED" => "Y"),
                                        "LOG_ELEMENT_EDIT" => array("IS_REQUIRED" => "Y"),
                                        "LOG_ELEMENT_DELETE" => array("IS_REQUIRED" => "Y"),
                                    ),

                                    "LIST_PAGE_URL" => "#SITE_DIR#/ublack_catalog/",
                                    "SECTION_PAGE_URL" => "#SITE_DIR#/ublack_catalog/",
                                    "DETAIL_PAGE_URL" => "#SITE_DIR#/ublack_catalog/",

                                    "INDEX_SECTION" => "Y", // Индексировать разделы для модуля поиска
                                    "INDEX_ELEMENT" => "Y", // Индексировать элементы для модуля поиска

                                    "VERSION" => 1, // Хранение элементов в общей таблице

                                    "ELEMENT_NAME" => "Номер",
                                    "ELEMENTS_NAME" => "Номера",
                                    "ELEMENT_ADD" => "Добавить Номер",
                                    "ELEMENT_EDIT" => "Изменить Номер",
                                    "ELEMENT_DELETE" => "Удалить Номер",
                                    "SECTION_NAME" => "Категории",
                                    "SECTIONS_NAME" => "Категория",
                                    "SECTION_ADD" => "Добавить категорию",
                                    "SECTION_EDIT" => "Изменить категорию",
                                    "SECTION_DELETE" => "Удалить категорию",
                                ],
                                "PROPERTY"=>[
                                    0=>[
                                        "NAME" =>"Галерея 1",
                                        "ACTIVE" =>"Y",
                                        "SORT" =>"500",
                                        "CODE" =>"GALLERY1",
                                        "PROPERTY_TYPE" =>"F",
                                        "USER_TYPE" =>"",
                                        "ROW_COUNT" =>1,
                                        "COL_COUNT" =>30,
                                    ],1=>[
                                        "NAME" =>"Галерея 2",
                                        "ACTIVE" =>"Y",
                                        "SORT" =>"500",
                                        "CODE" =>"GALLERY2",
                                        "PROPERTY_TYPE" =>"F",
                                        "USER_TYPE" =>"",
                                        "ROW_COUNT" =>1,
                                        "COL_COUNT" =>30,
                                    ],2=>[
                                        "NAME" =>"Количество гостей",
                                        "ACTIVE" =>"Y",
                                        "SORT" =>"500",
                                        "CODE" =>"MATURE",
                                        "PROPERTY_TYPE" =>"N",
                                        "USER_TYPE" =>"",
                                        "ROW_COUNT" =>1,
                                        "COL_COUNT" =>30,
                                    ],3=>[
                                        "NAME" =>"Количество доп. мест",
                                        "ACTIVE" =>"Y",
                                        "SORT" =>"500",
                                        "CODE" =>"DOP_MESTO_COUNT",
                                        "PROPERTY_TYPE" =>"N",
                                        "USER_TYPE" =>"",
                                        "ROW_COUNT" =>1,
                                        "COL_COUNT" =>30,
                                    ],4=>[
                                        "NAME" =>"Максимальное количество гостей",
                                        "ACTIVE" =>"Y",
                                        "SORT" =>"500",
                                        "CODE" =>"MATURE_MAX",
                                        "PROPERTY_TYPE" =>"N",
                                        "USER_TYPE" =>"",
                                        "ROW_COUNT" =>1,
                                        "COL_COUNT" =>30,
                                    ],5=>[
                                        "NAME" =>"Мета ключевые слова",
                                        "ACTIVE" =>"Y",
                                        "SORT" =>"500",
                                        "CODE" =>"METAKEYWORDS",
                                        "PROPERTY_TYPE" =>"S",
                                        "USER_TYPE" =>"",
                                        "ROW_COUNT" =>1,
                                        "COL_COUNT" =>30,
                                    ],6=>[
                                        "NAME" =>"Мета описание",
                                        "ACTIVE" =>"Y",
                                        "SORT" =>"500",
                                        "CODE" =>"METADESCRIPTION",
                                        "PROPERTY_TYPE" =>"S",
                                        "USER_TYPE" =>"",
                                        "ROW_COUNT" =>1,
                                        "COL_COUNT" =>30,
                                    ],7=>[
                                        "NAME" =>"Мета тайтл",
                                        "ACTIVE" =>"Y",
                                        "SORT" =>"500",
                                        "CODE" =>"METATITLE",
                                        "PROPERTY_TYPE" =>"S",
                                        "USER_TYPE" =>"",
                                        "ROW_COUNT" =>1,
                                        "COL_COUNT" =>30,
                                    ],8=>[
                                        "NAME" =>"Метка",
                                        "ACTIVE" =>"Y",
                                        "SORT" =>"500",
                                        "CODE" =>"LABEL",
                                        "PROPERTY_TYPE" =>"S",
                                        "USER_TYPE" =>"",
                                        "ROW_COUNT" =>1,
                                        "COL_COUNT" =>30,
                                    ],9=>[
                                        "NAME" =>"Минимальное количество дней ",
                                        "ACTIVE" =>"Y",
                                        "SORT" =>"500",
                                        "CODE" =>"MIN_DAY",
                                        "PROPERTY_TYPE" =>"N",
                                        "USER_TYPE" =>"",
                                        "ROW_COUNT" =>1,
                                        "COL_COUNT" =>30,
                                    ],10=>[
                                        "NAME" =>"Показывать на главной",
                                        "ACTIVE" =>"Y",
                                        "SORT" =>"500",
                                        "CODE" =>"MAIN",
                                        "PROPERTY_TYPE" =>"L",
                                        "USER_TYPE" =>"",
                                        "ROW_COUNT" =>1,
                                        "COL_COUNT" =>30,
                                    ],11=>[
                                        "NAME" =>"Похожие",
                                        "ACTIVE" =>"Y",
                                        "SORT" =>"500",
                                        "CODE" =>"LINK1",
                                        "PROPERTY_TYPE" =>"E",
                                        "USER_TYPE" =>"",
                                        "ROW_COUNT" =>1,
                                        "COL_COUNT" =>30,
                                    ],12=>[
                                        "NAME" =>"Привязка к прайс листу",
                                        "ACTIVE" =>"Y",
                                        "SORT" =>"500",
                                        "CODE" =>"PRICES",
                                        "PROPERTY_TYPE" =>"E",
                                        "USER_TYPE" =>"",
                                        "ROW_COUNT" =>1,
                                        "COL_COUNT" =>30,
                                    ],13=>[
                                        "NAME" =>"Стоимость",
                                        "ACTIVE" =>"Y",
                                        "SORT" =>"500",
                                        "CODE" =>"PRICE",
                                        "PROPERTY_TYPE" =>"N",
                                        "USER_TYPE" =>"",
                                        "ROW_COUNT" =>1,
                                        "COL_COUNT" =>30,
                                    ],14=>[
                                        "NAME" =>"Стоимость (текст)",
                                        "ACTIVE" =>"Y",
                                        "SORT" =>"500",
                                        "CODE" =>"PRICE_TEXT",
                                        "PROPERTY_TYPE" =>"S",
                                        "USER_TYPE" =>"HTML",
                                        "ROW_COUNT" =>1,
                                        "COL_COUNT" =>30,
                                    ],15=>[
                                        "NAME" =>"Стоимость без скидки",
                                        "ACTIVE" =>"Y",
                                        "SORT" =>"500",
                                        "CODE" =>"PRICE_DISCOUNT",
                                        "PROPERTY_TYPE" =>"N",
                                        "USER_TYPE" =>"",
                                        "ROW_COUNT" =>1,
                                        "COL_COUNT" =>30,
                                    ],16=>[
                                        "NAME" =>"Цены",
                                        "ACTIVE" =>"Y",
                                        "SORT" =>"500",
                                        "CODE" =>"PRICE_TABLE",
                                        "PROPERTY_TYPE" =>"S",
                                        "USER_TYPE" =>"HTML",
                                        "ROW_COUNT" =>1,
                                        "COL_COUNT" =>30,
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        "ID" => "ublack_forms",
                        "SECTIONS" => "Y",
                        "SORT" => 150,
                        "LANG" => [],
                        "NAME" => "Формы",
                        "IBLOCK" => [
                            0 => [
                                "MAIN" => [
                                    "ACTIVE" => "Y",
                                    "CODE" => "ublack_booking",
                                    "SITE_ID" => SITE_ID,
                                    "IBLOCK_TYPE_ID" => "ublack_forms",
                                    "NAME" => "Бронирование",
                                    "SORT" => "100",
                                    "GROUP_ID" => array("2" => "R"), // Права доступа
                                    "FIELDS" => array(
                                        "CODE" => array(
                                            "IS_REQUIRED" => "Y", // Обязательное
                                            "DEFAULT_VALUE" => array(
                                                "UNIQUE" => "Y", // Проверять на уникальность
                                                "TRANSLITERATION" => "Y", // Транслитерировать
                                                "TRANS_LEN" => "30", // Максмальная длина транслитерации
                                                "TRANS_CASE" => "L", // Приводить к нижнему регистру
                                                "TRANS_SPACE" => "-", // Символы для замены
                                                "TRANS_OTHER" => "-",
                                                "TRANS_EAT" => "Y",
                                                "USE_GOOGLE" => "N",
                                            ),
                                        ),
                                        "SECTION_CODE" => array(
                                            "IS_REQUIRED" => "Y",
                                            "DEFAULT_VALUE" => array(
                                                "UNIQUE" => "Y",
                                                "TRANSLITERATION" => "Y",
                                                "TRANS_LEN" => "30",
                                                "TRANS_CASE" => "L",
                                                "TRANS_SPACE" => "-",
                                                "TRANS_OTHER" => "-",
                                                "TRANS_EAT" => "Y",
                                                "USE_GOOGLE" => "N",
                                            ),
                                        ),
                                        "DETAIL_TEXT_TYPE" => array(      // Тип детального описания
                                            "DEFAULT_VALUE" => "html",
                                        ),
                                        "SECTION_DESCRIPTION_TYPE" => array(
                                            "DEFAULT_VALUE" => "html",
                                        ),
                                        "IBLOCK_SECTION" => array(         // Привязка к разделам обязательноа
                                            "IS_REQUIRED" => "Y",
                                        ),
                                        "LOG_SECTION_ADD" => array("IS_REQUIRED" => "Y"), // Журналирование
                                        "LOG_SECTION_EDIT" => array("IS_REQUIRED" => "Y"),
                                        "LOG_SECTION_DELETE" => array("IS_REQUIRED" => "Y"),
                                        "LOG_ELEMENT_ADD" => array("IS_REQUIRED" => "Y"),
                                        "LOG_ELEMENT_EDIT" => array("IS_REQUIRED" => "Y"),
                                        "LOG_ELEMENT_DELETE" => array("IS_REQUIRED" => "Y"),
                                    ),

                                    "LIST_PAGE_URL" => "#SITE_DIR#/ublack_booking/",
                                    "SECTION_PAGE_URL" => "#SITE_DIR#/ublack_booking/",
                                    "DETAIL_PAGE_URL" => "#SITE_DIR#/ublack_booking/",

                                    "INDEX_SECTION" => "Y", // Индексировать разделы для модуля поиска
                                    "INDEX_ELEMENT" => "Y", // Индексировать элементы для модуля поиска

                                    "VERSION" => 1, // Хранение элементов в общей таблице

                                    "ELEMENT_NAME" => "Бронь",
                                    "ELEMENTS_NAME" => "Бронь",
                                    "ELEMENT_ADD" => "Добавить бронь",
                                    "ELEMENT_EDIT" => "Изменить бронь",
                                    "ELEMENT_DELETE" => "Удалить бронь",
                                    "SECTION_NAME" => "Категории",
                                    "SECTIONS_NAME" => "Категория",
                                    "SECTION_ADD" => "Добавить категорию",
                                    "SECTION_EDIT" => "Изменить категорию",
                                    "SECTION_DELETE" => "Удалить категорию",
                                ],
                                "PROPERTY"=>[
                                    0=>[
                                        "NAME" =>"Имя",
                                        "ACTIVE" =>"Y",
                                        "SORT" =>"10",
                                        "CODE" =>"NAME",
                                        "PROPERTY_TYPE" =>"S",
                                        "USER_TYPE" =>"",
                                        "ROW_COUNT" =>1,
                                        "COL_COUNT" =>30,
                                    ],1=>[
                                        "NAME" =>"Телефон",
                                        "ACTIVE" =>"Y",
                                        "SORT" =>"20",
                                        "CODE" =>"PHONE",
                                        "PROPERTY_TYPE" =>"S",
                                        "USER_TYPE" =>"",
                                        "ROW_COUNT" =>1,
                                        "COL_COUNT" =>30,
                                    ],2=>[
                                        "NAME" =>"Email",
                                        "ACTIVE" =>"Y",
                                        "SORT" =>"30",
                                        "CODE" =>"EMAIL",
                                        "PROPERTY_TYPE" =>"S",
                                        "USER_TYPE" =>"",
                                        "ROW_COUNT" =>1,
                                        "COL_COUNT" =>30,
                                    ],3=>[
                                        "NAME" =>"Дата заезда",
                                        "ACTIVE" =>"Y",
                                        "SORT" =>"40",
                                        "CODE" =>"DATE_IN",
                                        "PROPERTY_TYPE" =>"S",
                                        "USER_TYPE" =>"Date",
                                        "ROW_COUNT" =>1,
                                        "COL_COUNT" =>30,
                                    ],4=>[
                                        "NAME" =>"Дата выезда",
                                        "ACTIVE" =>"Y",
                                        "SORT" =>"50",
                                        "CODE" =>"DATE_OUT",
                                        "PROPERTY_TYPE" =>"S",
                                        "USER_TYPE" =>"Date",
                                        "ROW_COUNT" =>1,
                                        "COL_COUNT" =>30,
                                    ],5=>[
                                        "NAME" =>"Всего дней",
                                        "ACTIVE" =>"Y",
                                        "SORT" =>"60",
                                        "CODE" =>"ALL_DAY",
                                        "PROPERTY_TYPE" =>"S",
                                        "USER_TYPE" =>"",
                                        "ROW_COUNT" =>1,
                                        "COL_COUNT" =>30,
                                    ],6=>[
                                        "NAME" =>"Сумма",
                                        "ACTIVE" =>"Y",
                                        "SORT" =>"70",
                                        "CODE" =>"SUM",
                                        "PROPERTY_TYPE" =>"S",
                                        "USER_TYPE" =>"",
                                        "ROW_COUNT" =>1,
                                        "COL_COUNT" =>30,
                                    ],7=>[
                                        "NAME" =>"Аванс",
                                        "ACTIVE" =>"Y",
                                        "SORT" =>"75",
                                        "CODE" =>"AVANS",
                                        "PROPERTY_TYPE" =>"S",
                                        "USER_TYPE" =>"",
                                        "ROW_COUNT" =>1,
                                        "COL_COUNT" =>30,
                                    ],8=>[
                                        "NAME" =>"Дополнительное место",
                                        "ACTIVE" =>"Y",
                                        "SORT" =>"80",
                                        "CODE" =>"DOP_MESTO",
                                        "PROPERTY_TYPE" =>"N",
                                        "USER_TYPE" =>"",
                                        "ROW_COUNT" =>1,
                                        "COL_COUNT" =>30,
                                    ],9=>[
                                        "NAME" =>"Количество взрослых",
                                        "ACTIVE" =>"Y",
                                        "SORT" =>"90",
                                        "CODE" =>"MATURE",
                                        "PROPERTY_TYPE" =>"S",
                                        "USER_TYPE" =>"",
                                        "ROW_COUNT" =>1,
                                        "COL_COUNT" =>30,
                                    ],10=>[
                                        "NAME" =>"Трансфер",
                                        "ACTIVE" =>"Y",
                                        "SORT" =>"110",
                                        "CODE" =>"TRANSFER",
                                        "PROPERTY_TYPE" =>"L",
                                        "USER_TYPE" =>"",
                                        "ROW_COUNT" =>1,
                                        "COL_COUNT" =>30,
                                    ],11=>[
                                        "NAME" =>"Номер",
                                        "ACTIVE" =>"Y",
                                        "SORT" =>"120",
                                        "CODE" =>"NOMER",
                                        "PROPERTY_TYPE" =>"E",
                                        "USER_TYPE" =>"",
                                        "ROW_COUNT" =>1,
                                        "COL_COUNT" =>30,
                                    ],12=>[
                                        "NAME" =>"Комментарий",
                                        "ACTIVE" =>"Y",
                                        "SORT" =>"130",
                                        "CODE" =>"COMMENT",
                                        "PROPERTY_TYPE" =>"S",
                                        "USER_TYPE" =>"HTML",
                                        "ROW_COUNT" =>1,
                                        "COL_COUNT" =>30,
                                    ],13=>[
                                        "NAME" =>"Оплата",
                                        "ACTIVE" =>"Y",
                                        "SORT" =>"140",
                                        "CODE" =>"PAYED",
                                        "PROPERTY_TYPE" =>"L",
                                        "USER_TYPE" =>"",
                                        "ROW_COUNT" =>1,
                                        "COL_COUNT" =>30,
                                    ],14=>[
                                        "NAME" =>"Номер заказа в сбербанке",
                                        "ACTIVE" =>"Y",
                                        "SORT" =>"150",
                                        "CODE" =>"OREDER_ID_SBER",
                                        "PROPERTY_TYPE" =>"S",
                                        "USER_TYPE" =>"",
                                        "ROW_COUNT" =>1,
                                        "COL_COUNT" =>30,
                                    ],15=>[
                                        "NAME" =>"Дополнительное место цена",
                                        "ACTIVE" =>"Y",
                                        "SORT" =>"500",
                                        "CODE" =>"DOP_MESTO_PRICE",
                                        "PROPERTY_TYPE" =>"S",
                                        "USER_TYPE" =>"",
                                        "ROW_COUNT" =>1,
                                        "COL_COUNT" =>30,
                                    ],16=>[
                                        "NAME" =>"Номер цена",
                                        "ACTIVE" =>"Y",
                                        "SORT" =>"500",
                                        "CODE" =>"NOMER_PRICE",
                                        "PROPERTY_TYPE" =>"S",
                                        "USER_TYPE" =>"",
                                        "ROW_COUNT" =>1,
                                        "COL_COUNT" =>30,
                                    ],17=>[
                                        "NAME" =>"Произвольная ссылка на оплату",
                                        "ACTIVE" =>"Y",
                                        "SORT" =>"500",
                                        "CODE" =>"OTHER_PAY_URL",
                                        "PROPERTY_TYPE" =>"S",
                                        "USER_TYPE" =>"",
                                        "ROW_COUNT" =>1,
                                        "COL_COUNT" =>30,
                                    ],18=>[
                                        "NAME" =>"Тип оплаты",
                                        "ACTIVE" =>"Y",
                                        "SORT" =>"500",
                                        "CODE" =>"PAY_TYP",
                                        "PROPERTY_TYPE" =>"L",
                                        "USER_TYPE" =>"",
                                        "ROW_COUNT" =>1,
                                        "COL_COUNT" =>30,
                                    ],19=>[
                                        "NAME" =>"Трансфер цена",
                                        "ACTIVE" =>"Y",
                                        "SORT" =>"500",
                                        "CODE" =>"TRANSFER_PRICE",
                                        "PROPERTY_TYPE" =>"S",
                                        "USER_TYPE" =>"",
                                        "ROW_COUNT" =>1,
                                        "COL_COUNT" =>30,
                                    ],20=>[
                                        "NAME" =>"Условия бронирования",
                                        "ACTIVE" =>"Y",
                                        "SORT" =>"500",
                                        "CODE" =>"BOOKING_CONDITIONS",
                                        "PROPERTY_TYPE" =>"L",
                                        "USER_TYPE" =>"",
                                        "ROW_COUNT" =>1,
                                        "COL_COUNT" =>30,
                                    ]
                                ]
                            ]
                        ],
                    ],
                ];

                if (!class_exists(mainiblock::class)) {
                    Main\Loader::registerNamespace(
                        'UBLACK\\Reservation',
                        Main\Loader::getDocumentRoot() .
                        '/bitrix/modules/' . $this->MODULE_ID . '/lib'
                    );
                }

                if (!class_exists(CreatinoIblock::class)) {
                    Main\Loader::registerNamespace(
                        'UBLACK\\Reservation\\Iblock',
                        Main\Loader::getDocumentRoot() .
                        '/bitrix/modules/' . $this->MODULE_ID . '/lib/iblock'
                    );
                }






                \UBLACK\Reservation\Admin\Iblock\DateTab::register($this->MODULE_ID);





                if (UBLACK\Reservation\mainiblock::load($arTypes)) {
                    global $DB;
                    $ch = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/db/".strtolower($DB->type)."/install.sql");
                    if ($ch !== false){
                        return false;
                    }else{
                        return true;
                    }
                } else {
                    return false;
                }
            } catch (Exception $e) {
                $APPLICATION->ThrowException($e->getMessage());
                return false;
            }
        }

        function UnInstallDB($arParams = array())
        {
            $arTypes = [
                [
                    "ID" => "ublack_catalog",
                    "SECTIONS" => "Y",
                    "SORT" => 100,
                    "LANG" => [],
                    "NAME" => "Каталог"
                ],
                [
                    "ID" => "ublack_forms",
                    "SECTIONS" => "Y",
                    "SORT" => 150,
                    "LANG" => [],
                    "NAME" => "Формы"
                ],
            ];
            if (!class_exists(mainiblock::class)) {
                Main\Loader::registerNamespace(
                    'UBLACK\\Reservation',
                    Main\Loader::getDocumentRoot() .
                    '/bitrix/modules/' . $this->MODULE_ID . '/lib'
                );
            }
            if (!class_exists(CreatinoIblock::class)) {
                Main\Loader::registerNamespace(
                    'UBLACK\\Reservation\\Iblock',
                    Main\Loader::getDocumentRoot() .
                    '/bitrix/modules/' . $this->MODULE_ID . '/lib/iblock'
                );
            }


            Main\Config\Option::delete($this->MODULE_ID);


            $sqlHelper = $this->getConnection()->getSqlHelper();
            $strSql = "DELETE FROM b_module_to_module WHERE TO_MODULE_ID='" . $sqlHelper->forSql(
                    $this->MODULE_ID
                ) . "'";
            $this->getConnection()->queryExecute($strSql);


            \UBLACK\Reservation\Admin\Iblock\DateTab::unregister($this->MODULE_ID);


            if (UBLACK\Reservation\mainiblock::unload($arTypes)) {
                global $DB;
                $ch = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/db/".strtolower($DB->type)."/uninstall.sql");
               if ($ch !== false){
                   return false;
               }else{
                   return true;
               }
            }
            return false;
        }

        function InstallInit()
        {
            $rsSites = CSite::GetList($siteby = "sort", $siteorder = "asc", array());
            $sites = array();
            while ($arSite = $rsSites->Fetch()) {
                $sites[] = $arSite["LID"];
            }
            if (count($sites) > 1) {
                foreach ($sites as $siteId) {
                    self::CheckInitFile($siteId);
                }
            } elseif (count($sites) == 1) {
                self::CheckInitFile($sites[0], true);
            }
        }


        function UnInstallInit()
        {
            if (is_dir($p = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . self::MODULE_ID . '/admin')) {
                if ($dir = opendir($p)) {
                    while (false !== $item = readdir($dir)) {
                        if ($item == '..' || $item == '.' || $item == 'menu.php') {
                            continue;
                        }
                        file_put_contents(
                            $file = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/' . self::MODULE_ID . '_' . $item,
                            '<' . '? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/' . self::MODULE_ID . '/admin/' . $item . '");?' . '>'
                        );
                    }
                    closedir($dir);
                }
            }
        }

        function InstallFiles()
        {
            if (is_dir($p = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . self::MODULE_ID . '/admin')) {
                if ($dir = opendir($p)) {
                    while (false !== $item = readdir($dir)) {
                        if ($item == '..' || $item == '.' || $item == 'menu.php') {
                            continue;
                        }
                        file_put_contents(
                            $file = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/' . self::MODULE_ID . '_' . $item,
                            '<' . '? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/' . self::MODULE_ID . '/admin/' . $item . '");?' . '>'
                        );
                    }
                    closedir($dir);
                }
            }

            CheckDirPath($_SERVER['DOCUMENT_ROOT'] . '/bitrix/tools/' . self::MODULE_ID . '/');
            if (is_dir($p = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . self::MODULE_ID . '/tools')) {
                if ($dir = opendir($p)) {
                    while (false !== $item = readdir($dir)) {
                        if ($item == '..' || $item == '.') {
                            continue;
                        }
                        file_put_contents(
                            $file = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/tools/' . self::MODULE_ID . '/' . $item,
                            '<' . '? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/' . self::MODULE_ID . '/tools/' . $item . '");?' . '>'
                        );
                    }
                    closedir($dir);
                }
            }

            CopyDirFiles(
                $_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/" . self::MODULE_ID . "/install/bitrix/",
                $_SERVER["DOCUMENT_ROOT"] . "/bitrix/",
                true,
                true
            );
            CopyDirFiles(
                $_SERVER["DOCUMENT_ROOT"] . "/local/modules/reservation_date/install/components",
                $_SERVER["DOCUMENT_ROOT"] . "/bitrix/components",
                true,
                true
            );
            return true;
        }

        function UnInstallFiles()
        {
            if (is_dir($p = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . self::MODULE_ID . '/admin')) {
                if ($dir = opendir($p)) {
                    while (false !== $item = readdir($dir)) {
                        if ($item == '..' || $item == '.') {
                            continue;
                        }
                        unlink($_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/' . self::MODULE_ID . '_' . $item);
                    }
                    closedir($dir);
                }
            }
            if (is_dir($p = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . self::MODULE_ID . '/tools')) {
                if ($dir = opendir($p)) {
                    while (false !== $item = readdir($dir)) {
                        if ($item == '..' || $item == '.') {
                            continue;
                        }
                        unlink($_SERVER['DOCUMENT_ROOT'] . '/bitrix/tools/' . self::MODULE_ID . '/' . $item);
                    }
                    closedir($dir);
                }
            }
            DeleteDirFiles(
                $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . self::MODULE_ID . "/install/bitrix/",
                $_SERVER["DOCUMENT_ROOT"] . "/bitrix/"
            );
            DeleteDirFilesEx('/bitrix/panel/' . self::MODULE_ID . '/');
            DeleteDirFilesEx('/bitrix/tools/' . self::MODULE_ID . '/');

            DeleteDirFilesEx("/local/components/reservation");
            return true;
        }

        function DoInstall()
        {
            global $DOCUMENT_ROOT, $APPLICATION;
            if (!CModule::IncludeModule("ublack.core")) {
                $APPLICATION->IncludeAdminFile(
                    GetMessage("UBLACK_CORE_ERROR"),
                    $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . self::MODULE_ID . "/install/ublack.core.php"
                );
            }


            Main\ModuleManager::registerModule($this->MODULE_ID);


            if (Loader::includeModule($this->MODULE_ID) &&
                $this->InstallDB()
                && $this->InstallEvents()
                && $this->InstallFiles()
            ) {
                $this->InstallInit();
                return true;
            } else {
                return false;
            }
        }

        function DoUninstall()
        {
            global $DOCUMENT_ROOT, $APPLICATION;


            global $APPLICATION;

            if (Loader::includeModule($this->MODULE_ID) &&
                $this->UnInstallDB()
                && $this->UnInstallEvents()
                && $this->UnInstallFiles()
            ) {
                COption::RemoveOption(self::MODULE_ID);
                CAdminNotify::DeleteByModule(self::MODULE_ID);
                CAgent::RemoveModuleAgents(self::MODULE_ID);
                UnRegisterModule(self::MODULE_ID);

                $APPLICATION->IncludeAdminFile(
                    "Деинсталляция модуля reservation_date",
                    $DOCUMENT_ROOT . "/local/modules/reservation_date/install/unstep.php"
                );


                return true;
            } else {
                return false;
            }
        }

        protected function getConnection()
        {
            return Main\Application::getInstance()->getConnection();
        }

        function GetModuleRightList()
        {
            $arr = array(
                "reference_id" => array("D", "R", "S", "T", "W"),
                "reference" => array(
                    "[D] " . GetMessage("UBLACK_SMTP_RIGHT_DENIED"),
                    "[R] " . GetMessage("UBLACK_SMTP_RIGHT_READ"),
                    "[S] " . GetMessage("UBLACK_SMTP_RIGHT_SEND"),
                    "[T] " . GetMessage("UBLACK_SMTP_RIGHT_TRACE"),
                    "[W] " . GetMessage("UBLACK_SMTP_RIGHT_ADMIN")
                )
            );
            return $arr;
        }


    }
}
?>
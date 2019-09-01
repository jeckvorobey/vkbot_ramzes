<?php
//ключи YANDEX API
const YANDEX_ID_CATALOG = 'b1gafa4iik0uceshd2u0';
const YANDEX_IAM_TOKEN = 'CggaATEVAgAAABKABGLh0J8l_H7prCBmw9Dy12CToZrHJHQFw_aj2aeGY3mqN3YBGOLinHb1wcfNDSEaYfrZqFRegQMUssi_NVgZ5nK1tTDIydV4ip1cqoQfeJVDhzve_Yvcb2DSk2N7SnMlUPYbpVkg3-aSMmI16aAFCaEqBJ2CyzRaIv4dLgSMfe6LNfFYoC5zlrUSLYZL1dlfoZxxGWCwzKDlmG9h0GZkUQHf0A7TFVQBv1pPfSoSJ2ncLXagF7xmqDRjUD0I3x2skIpQS6E39fXDBH49dwMBhc1FJMxhDdovvLKXI3OVHqArgg_phPPhcjma8db460gyvhzkdN-_SCN4qygUhHbeu3hPTR8G2ybKSOrKdrL5ONukAHNPd4Q1oDIDhoL1sc2rywyHO8jjCE4erPs-WFgql590IwdUf-qqXW00DPaifjDf9yw7LxltSfpy4lBNmtIF_1bULqXpqOu350n8fgPx_CiL5yC7bz8miiYpa5osEH2yzPVWTS2usG1AzWPfKIBPhQhItUnNp-gNw7h38PKRTJouPxDslx1QrwnCfouZdRVsHiKEyP6kDLkxwJpa8e5ILVOU1WXV9Dgxr4ytDz6G_tKlHSYbqEaU4pHiyHpMAkiMTznW7zxcfua_mCR5jd_kZAQROwgQBdnmKQ6FZmjdVGx6YpmZL5MT_sEyoapzPJt5GmUKIDMwOWE0OGVjNDcxMDQyNGM5ZWE2MzUxNTJkNTFiZDVlEMSMqesFGITeq-sFIiMKFGFqZWF0M2pqZGU0cWJsanRnNWVuEgtKZWNrVm9yb2JleVoAMAI4AUoIGgExFQIAAABQASD0BA';
const YANDEX_URL = 'https://tts.api.cloud.yandex.net/speech/v1/tts:synthesize';

/**
 * настройки yandex speech kit
 */
const YANDEX_LANG = 'ru-RU';
const YANDEX_VOICE = 'zahar';
const YANDEX_EMOTION = 'good';
//Ключи от API VK
const CALLBACK_API_CONFIRMATION_TOKEN = 'c236625a'; // Строка, которую должен вернуть сервер 
const VK_API_ACCESS_TOKEN = '05f22f63db3cd1a00033ed0d11645c7d90e9ee369934b57d14ac78fe564a1f3272487d4beed9911630e66'; // Ключ доступа сообщества 
const VK_API_SECRET_KEY = 'lascaJWI46LKjbKHbkn455sc54KN5xk'; //секретный ключ сообщества
//события API VK
const CALLBACK_API_EVENT_CONFIRMATION = 'confirmation'; // Тип события о подтверждении сервера
const CALLBACK_API_EVENT_MESSAGE_NEW = 'message_new'; // Тип события о новом сообщении 
const CALLBACK_API_EVENT_MESSAGE_REPLY = 'message_reply'; //тип события о отправленном сообщениии

const VK_API_ENDPOINT = 'https://api.vk.com/method/'; // Адрес обращения к API 
const VK_API_VERSION = '5.101'; // Используемая версия API 

/**
 * базовые пути к файлам
 */
const STATUS_DIRECTORY = './files/status';
const AUDIO_DIRECTORY = './files/audio';

/**
 * цвета кнопок
 */
const COLOR_POSITIVE = 'positive'; //зеленый
const COLOR_NEGATIVE = 'negative'; //красный
const COLOR_PRIMARY = 'primary'; //синий
const COLOR_SECONDARY = 'secondary'; //прозрачный

/**
 * типы кнопок
 */
const TYPE_TEXT = 'text';

/**
 * индитификатор команд входящих текстовых сообщений
 */
const TEXT_START = 'Начать';
const TEXT_INSTALLATION = 'Проработать установку';


/**
 * индитификатор команд кнопок
 */
const CMD_START = 'start';
const CMD_INSTALLATION = 'inst';
const CMD_SUB = 'submit';
const CMD_FLIP = 'flip';

/**
 * статусы разговора
 * 0 - начало
 * 1 - обработка неэффективной установки
 * 2 - обработка перевернутой установки
 */
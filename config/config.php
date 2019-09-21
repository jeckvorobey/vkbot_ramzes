<?php

/**
 * настройки yandex speech kit
 */
const YANDEX_LANG = 'ru-RU';
const YANDEX_VOICE = 'jane';
const YNDEX_SPEED = 0.9;
const YANDEX_EMOTION = 'neutral';

//события API VK
const CALLBACK_API_EVENT_CONFIRMATION = 'confirmation'; // Тип события о подтверждении сервера
const CALLBACK_API_EVENT_MESSAGE_NEW = 'message_new'; // Тип события о новом сообщении
const CALLBACK_API_EVENT_MESSAGE_REPLY = 'message_reply'; //тип события о отправленном сообщениии


/**
 * mime type
 */
const OGG = 'audio/ogg';

/**
 * базовые пути к файлам
 */
const STATUS_DIRECTORY = '../files/status';
const AUDIO_DIRECTORY = '../files/audio';
const LOG_DIRECTORY ='../files/logs/user';
const ERR_LOG_DIRECTORY = '../files/logs/err';

/*
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
const CMD_CLARIFY = 'clarify';
const CMD_CLARIFY_EFFECT = 'clarifyEffect';

/**
 * статусы разговора
 * 0 - начало
 * 1 - обработка неэффективной установки
 * 2 - обработка перевернутой установки
 */

/**
 * Пол пользователя
 *
 * 0 - пол не установлен
 * 1 - женщина
 * 2 - мужчина
 */
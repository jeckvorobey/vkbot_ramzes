<?php

const CALLBACK_API_CONFIRMATION_TOKEN = 'c236625a'; // Строка, которую должен вернуть сервер 
const VK_API_ACCESS_TOKEN = '669506a6a80ac69ec3a05eb55d842fe868f5fdff52e90252d7b21a389fc0a007bc58674e69f36894636b7'; // Ключ доступа сообщества 
const VK_API_SECRET_KEY = 'lascaJWI46LKjbKHbkn455sc54KN5xk'; //секретный ключ сообщества

const CALLBACK_API_EVENT_CONFIRMATION = 'confirmation'; // Тип события о подтверждении сервера
const CALLBACK_API_EVENT_MESSAGE_NEW = 'message_new'; // Тип события о новом сообщении 
const CALLBACK_API_EVENT_MESSAGE_REPLY = 'message_reply'; //тип события о отправленном сообщениии

const VK_API_ENDPOINT = 'https://api.vk.com/method/'; // Адрес обращения к API 
const VK_API_VERSION = '5.101'; // Используемая версия API 

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
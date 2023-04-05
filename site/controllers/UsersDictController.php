<?php

namespace app\controllers;

use Yii;

use app\models\User;

/**
 * контроллер для работы с пользователем (сотрудник)
 */
class UsersDictController extends DictsBaseController
{
    const MODEL = User::class;
    const MESS_NOTFOUND = 'Сотрудник не найден';
    const MESS_SAVED = 'Данные сотрудника сохранены';
    const MESS_KILLED = 'Сотрудник удалён';
}
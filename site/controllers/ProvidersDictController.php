<?php 

namespace app\controllers;

use app\models\Provider;

/**
 * Контроллер для работы с поставщиками ..
 */
class ProvidersDictController extends DictsBaseController
{
    const MODEL = Provider::class;
    const MESS_NOTFOUND = 'Поставщик не найден';
    const MESS_SAVED = 'Данные поставшика сохранены';
    const MESS_KILLED = 'Поставщик удалён';


}
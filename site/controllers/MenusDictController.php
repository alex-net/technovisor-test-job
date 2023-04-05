<?php 

namespace app\controllers;

use Yii;

use app\models\Menu;
use app\models\Provider;

/**
 * Контрроллер для работы с меню ..
 */
class MenusDictController extends DictsBaseController
{
    const MODEL = Menu::class;
    const MESS_NOTFOUND = 'Раздел меню не найден';
    const MESS_SAVED = 'Данные раздела меню сохранены';
    const MESS_KILLED = 'Раздел меню удалён';

    /**
     * Объект поставщика .. (владелец меню)
     */
    protected $provider;

}
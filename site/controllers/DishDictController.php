<?php 

namespace app\controllers;

use Yii;

use app\models\Menu;
use app\models\Provider;
use app\models\Dish;

/**
 * контроллер для работы с блюдами ...
 */
class DishDictController extends DictsBaseController
{
    const MODEL = Dish::class;
    const MESS_NOTFOUND = 'Раздел меню не найден';
    const MESS_SAVED = 'Данные раздела меню сохранены';
    const MESS_KILLED = 'Раздел меню удалён';

    /**
     * объект провайдера
     */
    protected $provider;

    /**
     * объект меню ...
     */
    protected $menu;
}
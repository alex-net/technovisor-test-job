<?php 

namespace app\controllers;

use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use Yii;

use app\models\User;
use app\models\Provider;
use app\models\Menu;
use app\models\Dish;
use app\models\Order;

/**
 * базовый контроллер для просмотра и редактирования сущностей...
 */
class DictsBaseController extends Controller
{
    const MODEL = '';
    const MESS_NOTFOUND = '';
    const MESS_SAVED =  '';
    const MESS_KILLED = '';

    public function init()
    {
        parent::init();

        foreach (['pid' => 'provider', 'mid' => 'menu'] as $k => $v) {
            if (!$this->hasProperty($v)) {
                continue;
            }
            $class = 'app\\models\\' . ucfirst($v);
            $this->$v = $class::getById($this->request->get($k));
            if (!$this->$v) {
                Yii::$app->session->addFlash('warning', 'Не указан уточняющий параметр');
                $redirect = ["/{$v}s-dict/index"];
                switch ($v) {
                    case 'menu':
                        if ($this->hasProperty('provider') && $this->provider) {
                            $redirect['pid'] = $this->provider->id;
                        }
                }
                return $this->redirect($redirect);
            }
        }
    }


    public function behaviors()
    {
        return [
            [
                'class' => AccessControl::class,
                'rules' => [
                    ['allow' => true, 'roles' => [User::PERM_ALLRULE]],
                ],
            ]
        ];
    }

    public function __get($name)
    {
        if (preg_match('#(.*?)OptionsList$#', $name, $match)) {
            $match = end($match);
            if (in_array($match, ['menu', 'provider']) && $this->hasProperty($match) &&  $this->$match) {
                $filter = [];
                if ($match == 'menu')
                    $filter['pid'] = $this->provider->id;
                return $this->$match::getOptionList('name', $filter);
            }
        }
        return parent::__get($name);
    }

    /**
     * просмотр списка ..
     */
    public function actionIndex()
    {
        $params = [];
        foreach (['provider', 'menu'] as $var) {
            if ($this->hasProperty($var)) {
                $params[substr($var, 0, 1)] = $this->$var;
            }
        }

        return $this->render('list', $params);
    }

    /**
     * заполнение параметров запроса .. данными из сохранённных переменных
     */
    private function fillParams(&$params, $suffix = '')
    {
        foreach (['provider', 'menu'] as $field ) {
            if ($this->hasProperty($field)) {
                $key = substr($field, 0, 1);
                $val = $this->$field;
                if ($suffix) {
                    $key .= 'id';
                    $val = $val->id;
                }
                $params[$key] = $val;
            }
        }
    }


    /**
     * действие редактирования и добавления
     *
     * @param      bool|int                        $id     Идентификатор сущности
     */
    public function actionEdit($id = 0)
    {
        $modelClass = static::MODEL;
        $m = $id ? $modelClass::getById($id) : new $modelClass();
        if (!$m) {
            throw new NotFoundHttpException(static::MESS_NOTFOUND);
        }

        if (!$id) {
            switch (get_class($m)) {
                case Menu::class:
                    $m->pid = $this->provider->id;
                    break;
                case Dish::class:
                    $m->mid = $this->menu->id;
                    break;
            }
        }
        switch (get_class($m)) {
            case Dish::class:
                if ($this->provider) {
                    $m->provider = $this->provider->id;
                }
                break;
            case User::class:
                $m->pass = '';
                break;
        }

        if ($this->request->isPost) {
            $post = $this->request->post();
            $redirect = ['index'];
            $this->fillParams($redirect, 'id');

            switch (true) {
                // сохранение ...
                case isset($post['save']):
                    if ($m->save($post)) {
                        Yii::$app->session->addFlash('success', static::MESS_SAVED);
                        return $this->redirect($redirect);
                    }
                    break;
                 case isset($post['kill']):
                     if ($m->kill()) {
                        Yii::$app->session->addFlash('info', static::MESS_KILLED);
                        return $this->redirect($redirect);
                     }
                     break;
            }
        }
        $params = ['model' => $m];
        $this->fillParams($params);


        return $this->render('edit', $params);
    }

}
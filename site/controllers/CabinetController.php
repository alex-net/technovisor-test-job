<?php

namespace app\controllers;

use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;
use Yii;

use app\models\Order;

/**
 * контроллер кабинета ... сотрудника с  возможностью сделать заказ . .
 */
class CabinetController extends OrdersDictController
{
    public function behaviors()
    {
        return [
            [
                'class' => AccessControl::class,
                'rules' => [
                    ['allow' => true, 'roles' => ['@']],
                ],
            ]
        ];
    }

    public function beforeAction($act)
    {
        if (!parent::beforeAction($act)) {
            return false;
        }
        if ($act->id == 'edit') {
            $oId = $act->controller->request->get('id');
            if ($oId && Order::getOwner($oId) != Yii::$app->user->id) {
                throw new ForbiddenHttpException('Доступ запрещён.');
            }
        }

        return true;
    }
}
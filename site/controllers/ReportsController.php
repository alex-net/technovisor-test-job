<?php

namespace app\controllers;

use yii\web\Controller;
use yii\filters\AccessControl;
use app\models\User;
use app\models\Order;

/**
 * контроллер для просмотра отчтов ..
 */
class ReportsController extends Controller
{
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

    /**
     *
     * заказы для поставщика.. на дату
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function actionProviderPerDate()
    {
        $date = $this->request->get('show-on-date');
        $sum = 0;
        if ($date) {
            $pd = Order::getReportForProvider($date);
            foreach ($pd->models as $row) {
                $sum += intval($row['qty']) * floatval($row['price']);
            }
        }
        return $this->render($this->action->id, [
            'date' => $date,
            'pd' => $pd ?? null,
            'sum' => $sum,
        ]);
    }

    /**
     * Заказы сотрудников на дату
     */
    public function actionEmployeesPerDate()
    {
        $date = $this->request->get('show-on-date');
        $perMonth = $this->request->get('per-month');
        $dp = Order::getReportForEmployees($date, $perMonth);
        if ($dp) {
            $sum = 0;
            foreach ($dp->models as $row) {
                $sum += floatval($row['sum']);
            }
        }

        return $this->render($this->action->id, [
            'date' => $date,
            'perMonth' => $perMonth,
            'dp' => $dp,
            'sum' => $sum ?? 0,
        ]);
    }
}
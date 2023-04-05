<?php 

namespace app\controllers;

use app\models\Order;
use app\models\OrderItem;
use Yii;

use app\models\User;

/**
 * контроллер для работы с заказами ..
 */
class OrdersDictController extends DictsBaseController
{
    const MODEL = Order::class;
    const MESS_NOTFOUND = 'Заказ не найден';
    const MESS_SAVED = 'Данные заказа сохранены';
    const MESS_KILLED = 'Заказ удалён';

    /**
     * Редактирование заказа и создание нового
     *
     * @param      bool    $id     The identifier
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function actionEdit($id = null)
    {
        $order = $id ? Order::getById($id) : new Order();

        if (!Yii::$app->user->can(User::PERM_ALLRULE)) {
            $order->eid = Yii::$app->user->id;
        }

        if ($this->request->isPost) {
            $post = $this->request->post();
            if ($order->load($post)) {
                switch (true) {
                    case isset($post['add']):
                        $order->items[] = new OrderItem();
                        break;
                    case isset($post['kill']):
                        if (isset($order->items[$post['kill']])) {
                            if (count($order->items) > 1) {
                                unset($order->items[$post['kill']]);
                                $order->items = array_values($order->items);
                            } else {
                                Yii::$app->session->addFlash('warning', 'Нельзя удалить единственную строку');
                            }
                        }
                        break;
                    case isset($post['save']):
                        if ($order->save()) {
                            Yii::$app->session->addFlash('success', 'Данные заказа сохранены');
                            return $this->redirect(['index']);
                        }
                        break;
                }
            }
        }

        return $this->render('edit', ['model' => $order]);
    }
}
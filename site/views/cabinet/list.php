<?php

use yii\grid\GridView;
use yii\helpers\Html;

use app\models\Order;

$date = Yii::$app->request->get('show-on-date');

$this->title = 'Мои заказы';

echo Html::a('Новый заказ', ['edit']);

echo Html::beginForm([''], 'get');
echo Html::input('date', 'show-on-date', $date);
echo Html::submitButton('применить', ['class' => 'btn btn-primary']);
echo Html::a('Сбросить', [''], ['class' => 'btn btn-default']);
echo Html::endForm();


echo GridView::widget([
    'dataProvider' => Order::getForList(Yii::$app->user->id, $date),
    'columns' => [
        [
            'attribute' => 'id',
            'label' => 'ID',
            'format' => 'html',
            'value' => function ($m) {
                return Html::a('Заказ №' . $m['id'], ['edit', 'id' => $m['id']]);
            }
        ],
        'date:date:Дата',
        [
            'attribute' => 'list',
            'label' => 'Содержимое',
            'format' => 'html',
            'value' => function ($m) {
                $list = explode('<>', $m['list']);
                $list = array_map(function ($el) {
                    list($name, $price, $qty) = explode('|', $el);
                    $price = floatval($price);
                    $qty = intval($qty);

                    return sprintf('%s: %.2f * %d = %.2f руб', $name, $price, $qty, $price * $qty);
                }, $list);

                return Html::ul($list);
            }
        ],
        'sum:decimal:Итого, руб',
    ],
]);
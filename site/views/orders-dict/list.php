<?php

use yii\grid\GridView;
use yii\helpers\Html;

use app\models\Order;


$this->title = 'Заказы сотрудников';

echo Html::a('Новый заказ', ['edit']);

echo GridView::widget([
    'dataProvider' => Order::getForList(),
    'columns' => [
        [
            'attribute' => 'id',
            'label' => 'ID',
            'format' => 'html',
            'value' => function ($m) {
                return Html::a('Заказ №' . $m['id'], ['edit', 'id' => $m['id']]);
            }
        ],
        'fio:html:ФИО',
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
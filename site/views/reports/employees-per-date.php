<?php

use yii\bootstrap5\Html;
use yii\grid\GridView;

$this->title = 'Заказ сотрудников на дату/месяц';

echo Html::beginForm([''], 'get');
echo Html::input('date', 'show-on-date', $date);
echo Html::tag('label', Html::checkbox('per-month', $perMonth, ['title' => 'За месяц от даты' ]). ' За месяц', ['class' => 'p-3 d-inline-block']);

echo Html::submitButton('применить', ['class' => 'btn btn-primary']);
echo Html::a('Сбросить', [''], ['class' => 'btn btn-default']);
echo Html::endForm();

if ($date) {
    echo GridView::widget([
        'dataProvider' => $dp,
        'showFooter' => true,
        'columns' => [
            [
                'attribute' => 'fio',
                'label' => 'ФИО',
                'footer' => 'Всего:',
            ],
            [
                'attribute' => 'list',
                'label' => 'Блюда и ценники',
                'format' => 'html',
                'value' => function($m) {
                    $list = explode('<>', $m['list']);
                    $menu = [];
                    foreach ($list as $row) {
                        list($id, $name, $price, $qty) = explode('|', $row);
                        if (empty($menu[$id])) {
                            $menu[$id] = [
                                'name' => $name,
                                'price' => floatval($price),
                                'qty' => intval($qty),
                            ];
                        } else {
                            $menu[$id]['qty'] += intval($qty);
                        }
                    }


                    $menu = array_map(function ($el){
                        return sprintf('<u>%s</u>: %.2f * %d = %.2f руб', $el['name'], $el['price'], $el['qty'], $el['price'] * $el['qty'] );
                    }, $menu);
                    return  Html::ul($menu, ['encode' => false]);
                }
            ],
            [
                'attribute' => 'sum',
                'format' => 'decimal',
                'label' => 'Итого',
                'footer' => "<b>$sum руб.</b>",
            ]
        ],
    ]);
}

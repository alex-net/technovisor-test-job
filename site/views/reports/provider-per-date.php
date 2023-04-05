<?php

use yii\grid\GridView;
use yii\bootstrap5\Html;

$this->title = 'Заказ поставщику на дату';

echo Html::beginForm([''], 'get');
echo Html::input('date', 'show-on-date', $date);
echo Html::submitButton('применить', ['class' => 'btn btn-primary']);
echo Html::a('Сбросить', [''], ['class' => 'btn btn-default']);
echo Html::endForm();
if ($date) {
    echo GridView::widget([
        'dataProvider' => $pd,
        'showFooter' => true,
        'columns' => [
            [
                'attribute' => 'name',
                'format' => 'text',
                'label' => 'Наименованиие',
                'footer' => '<b>Итого:</b>',
            ],
            'price:decimal:Цена',
            'qty:integer:Количество',
            [
                'label' => 'Сумма',
                'format' => 'decimal',
                'value' => function ($m) {
                    return floatval($m['price']) * intval($m['qty']);
                },
                 'footer' => '<b>' . Yii::$app->formatter->asDecimal($sum) . '</b>',
            ],
        ],
    ]);
}
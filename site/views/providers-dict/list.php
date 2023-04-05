<?php

use yii\grid\GridView;
use yii\helpers\Html;

use app\models\Provider;


$this->title = 'Список поставщиков';

echo Html::a('Новый поставщик', ['edit']);

echo GridView::widget([
    'dataProvider' => Provider::getForList(),
    'columns' => [
        'id:integer:ID',
        [
            'attribute' => 'name',
            'label' => 'Наименование',
            'format' => 'html',
            'value' => function($m) {
                return Html::a($m['name'], ['edit', 'id' => $m['id']]);
            }
        ],
        'active:boolean:Активный',
        [
            'attribute' => 'msco',
            'format' => 'html',
            'label' => 'Разделы меню',
            'value' => function ($m) {
                return Html::a($m['msco'], ['menus-dict/index', 'pid' => $m['id']]);
            }
        ]
    ],
]);
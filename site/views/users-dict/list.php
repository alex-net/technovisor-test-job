<?php

use yii\grid\GridView;
use yii\helpers\Html;

use app\models\User;


$this->title = 'Список сотрудников';

echo Html::a('Новый сотрудник', ['edit']);

echo GridView::widget([
    'dataProvider' => User::getForList(),
    'columns' => [
        'id:integer:ID',
        [
            'attribute' => 'fio',
            'label' => 'ФИО',
            'format' => 'html',
            'value' => function($m) {
                return Html::a($m['fio'], ['edit', 'id' => $m['id']]);
            }
        ],
        'active:boolean:Активный',
    ],
]);
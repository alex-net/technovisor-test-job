<?php

use yii\grid\GridView;
use yii\helpers\Html;

use app\models\Dish;


$this->title = 'Блюда раздел меню "' . $m->name . '"';
$this->params['breadcrumbs'][] = ['label'=>'Поставщики', 'url' => ['providers-dict/index']];
$this->params['breadcrumbs'][] = ['label'=>'Разделы меню (поставщик "' . $p->name . '")', 'url' => ['menus-dict/index', 'pid' => $p->id]];
$this->params['breadcrumbs'][] = $this->title;

echo Html::a('Новое блюдо', ['edit', 'pid' => $p->id, 'mid' => $m->id]);

echo GridView::widget([
    'dataProvider' => Dish::getForList(['mid' => $m->id]),
    'columns' => [
        'id:integer:ID',
        [
            'attribute' => 'name',
            'label' => 'Наименование',
            'format' => 'html',
            'value' => function($model) use ($p, $m) {
                return Html::a($model['name'], ['edit', 'id' => $model['id'], 'pid' => $p->id, 'mid' => $m->id]);
            }
        ],
        'active:boolean:Активный',
        'price:decimal:Цена',

    ],
]);
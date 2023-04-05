<?php

use yii\grid\GridView;
use yii\helpers\Html;

use app\models\Menu;


$this->title = 'Разделы меню поставщика "' . $p->name . '"';
$this->params['breadcrumbs'][] = ['label' => 'Поставщики', 'url' => ['providers-dict/index']];
$this->params['breadcrumbs'][] = $this->title;

echo Html::a('Новый раздел меню', ['edit', 'pid' => $p->id]);

echo GridView::widget([
    'dataProvider' => Menu::getForList(['pid' => $p->id]),
    'columns' => [
        'id:integer:ID',
        [
            'attribute' => 'name',
            'label' => 'Наименование',
            'format' => 'html',
            'value' => function($m) use ($p) {
                return Html::a($m['name'], ['edit', 'id' => $m['id'], 'pid' => $p->id]);
            }
        ],
        [
            'attribute' => 'dsco',
            'label' => 'Блюда',
            'format' => 'html',
            'value' => function ($m) use ($p) {
                return Html::a($m['dsco'], ['/dish-dict/index', 'pid' => $p->id, 'mid' => $m['id']]);
            }
        ],
    ],
]);
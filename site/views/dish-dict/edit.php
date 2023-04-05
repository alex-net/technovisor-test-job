<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = $model->id ? 'Редактирование блюда '. $model->name : 'Новое блюдо';

$this->params['breadcrumbs'][] = ['label' => 'Поставщики', 'url' => ['providers-dict/index']];
$this->params['breadcrumbs'][] = ['label' => 'Разделы меню (' . $p->name . ')', 'url' => ['menus-dict/index', 'pid' => $p->id]];
$this->params['breadcrumbs'][] = ['label' => 'Блюда раздела меню (' . $m->name . ')', 'url' => ['dish-dict/index', 'pid' => $p->id, 'mid' => $m->id]];
$this->params['breadcrumbs'][] = $this->title;

$f = ActiveForm::begin();

echo $f->field($model, 'name');
echo $f->field($model, 'price');
echo $f->field($model, 'active')->checkbox();
echo $f->field($model, 'mid')->dropdownList($m::getOptionList('name', ['pid' => $p->id]), ['prompt' => 'Не указано']);
echo $f->field($model, 'descr')->textarea();


echo Html::submitButton('Сохранить', ['class' => 'btn btn-primary', 'name' => 'save']);
if ($model->id) {
    echo Html::submitButton('Удалить', ['class' => 'btn btn-danger', 'name' => 'kill']);
}

ActiveForm::end();
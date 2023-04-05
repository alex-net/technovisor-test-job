<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = $model->id ? 'Редактирование разела меню '. $model->name : 'Новый разднл меню';

$this->params['breadcrumbs'][] = ['label' => 'Поставщики', 'url' => ['providers-dict/index']];
$this->params['breadcrumbs'][] = ['label' => 'Разделы меню поставщик "' . $p->name . '"', 'url' => ['index', 'pid' => $p->id]];
$this->params['breadcrumbs'][] = $this->title;

$f = ActiveForm::begin();

echo $f->field($model, 'name');
echo $f->field($model, 'pid')->dropdownList($this->context->providerOptionsList, ['prompt' => 'не выбран']);


echo Html::submitButton('Сохранить', ['class' => 'btn btn-primary', 'name' => 'save']);
if ($model->id) {
    echo Html::submitButton('Удалить', ['class' => 'btn btn-danger', 'name' => 'kill']);
}

ActiveForm::end();
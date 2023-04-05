<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = $model->id ? 'Редактирование поставщика "'. $model->name . '"' : 'Новый поставщик';

$this->params['breadcrumbs'][] = ['label' => 'Поствщики','url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$f = ActiveForm::begin();

echo $f->field($model, 'name');
echo $f->field($model, 'active')->checkbox();


echo Html::submitButton('Сохранить', ['class' => 'btn btn-primary', 'name' => 'save']);
if ($model->id) {
    echo Html::submitButton('Удалить', ['class' => 'btn btn-danger', 'name' => 'kill']);
}

ActiveForm::end();
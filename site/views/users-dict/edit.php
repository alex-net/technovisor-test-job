<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = $model->id ? 'Редактирование пользователя '. $model->fio : 'Новый пользователь';

$this->params['breadcrumbs'][] = [
    'label' => 'Сотрудники',
    'url' => ['index'],
];

$f = ActiveForm::begin();

echo $f->field($model, 'fio');
echo $f->field($model, 'pass')->textInput(['placeholder' => 'Без изменений']);
echo $f->field($model, 'active')->checkbox();

echo Html::submitButton('Сохранить', ['class' => 'btn btn-primary', 'name' => 'save']);
if ($model->id) {
    echo Html::submitButton('Удалить', ['class' => 'btn btn-danger', 'name' => 'kill']);
}

ActiveForm::end();
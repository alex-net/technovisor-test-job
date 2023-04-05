<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

use app\models\User;

$this->title = $model->id ? 'Редактирование заказа ' : 'Новый заказ';

$this->params['breadcrumbs'][] = ['label' => 'Заказы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$f = ActiveForm::begin();

echo $f->field($model, 'eid')->dropdownList(User::getOptionList('fio'), ['prompt' => 'Сотрудник не выбран']);
echo $f->field($model, 'date')->input('date');

echo Html::beginTag('div', ['class' => 'row']);
    echo Html::tag('div', 'Блюдо', ['class' => 'col']);
    echo Html::tag('div', 'Количество', ['class' => 'col']);
    echo Html::tag('div', '', ['class' => 'col-1']);
echo Html::endTag('div');
for ($i = 0; $i < count($model->items); $i++) {
    echo Html::beginTag('div', ['class' => 'row']);
    echo Html::tag('div', $f->field($model->items[$i], "[$i]did")->label(false)->dropdownList($model->items[$i]->didOptionsList(), ['prompt' => 'Не указано']), ['class' => 'col']);
    echo Html::tag('div', $f->field($model->items[$i], "[$i]qty")->input('number')->label(false), ['class' => 'col']);
    echo Html::tag('div', Html::submitButton('-', ['class' => 'btn btn-danger', 'title' => 'Удалить', 'name' => 'kill', 'value' => $i]), ['class' => 'col-1 text-end']);
    echo Html::endTag('div');
}
echo Html::submitButton('+', ['class' => 'btn btn-primary', 'name' => 'add']);
// echo $f->field($model, 'active')->checkbox();

echo Html::beginTag('div');
echo Html::submitButton('Сохранить', ['class' => 'btn btn-primary', 'name' => 'save']);
if ($model->id) {
    echo Html::submitButton('Удалить', ['class' => 'btn btn-danger', 'name' => 'kill']);
}

echo Html::endTag('div');
ActiveForm::end();
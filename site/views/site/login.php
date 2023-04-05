<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var app\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Please fill out the following fields to login:</p>

    <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'id')->label('ФИО')->dropdownList($model->getOptionList('fio'), ['prompt' => 'Не выбрано']) ?>

        <?= $form->field($model, 'pass')->passwordInput() ?>


        <?= Html::submitButton('Вход', ['class' => 'btn btn-primary']) ?>


    <?php ActiveForm::end(); ?>

</div>

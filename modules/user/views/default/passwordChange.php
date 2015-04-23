<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 20.04.15
 * Time: 13:23
 */
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Изменить пароль';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-change-password">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Пожалуйста укажите текущий и новый пароли:</p>

    <?php $form = ActiveForm::begin(['id' => 'change-password-form']); ?>

    <div class="row">
        <div class="col-lg-5">
            <?= $form->field($model, 'password_old')->passwordInput() ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-5">
            <?= $form->field($model, 'password')->passwordInput() ?>
        </div>
    </div>


    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
    </div>


    <?php ActiveForm::end(); ?>

</div>
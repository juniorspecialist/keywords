<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 13.05.15
 * Time: 15:37
 */
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<div class="ticket-answer-form">

    <?php $form = ActiveForm::begin([]); ?>

    <?=$form->errorSummary($model);?>

    <?=\yii\helpers\Html::activeHiddenInput($model, 'ticket_id');?>

    <?= $form->field($model, 'answer')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton('Отправить', ['class' => 'btn btn-success']) ?>

        <?php ActiveForm::end(); ?>


    </div>

    <?php $form = ActiveForm::begin(['action'=>'/ticket/default/close', 'method'=>'POST']); ?>

    <?=\yii\helpers\Html::hiddenInput('id', $model->ticket_id);?>

    <?= Html::submitButton('Закрыть тикет', ['class' => 'btn btn-info pull-right', 'style'=>'margin-left:30px; margin-top:-55px;']) ?>

    <?php ActiveForm::end(); ?>



</div>
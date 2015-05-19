<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 15.05.15
 * Time: 14:55
 */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

?>

<p><h2 class="">Пополнение баланса через Robokassa</h2></p>


<div class="financy-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'sum_operation', [
        'inputOptions' => [
            'placeholder' => 'Сумма пополнения',
        ],
    ])->inline()->textInput(['maxlength' => 8]); ?>

    <div class="form-group">
        <?= Html::submitButton('Пополнить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

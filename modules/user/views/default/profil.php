<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 20.04.15
 * Time: 13:50
 */

use yii\helpers\Html;
use yii\widgets\DetailView;


$this->title = 'Профиль пользователя:'.$model->username;
$this->params['breadcrumbs'][] = ['label' => 'Профиль'];

?>
<div class="profil-view">

    <h1><?= Html::encode($this->title) ?></h1>


    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'username',
            'email',
            'statusname',
            //'balance',
          //'balance:html',  // description attribute in HTML
          [                    // the owner name of the model
              'label' => 'Баланс',
              'value' => $model->balance.' руб.',
          ],
            [
                'label'=>'Зарегистрирован',
                'value'=>date('Y-m-d H:i:s', $model->created_at)
            ],
            //'',
            //'created_at',
        ],
    ]) ?>

</div>

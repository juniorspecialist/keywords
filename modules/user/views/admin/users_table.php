<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 19.04.15
 * Time: 22:06
 */
use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Пользователи';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            'id',
            'username',
            'email',
            'statusname',
            'balance',
            // 'created_at',

            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>

<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 13.05.15
 * Time: 15:32
 */

use yii\helpers\Html;

?>

<?php
//подсвечиваем ответы админа другим цветом
if($answer->user_id==$model->user_id){//user
    $class = 'panel-info';
}else{
    $class = 'panel-success';//admin
}
?>

<div class="panel <?=$class;?>">
    <!-- Default panel contents -->
    <div class="panel-heading"><?=$answer->user->username.' : '.date('Y-m-d H:i:s',$answer->created_at);?></div>
    <div class="panel-body">
        <p><?=Html::encode($answer->answer);?></p>
    </div>

    <!-- Table -->
<!--    <table class="table">-->
<!--        -->
<!--    </table>-->
</div>
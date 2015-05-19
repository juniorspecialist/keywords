<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 13.04.15
 * Time: 13:58
 */
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \app\modules\user\models\LoginForm */

$this->title = 'Авторизация';
$this->params['breadcrumbs'][] = $this->title;
?>

<script type="text/javascript">
    var reformalOptions = {
        project_id: 890333,
        project_host: "mykeywords.reformal.ru",
        tab_orientation: "left",
        tab_indent: "50%",
        tab_bg_color: "#1176ba",
        tab_border_color: "#FFFFFF",
        tab_image_url: "http://tab.reformal.ru/T9GC0LfRi9Cy0Ysg0Lgg0L%252FRgNC10LTQu9C%252B0LbQtdC90LjRjw==/FFFFFF/2a94cfe6511106e7a48d0af3904e3090/left/1/tab.png",
        tab_border_width: 2
    };

    (function() {
        var script = document.createElement('script');
        script.type = 'text/javascript'; script.async = true;
        script.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'media.reformal.ru/widgets/v3/reformal.js';
        document.getElementsByTagName('head')[0].appendChild(script);
    })();
</script><noscript><a href="http://reformal.ru"><img src="http://media.reformal.ru/reformal.png" /></a><a href="http://mykeywords.reformal.ru">Oтзывы и предложения для Ключевые слова всем</a></noscript>

<div class="form-box" id="login-box">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Пожалуйста заполните поля для авторизации:</p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
            <?= $form->field($model, 'username') ?>
            <?= $form->field($model, 'password')->passwordInput() ?>
            <?= $form->field($model, 'rememberMe')->checkbox() ?>
            <div style="color:#999;margin:1em 0">
                Если вы забыли свой пароль то можете <?= Html::a('сбросить его', ['request-password-reset']) ?>.
            </div>
            <div class="form-group">
                <?= Html::submitButton('Авторизация', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
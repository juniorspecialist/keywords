<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\components\widgets\Alert;
use app\assets\AppAsset;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Yii::$app->name.Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>

<?php $this->beginBody() ?>
    <div class="wrap">
        <?php
        //TODO переделать меню под нормальный вид
            NavBar::begin([
                'brandLabel' => 'MYKEYWORDS.RU',
                'brandUrl' => Yii::$app->homeUrl,
                'options' => [
                    'class' => 'navbar-inverse navbar-fixed-top',
                ],
            ]);



            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-right'],
                'items' => [
                    ['label' => 'Главная', 'url' => ['/site/index'],'visible'=>Yii::$app->user->isGuest,],
                    ['label' => 'О нас', 'url' => ['/site/about'],'visible'=>Yii::$app->user->isGuest,],
                    ['label' => 'Контакты', 'url' => ['/site/contact'],'visible'=>Yii::$app->user->isGuest,],

                    ['label' => 'Пользователи',
                        'url' => ['/user/admin/'],
                        'visible'=>Yii::$app->user->identity && Yii::$app->user->identity->isAdmin()
                    ],

                    [
                        'label'=>'Профиль',
                        'url'=>['/user/default/profil/'],
                        'visible'=>!Yii::$app->user->isGuest,
                    ],

                    [
                        'label'=>'Изменить пароль',
                        'url'=>['/user/default/change-password/'],
                        'visible'=>!Yii::$app->user->isGuest,
                    ],

                    ['label' => 'Задания',
                        'url' => ['/tasks/'],
                        'visible'=>!Yii::$app->user->isGuest],

                    ['label' => 'Тикеты',
                        'url' => ['/ticket/'],
                        'visible'=>!Yii::$app->user->isGuest],

                    ['label' => 'Финансы',
                        'url' => ['/financy/'],
                        'visible'=>!Yii::$app->user->isGuest],

                    Yii::$app->user->isGuest ?
                        ['label' => 'Регистрация', 'url' => ['/user/default/signup']] :
                        '',
                    Yii::$app->user->isGuest ?
                        ['label' => 'Авторизация', 'url' => ['/user/default/login']] :
                        ['label' => 'Выход (' . Yii::$app->user->identity->username . ')',
                            'url' => ['/user/default/logout'],
                            'linkOptions' => ['data-method' => 'post']],


                ],
            ]);
            NavBar::end();
        ?>

        <div class="container">
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs'])&&(!Yii::$app->user->isGuest) ? $this->params['breadcrumbs'] : [],
            ]) ?>

            <?php
            Alert::widget()

            ?>

            <?= $content ?>

        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p class="pull-left">&copy;MYKEYWORDS.RU <?= date('Y') ?></p>
        </div>
    </footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

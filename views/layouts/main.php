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
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>

<?php $this->beginBody() ?>
    <div class="wrap">
        <?php
            NavBar::begin([
                'brandLabel' => Yii::$app->name,
                'brandUrl' => Yii::$app->homeUrl,
                'options' => [
                    'class' => 'navbar-inverse navbar-fixed-top',
                ],
            ]);
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-right'],
                'items' => [
                    ['label' => 'Главная', 'url' => ['/site/index']],
                    ['label' => 'О нас', 'url' => ['/site/about']],
                    ['label' => 'Контакты', 'url' => ['/site/contact']],

                    ['label' => 'Пользователи',
                        'url' => ['/users/'],
                        'visible'=>!Yii::$app->user->identity->isAdmin()],

                    ['label' => 'Задания',
                        'url' => ['/tasks/'],
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
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]) ?>
            <?= Alert::widget() ?>
            <?= $content ?>

            <?php


            // http://www.elasticsearch.org/guide/en/elasticsearch/reference/current/query-dsl-flt-query.html
//            $query = \app\models\Bulk::find()->query([
//                'regexp' => [
//                    'word' => 'мульт.*|фильм.*|порн.*',
//                ]
//            ])->filter(['bool'=>['must_not'=>['terms'=>['word'=>['видео', 'скачать','лет','1','7']]]]]);
//
//            $count = $query->count(); // gives you all the documents
//            echo 'count='.$count;



            $model = \app\models\Tasks::findOne(5);

            $bulk = new \app\models\Bulk();

            $bulk->createQuery($model);

            ?>

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

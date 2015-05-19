<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 27.04.15
 * Time: 19:34
 */

namespace app\modules\user\controllers;
use app\modules\user\models\PasswordChangeForm;
use app\modules\user\models\User;
use app\modules\user\models\UserSearch;
use yii\web\Controller;

use app\modules\user\models\ConfirmEmailForm;
use app\modules\user\models\LoginForm;
use app\modules\user\models\PasswordResetRequestForm;
use app\modules\user\models\ResetPasswordForm;
use app\modules\user\models\SignupForm;
use yii\base\InvalidParamException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
//use yii\base\Controller;
use Yii;


class AdminController extends Controller{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index'],
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        //'roles' => ['@'],
                        'matchCallback' => function() {
                            if(Yii::$app->user->identity && Yii::$app->user->identity->isAdmin()){
                                return true;
                            }
                            return false;
                        }
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /*
     * список всех пользователей системы
     */
    public function actionIndex(){

        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return
            $this->render('users_table',[
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
    }

    /*
     * редактирование данных пользователя
     */
    public function actionUpdate(){

    }
}
<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 15.05.15
 * Time: 13:42
 */

namespace app\modules\pay\controllers;

use app\models\Financy;
use app\modules\ticket\models\Ticket;
use app\modules\ticket\models\TicketAnswer;
use app\modules\ticket\models\TicketSearch;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\BadRequestHttpException;
use app\models\Robokassa;

class RobokassaController extends Controller{


    public function readParams(){

        $shp = [];

        foreach ($_REQUEST as $key => $param) {
            if (strpos(strtolower($key), 'shp') === 0) {
                $shp[$key] = $param;
            }
        }

        return $shp;
    }

    public function actionResult(){

        if (!isset($_REQUEST['OutSum'], $_REQUEST['InvId'], $_REQUEST['SignatureValue'])) {
            throw new BadRequestHttpException;
        }

        $merchant = Yii::$app->get('robokassa');

        $shp = $this->readParams();

        if ($merchant->checkSignature($_REQUEST['SignatureValue'], $_REQUEST['OutSum'], $_REQUEST['InvId'], $merchant->sMerchantPass2, $shp)) {
            //return $this->callback($merchant, $_REQUEST['InvId'], $_REQUEST['OutSum'], $shp);


            //заявка на пополнение баланса
            $financy = $this->loadModel($_REQUEST['InvId']);

            if($financy->status !== Financy::STATUS_PAID){
                $financy->status = Financy::STATUS_PAID;

                $financy->update();
                //обновим статус заявки на пополнение
                //$financy->updateAttributes(['status' => Financy::STATUS_PAID]);

                //пополним баланс пользователя
                $user = $financy->user;

                $user->updateCounters(['balance' => (int)$_REQUEST['OutSum']]);

                return 'OK'.$financy->id;
            }else{
                return 'Статус заявки не соотвествует';
            }

        }
        throw new BadRequestHttpException;
    }

    public function actionSuccess(){
        if (!isset($_REQUEST['OutSum'], $_REQUEST['InvId'], $_REQUEST['SignatureValue'])) {
            throw new BadRequestHttpException;
        }

        $merchant = Yii::$app->get('robokassa');

        $shp  = $this->readParams();

        if ($merchant->checkSignature($_REQUEST['SignatureValue'], $_REQUEST['OutSum'], $_REQUEST['InvId'], $merchant->sMerchantPass1, $shp)) {
            //return $this->callback($merchant, $_REQUEST['InvId'], $_REQUEST['OutSum'], $shp);
            //$model = $this->loadModel($_REQUEST['InvId']);
            //$model->updateAttributes(['status' => Invoice::STATUS_ACCEPTED]);
            Yii::$app->getSession()->setFlash('success', 'Спасибо, ваш баланс успешно пополнен.');

            return $this->redirect('/financy/');
        }
        throw new BadRequestHttpException;
    }

    public function actionFail(){

        if (!isset($_REQUEST['OutSum'], $_REQUEST['InvId'])) {
            throw new BadRequestHttpException;
        }

        $shp  = $this->readParams();

        $model = $this->loadModel($_REQUEST['InvId']);

        if ($model->status == Financy::STATUS_NOT_PAID) {
            $model->status = Financy::STATUS_PAID_FAIL;
            $model->update();
            //$model->updateAttributes(['status' => Financy::STATUS_PAID_FAIL]);
            return 'Ok';
        } else {
            return 'Status has not changed';
        }
    }

    /*
     * форма пополнения через робокассу
     */
    public function actionIndex(){

        $model = new Financy();

        $model->type_pay_system = Financy::TYPE_PAY_SYSTEM_ROBOKASSA;//тип пополнения Робокасса

        $model->type_operation = Financy::TYPE_OPERATION_PLUS;//операция пополнения

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            $merchant = Yii::$app->get('robokassa');

            //формируем ссылку на оплату+ делаем редирект
            return $merchant->payment($model->sum_operation, $model->id, 'Пополнение счета', null, Yii::$app->user->identity->email);

        } else {
            return $this->render('_form', [
                'model' => $model,
            ]);
        }
    }

    /**
     * @param integer $id
     * @return Financy
     * @throws \yii\web\BadRequestHttpException
     */
    protected function loadModel($id) {
        $model = Financy::findOne($id);
        if ($model === null) {
            throw new BadRequestHttpException;
        }
        return $model;
    }
}
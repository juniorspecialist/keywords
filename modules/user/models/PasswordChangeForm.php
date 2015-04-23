<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 20.04.15
 * Time: 12:19
 */

namespace app\modules\user\models;

use yii\base\InvalidParamException;
use yii\base\Model;
use Yii;

class PasswordChangeForm extends Model {
    public $password;
    public $password_old;
    /**
     * @var \app\models\User
     */
    private $_user;

    public function __construct( $config = [])
    {
        $this->_user = User::findOne(['id'=>Yii::$app->user->id]);
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['password','password_old'], 'required'],
            ['password', 'string', 'min' => 6],
            ['password','validatePassword']
        ];
    }

    public function attributeLabels()
    {
        return [
            'password' => 'Пароль',
            'password_old' => 'Текущий пароль',
        ];
    }

    /*
     * валидируем текущий пароль юзера, вверно ли он указал старый пароль
     */
    public function validatePassword(){
        if(!$this->hasErrors()){

            //validatePassword
            $user = $this->_user;

            if(!$user->validatePassword($this->password_old)){
                $this->addError('password_old','Текущий пароль указан не верно');
            }
        }
    }

    /*
     * установим новый пароль для юзера
     */
    public function setNewPassword(){

        $user = $this->_user;
        $user->setPassword($this->password);
        $user->removePasswordResetToken();
        return $user->save(false);
    }

    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findOne(['id'=>Yii::$app->user->id]);
        }

        return $this->_user;
    }
}
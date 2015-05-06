<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 13.04.15
 * Time: 13:57
 */

namespace app\components\widgets;

use kartik\growl\Growl;


class Alert extends \yii\bootstrap\Widget
{
    /**
     * @var array the alert types configuration for the flash messages.
     * This array is setup as $key => $value, where:
     * - $key is the name of the session flash variable
     * - $value is the bootstrap alert type (i.e. danger, success, info, warning)
     */
    public $alertTypes = [
        'error'   => 'alert-danger',
        'danger'  => 'alert-danger',
        'success' => 'alert-success',
        'info'    => 'alert-info',
        'warning' => 'alert-warning'
    ];
    /**
     * @var array the options for rendering the close button tag.
     */
    public $closeButton = [];
    public function init()
    {
        parent::init();
        $session = \Yii::$app->getSession();
        $flashes = $session->getAllFlashes();
        $appendCss = isset($this->options['class']) ? ' ' . $this->options['class'] : '';
        foreach ($flashes as $type => $data) {
            if (isset($this->alertTypes[$type])) {
                $data = (array) $data;
                foreach ($data as $i => $message) {
                    /* initialize css class for each alert box */
                    $this->options['class'] = $this->alertTypes[$type] . $appendCss;
                    /* assign unique id to each alert box */
                    $this->options['id'] = $this->getId() . '-' . $type . '-' . $i;

                    echo Growl::widget([

                        'body' => $message,
                        'closeButton' => $this->closeButton,
                        'options' => $this->options,
                        'delay' => false,
                        'pluginOptions' => [
                            //'icon_type'=>'image',
                            'showProgressbar' => false,
                            'placement' => [
                                'from' => 'top',
                                'align' => 'right',
                            ],
                        ]
//                        'type' => Growl::TYPE_MINIMALIST,
//                        'title' => 'Kartik Visweswaran',
//                        'icon' => '/images/kartik.png',
//                        'iconOptions' => ['class'=>'img-circle pull-left'],
//                        'body' => 'Momentum reduce child mortality effectiveness incubation empowerment connect.',
//                        'showSeparator' => false,
//                        'delay' => 7500,
//                        'pluginOptions' => [
//                            'icon_type'=>'image',
//                            'showProgressbar' => false,
//                            'placement' => [
//                                'from' => 'top',
//                                'align' => 'right',
//                            ],
//                        ]
                    ]);
                    /*
                    echo \yii\bootstrap\Alert::widget([
                        'body' => $message,
                        'closeButton' => $this->closeButton,
                        'options' => $this->options,

                    ]);*/
                }
                $session->removeFlash($type);
            }
        }
    }
}
<?php

namespace infoweb\settings;

use Yii;
use yii\base\Event;
use yii\db\ActiveRecord;

class Module extends \yii\base\Module
{
    /**
     * Allow content duplication with the "duplicateable" plugin
     * @var boolean
     */
    public $allowContentDuplication = true;

    public function init()
    {
        parent::init();

        Yii::configure($this, require(__DIR__ . '/config.php'));
    }
}
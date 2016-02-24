<?php

namespace infoweb\settings\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use creocoder\translateable\TranslateableBehavior;
use infoweb\settings\models\SettingValue;
use infoweb\settings\models\SettingCategory;

class Setting extends \yii\db\ActiveRecord
{
    const TYPE_SYSTEM = 'system';
    const TYPE_USER_DEFINED = 'user-defined';
    const TEMPLATE_TEXT = 'text';
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'settings';
    }

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'transleateable' => [
                'class' => TranslateableBehavior::className(),
                'translationAttributes' => [
                    'value'
                ]
            ],
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_at',
                ],
                'value' => function() { return time(); },
            ]
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key', 'category_id', 'label', 'type', 'template'], 'required'],
            ['key', 'unique'],
            [['category_id'], 'integer'],
            // Types
            [['type'], 'string'],
            ['type', 'in', 'range' => [self::TYPE_SYSTEM, self::TYPE_USER_DEFINED]],
            ['type', 'default', 'value' => self::TYPE_SYSTEM],
            ['translateable', 'default', 'value' => 1]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'key' => Yii::t('app', 'Key'),
            'type' => Yii::t('app', 'Type'),
            'category_id' => Yii::t('app', 'Category'),
            'label' => Yii::t('app', 'Label'),
            'template' => Yii::t('app', 'Template'),
            'translateable' => Yii::t('infoweb/settings', 'Translateable'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTranslations()
    {
        return $this->hasMany(SettingValue::className(), ['setting_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(SettingCategory::className(), ['id' => 'category_id']);
    }
}

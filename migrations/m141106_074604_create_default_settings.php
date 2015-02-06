<?php

use yii\db\Schema;
use yii\db\Migration;
use infoweb\settings\models\Setting;
use infoweb\settings\models\SettingValue;

class m141106_074604_create_default_settings extends Migration
{
    public function safeUp()
    {
        // Load application languages
        $languages = Yii::$app->params['languages'];
        
        // Create SEO settings
        $seoSettings = [
            'seo/meta/title'                        => [
                'label'         => 'Metatag - Title',
                'value'         => 'Title',
                'translateable' => 1
            ],
            'seo/meta/description'                  => [
                'label'         => 'Metatag - Description',
                'value'         => 'Description',
                'translateable' => 1
            ],
            'seo/meta/keywords'                     => [
                'label'         => 'Metatag - Keywords',
                'value'         => 'Keywords',
                'translateable' => 1
            ],
            'seo/analytics/email-google-account'    => [
                'label'         => 'E-mailadres Google account',
                'value'         => 'google@infoweb.be',
                'translateable' => 0
            ]               
        ];
        
        foreach ($seoSettings as $k => $v) {
            // Create setting
            $setting = new Setting([
                'key'           => $k,
                'category_id'   => 2,
                'label'         => $v['label'],
                'type'          => Setting::TYPE_USER_DEFINED,
                'template'      => Setting::TEMPLATE_TEXT,
                'translateable' => $v['translateable']
            ]);
            
            $setting->save(false);
            
            // Set values
            foreach ($languages as $languageId => $languageName) {
                $setting->language = $languageId;
                $setting->value = $v['value'];
                $setting->saveTranslation();    
            }    
        }

        // Create Social settings
        $socialSettings = [
            'social/facebook'   => [
                'label'         => 'Facebook account',
                'value'         => 'https://www.facebook.com',
                'translateable' => 1
            ],
            'social/twitter'    => [
                'label'         => 'Twitter account',
                'value'         => 'https://www.twitter.com/',
                'translateable' => 1
            ],
            'social/google+'    => [
                'label'         => 'Google+ account',
                'value'         => 'https://plus.google.com',
                'translateable' => 1
            ],
            'social/linkedin'   => [
                'label'         => 'LinkedIn account',
                'value'         => 'https://www.linkedin.com/',
                'translateable' => 1
            ]    
        ];
        
        foreach ($socialSettings as $k => $v) {
            // Create setting
            $setting = new Setting([
                'key'           => $k,
                'category_id'   => 3,
                'label'         => $v['label'],
                'type'          => Setting::TYPE_USER_DEFINED,
                'template'      => Setting::TEMPLATE_TEXT,
                'translateable' => $v['translateable']
            ]);
            
            $setting->save(false);
            
            // Set values
            foreach ($languages as $languageId => $languageName) {
                $setting->language = $languageId;
                $setting->value = $v['value'];
                $setting->saveTranslation();    
            }    
        }
    }

    public function safeDown()
    {
        echo "m141106_074604_create_default_settings cannot be reverted.\n";

        return false;
    }
}

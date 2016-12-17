<?php

use yii\db\Migration;

class m161217_144527_changeSettingsValuesSize extends Migration
{
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        $this->alterColumn('settings_values', 'value', $this->text());
    }

    public function safeDown()
    {
        $this->alterColumn('settings_values', 'value', $this->string());
    }
}

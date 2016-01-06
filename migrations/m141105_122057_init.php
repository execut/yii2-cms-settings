<?php

use yii\db\Schema;
use yii\db\Migration;

class m141105_122057_init extends Migration
{
    public function up()
    {
        $tableOptions = null;
        
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        // Create 'settings' table
        $this->createTable('{{%settings}}', [
            'id'            => Schema::TYPE_PK,
            'key'           => Schema::TYPE_STRING . '(255) NOT NULL',
            'category_id'   => Schema::TYPE_INTEGER . ' NOT NULL',
            'label'         => Schema::TYPE_STRING . '(255) NOT NULL',
            'type'          => "ENUM('system','user-defined') NOT NULL DEFAULT 'user-defined'",
            'template'      => "ENUM('text') NOT NULL DEFAULT 'text'",
            'translateable' => 'TINYINT(3) UNSIGNED NOT NULL DEFAULT \'1\'',
            'created_at'    => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL',
            'updated_at'    => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL',
        ], $tableOptions);
        
        $this->createIndex('key', '{{%settings}}', 'key', true);
        $this->createIndex('category_id', '{{%settings}}', 'category_id');
        
        // Create 'settings_values' table
        $this->createTable('{{%settings_values}}', [
            'setting_id'    => Schema::TYPE_INTEGER . ' NOT NULL',
            'language'      => Schema::TYPE_STRING . '(10) NOT NULL',
            'value'         => Schema::TYPE_STRING . '(255) NOT NULL',
            'created_at'    => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL',
            'updated_at'    => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL',
        ], $tableOptions);
        
        $this->addPrimaryKey('setting_id_language', '{{%settings_values}}', ['setting_id', 'language']);
        $this->createIndex('language', '{{%settings_values}}', 'language');
        $this->addForeignKey('FK_SETTINGS_VALUES_SETTING_ID', '{{%settings_values}}', 'setting_id', '{{%settings}}', 'id', 'CASCADE', 'RESTRICT');
        
        // Create 'settings_categories' table
        $this->createTable('{{%settings_categories}}', [
            'id'                    => Schema::TYPE_PK,
            'name'                  => Schema::TYPE_STRING . "(255) NOT NULL"
        ], $tableOptions);
        
        // Insert the default categories
        $this->insert('{{%settings_categories}}', ['name' => 'Systeem']);
        $this->insert('{{%settings_categories}}', ['name' => 'SEO']);
        $this->insert('{{%settings_categories}}', ['name' => 'Social']);
        $this->insert('{{%settings_categories}}', ['name' => 'Formulieren']);
    }

    public function down()
    {
        $this->dropTable('settings_categories');
        $this->dropTable('settings_tables');
        $this->dropTable('settings');
    }
}

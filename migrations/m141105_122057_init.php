<?php

use yii\db\Schema;
use yii\db\Migration;

class m141105_122057_init extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        if ($this->db->driverName === 'pgsql') {
            $this->execute('CREATE TYPE settings_type AS ENUM (\'system\',\'user-defined\')');
            $this->execute('CREATE TYPE text_type AS ENUM (\'text\')');
        }

        // Create 'settings' table
        $this->createTable('{{%settings}}', [
            'id'            => $this->primaryKey(),
            'key'           => $this->string()->notNull(),
            'category_id'   => $this->integer()->notNull(),
            'label'         => $this->string()->notNull(),
            'type'          => "settings_type NOT NULL DEFAULT 'user-defined'",
            'template'      => "text_type NOT NULL DEFAULT 'text'",
            'translateable' => $this->integer(3)->unsigned()->notNull()->defaultValue('1'),
            'created_at'    => $this->integer()->unsigned()->notNull(),
            'updated_at'    => $this->integer()->unsigned()->notNull(),
        ], $tableOptions);
        
        $this->createIndex('key', '{{%settings}}', 'key', true);
        $this->createIndex('settings_category_id_i', '{{%settings}}', 'category_id');
        
        // Create 'settings_values' table
        $this->createTable('{{%settings_values}}', [
            'setting_id'    => $this->integer()->notNull(),
            'language'      => $this->string(10)->notNull(),
            'value'         => $this->string()->notNull(),
            'created_at'    => $this->integer()->unsigned()->notNull(),
            'updated_at'    => $this->integer()->unsigned()->notNull(),
        ], $tableOptions);
        
        $this->addPrimaryKey('setting_id_language', '{{%settings_values}}', ['setting_id', 'language']);
        $this->createIndex('settings_values_language_i', '{{%settings_values}}', 'language');
        $this->addForeignKey('FK_SETTINGS_VALUES_SETTING_ID', '{{%settings_values}}', 'setting_id', '{{%settings}}', 'id', 'CASCADE', 'RESTRICT');
        
        // Create 'settings_categories' table
        $this->createTable('{{%settings_categories}}', [
            'id'                    => $this->primaryKey(),
            'name'                  => $this->string()->notNull()
        ], $tableOptions);
        
        // Insert the default categories
        $this->insert('{{%settings_categories}}', ['name' => 'Systeem']);
        $this->insert('{{%settings_categories}}', ['name' => 'SEO']);
        $this->insert('{{%settings_categories}}', ['name' => 'Social']);
        $this->insert('{{%settings_categories}}', ['name' => 'Formulieren']);
    }

    public function safeDown()
    {
        $this->dropTable('settings_categories');
        $this->dropTable('settings_tables');
        $this->dropTable('settings');
    }
}

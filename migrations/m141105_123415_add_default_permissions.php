<?php

use yii\db\Schema;
use yii\db\Migration;

class m141105_123415_add_default_permissions extends Migration
{
    public function up()
    {
        // Create the auth items
        $this->insert('{{%auth_item}}', [
            'name'          => 'showSettingsModule',
            'type'          => 2,
            'description'   => 'Show Settings module icon in main-menu',
            'created_at'    => time(),
            'updated_at'    => time()
        ]);
        
        // Create the auth item relation
        $this->insert('{{%auth_item_child}}', [
            'parent'        => 'Superadmin',
            'child'         => 'showSettingsModule'
        ]);
    }

    public function down()
    {
        // Delete the auth item relation
        $this->delete('{{%auth_item_child}}', [
            'parent'        => 'Superadmin',
            'child'         => 'showSettingsModule'
        ]);
        
        // Delete the auth items
        $this->delete('{{%auth_item}}', [
            'name'          => 'showSettingsModule',
            'type'          => 2,
        ]);
    }
}

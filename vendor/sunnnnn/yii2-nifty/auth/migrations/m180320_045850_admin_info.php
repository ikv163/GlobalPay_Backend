<?php

use yii\db\Migration;

/**
 * Class m180320_045850_admin_info
 */
class m180320_045850_admin_info extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%admin_info}}', [
            'id' => $this->primaryKey(),
            'admin_id' => $this->integer()->notNull(),
            'name' => $this->string(128)->notNull(),
            'mobile' => $this->string(32)->notNull(),
            'email' => $this->string(128)->notNull(),
            'gender' => $this->smallInteger(1)->notNull(),
            'address' => $this->string(512)->notNull(),
            'add_time' => $this->integer()->notNull(),
            'edit_time' => $this->integer()->notNull(),
        ], $tableOptions);
        
    }

    public function down()
    {
        $this->dropTable('{{%admin_info}}');
    }
}

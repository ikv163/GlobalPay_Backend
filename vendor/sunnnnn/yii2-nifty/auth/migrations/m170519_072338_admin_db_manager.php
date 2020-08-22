<?php
use yii\db\Migration;
//php   yii migrate/create admin_db_manager
//php   yii migrate --migrationPath=@backend/modules/auth/migrations/
class m170519_072338_admin_db_manager extends Migration
{
    public function up(){
    	
    	$tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%admin}}', [
			'id' => $this->primaryKey(),
        	'username' => $this->string(32)->notNull(),
        	'password' => $this->string(128)->notNull(),
        	'role' => $this->integer()->notNull(),
            'photo' => $this->string(256)->notNull(),
            'auth_key' => $this->string(32)->notNull(),
        	'add_time' => $this->integer()->notNull(),
        	'edit_time' => $this->integer()->notNull(),
        	'login_time' => $this->integer()->notNull(),
        	'status' => $this->smallInteger(1)->notNull(),
		], $tableOptions);
        
        $this->insert('{{%admin}}', [
			'id' => 1,
			'username' => 'admin',
			'password' => '$2y$13$2RwWJxWbroUlV5BQbNKsju0igcvRFugucahp./OMyzP8XRqSQ5zCu', //123456
			'role' => 1,
            'photo' => '',
			'auth_key' => '',
			'add_time' => time(),
			'edit_time' => 0,
			'login_time' => 0,
			'status' => 0,
        ]);
    }

    public function down(){
        $this->dropTable('{{%admin}}');
    }
}

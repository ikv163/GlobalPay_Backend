<?php
use yii\db\Migration;
//php   yii migrate/create auth_db_manager
//php   yii migrate --migrationPath=@sunnnnn/admin/auth/migrations/
class m170519_062225_auth_db_manager extends Migration{
	
    public function up(){
    	
    	$tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%auth_route}}', [
			'id' => $this->primaryKey(),
        	'name' => $this->string(64)->notNull(),
        	'route' => $this->string(64)->notNull(),
        	'add_time' => $this->integer()->notNull(),
        	'edit_time' => $this->integer()->notNull(),
		], $tableOptions);
        
        $this->batchInsert('{{%auth_route}}',
        	['id', 'name', 'route', 'add_time', 'edit_time'], 
        	[
				[1, '所有权限', '/*', time(), 0],
				[2, '权限管理【所有权限】', '/auth/*', time(), 0],
				[3, '权限管理-路由【所有权限】', '/auth/route/*', time(), 0],
				[4, '权限管理-路由【首页展示】', '/auth/route/index', time(), 0],
				[5, '权限管理-路由【添加操作】', '/auth/route/add', time(), 0],
				[6, '权限管理-路由【编辑操作】', '/auth/route/edit', time(), 0],
				[7, '权限管理-路由【删除操作】', '/auth/route/delete', time(), 0],
				[8, '权限管理-角色【所有权限】', '/auth/role/*', time(), 0],
				[9, '权限管理-角色【首页展示】', '/auth/role/index', time(), 0],
				[10, '权限管理-角色【添加操作】', '/auth/role/add', time(), 0],
				[11, '权限管理-角色【编辑操作】', '/auth/role/edit', time(), 0],
				[12, '权限管理-角色【删除操作】', '/auth/role/delete', time(), 0],
				[13, '权限管理-菜单【所有权限】', '/auth/menu/*', time(), 0],
				[14, '权限管理-菜单【首页展示】', '/auth/menu/index', time(), 0],
				[15, '权限管理-菜单【添加操作】', '/auth/menu/add', time(), 0],
				[16, '权限管理-菜单【编辑操作】', '/auth/menu/edit', time(), 0],
				[17, '权限管理-菜单【删除操作】', '/auth/menu/delete', time(), 0],
				[18, '管理员【所有权限】', '/admin/*', time(), 0],
				[19, '管理员【首页展示】', '/admin/index', time(), 0],
				[20, '管理员【添加操作】', '/admin/add', time(), 0],
				[21, '管理员【编辑操作】', '/admin/edit', time(), 0],
				[22, '管理员【删除操作】', '/admin/delete', time(), 0],
				[23, '用户管理【所有权限】', '/user/*', time(), 0],
				[24, '用户管理【首页展示】', '/user/index', time(), 0],
				[25, '用户管理【添加操作】', '/user/add', time(), 0],
				[26, '用户管理【编辑操作】', '/user/edit', time(), 0],
				[27, '用户管理【个人资料】', '/user/profile', time(), 0],
				[28, '用户管理【修改头像操作】', '/user/edit-photo', time(), 0],
				[29, '用户管理【修改密码操作】', '/user/edit-password', time(), 0],
				[30, '用户管理【修改资料操作】', '/user/edit-profile', time(), 0],
				[31, '首页管理【所有权限】', '/site/*', time(), 0],
				[32, '首页管理【首页展示】', '/site/index', time(), 0],
				[33, '首页管理【退出登录】', '/site/logout', time(), 0],
        	]
		);
        
        $this->createTable('{{%auth_roles}}', [
			'id' => $this->primaryKey(),
        	'name' => $this->string(64)->notNull(),
        	'routes' => $this->string(1024)->notNull(),
        	'remark' => $this->string(1024)->notNull(),
        	'add_time' => $this->integer()->notNull(),
        	'edit_time' => $this->integer()->notNull(),
		], $tableOptions);
        
        $this->insert('{{%auth_roles}}', [
			'id' => 1,
			'name' => 'Super Admin',
			'routes' => '1',
			'remark' => 'All Permissions',
			'add_time' => time(),
			'edit_time' => 0,
        ]);
        
        $this->createTable('{{%auth_menu}}', [
			'id' => $this->primaryKey(),
        	'name' => $this->string(64)->notNull(),
        	'parent' => $this->integer()->notNull(),
        	'route' => $this->integer()->notNull(),
        	'order' => $this->integer()->notNull(),
        	'icon' => $this->string(64)->notNull(),
        	'add_time' => $this->integer()->notNull(),
        	'edit_time' => $this->integer()->notNull(),
		], $tableOptions);
        
        $this->batchInsert('{{%auth_menu}}', 
        	['id', 'name', 'parent', 'route', 'order', 'icon', 'add_time', 'edit_time'], 
        	[
        	    [1, 'home', 0, 32, 1, 'pli-home', time(), 0],
				[2, 'permissions', 0, 0, 1000, 'pli-key', time(), 0],
				[3, 'administrator', 2, 19, 1001, '', time(), 0],
				[4, 'menu', 2, 14, 1002, '', time(), 0],
				[5, 'role', 2, 9, 1003, '', time(), 0],
				[6, 'route', 2, 4, 1004, '', time(), 0],
        	]
		);
        
    }

    public function down(){
        $this->dropTable('{{%auth_test}}');
        $this->dropTable('{{%auth_roles}}');
        $this->dropTable('{{%auth_menu}}');
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}

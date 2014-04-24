<?php

namespace Fuel\Migrations;

class Create_users
{
	public function up()
	{
		\DBUtil::create_table('users', array(
//			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment'=>true),
			'email' => array('type' => 'varchar(255)'),
			'password' => array('type' => 'varchar(255)'),
			//			'created_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),
//			'updated_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),

		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('users');
	}
}
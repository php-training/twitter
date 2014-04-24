<?php

class Model_Login_Token extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'iduser',
		'token',
		'created_at',
		'due_date',
	);

	protected static $_observers = array(
			'Orm\Observer_CreatedAt' => array(
					'events' => array('before_insert'),
					'mysql_timestamp' => true,
			),
			
	);
	
	protected static $_table_name = 'login_token';

}

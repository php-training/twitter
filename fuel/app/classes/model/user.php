<?php

class Model_User extends Orm\Model
{
	protected static $_properties = array(
		'id',
		'email',
		'password',
/**
 * auto generated by oil
 */
		'created_at',
//		'updated_at',
/**
 * auto generated by oil
 */
	);
/**
 * auto generated by oil
 * @var unknown
 */

/*	protected static $_observers = array(
		'Orm\Observer_CreatedAt' => array(
			'events' => array('before_insert'),
			'mysql_timestamp' => true,
		),
		'Orm\Observer_UpdatedAt' => array(
			'events' => array('before_update'),
			'mysql_timestamp' => false,
		),
	);
*/
/**
 * auto generated by oil
 * @var unknown
 */
	protected static $_table_name = 'users';
	
	protected static $_primary_key = array('id');

}
?>
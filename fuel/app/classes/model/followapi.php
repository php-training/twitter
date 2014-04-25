<?php
namespace Model;
use \DB;
class Followapi extends \Model
{
	protected static $_properties = array(
		'id',
		'user_id_follow',
		'user_id_followed',
	);
	protected function insertFollow(){
		try{
			list($insert_id,$row_affected) = DB::insert('follow')->columns(array('user_id_follow','user_id_followed'))->values(array(1,2))->execute();
			echo $insert_id . "<br/>";
			echo $row_affected;
			exit;
		}catch (\Database_Exception $e) {
			return false;		  		  
		}
	}
}
<?php
namespace Model;
use \DB;

class Post extends \Model
{
	//check token to get userid
	public static function checkToken($token='')
	{
		//$sql = DB::select('*')-> from('login_token')-> where('token', $token)-> and_where('due_date', '>=', 'NOW()');
		$sql = DB::query("Select `iduser` from `login_token` where token = '" . $token . "' AND due_date >= NOW()");
		$result = $sql->execute()->current();
		
		return $result;
	}
	
	public static function insert($data)
	{
		try
		{
			$query = DB::insert('post')->columns(array('content', 'image_url', 'id_user', 'delete_status'));
			$query->values(array($data['content'], $data['image_url'], $data['id_user'], $data['delete_status']));
			$query->execute();
			
			return true;
		} catch(\Database_Exception $e) {
			return false;
		}	
	}

}
?>
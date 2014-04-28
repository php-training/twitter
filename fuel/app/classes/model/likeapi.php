<?php
namespace Model;
use \DB;

class Likeapi extends \Model
{

	//check token to get userid
	public static function checkToken($token='')
	{
		//$sql = DB::select('*')-> from('login_token')-> where('token', $token)-> and_where('due_date', '>=', 'NOW()');
		$sql = DB::query("Select `iduser` from `login_token` where token = '" . $token . "' AND due_date >= NOW()");
		$result = $sql->execute()->current();
		
		return $result;
	}
	
	//check post which exist or not
	public static function checkPost($idmessage='')
	{
		$sql = DB::select('id')-> from('post')-> where('id', $idmessage);
		$result = $sql->execute();
		
		return $result;
	}
	
	//check like record
	//if like -> not exist -> insert
	//else ->delete
	public static function checkLike($idmessage='', $iduser='')
	{
		$sql = DB::select('id')-> from('like')-> where('idmessage', $idmessage)-> and_where('iduser', $iduser);
		$result = $sql->execute();
		
		return $result;
	}
	
	//insert like
	public static function insertLike($idmessage='', $iduser='')
	{
		$query = DB::insert('like')->columns(array('iduser','idmessage'));
		$query->values(array($iduser,$idmessage));
		$query->execute();
		
		return true;
	}
	
	//delete like
	public static function deleteLike($idmessage='', $iduser='')
	{
		$query = DB::delete('like')->where('iduser', $iduser)->and_where('idmessage', $idmessage)->execute();
		
		return true;
	}

}
?>
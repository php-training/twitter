<?php
namespace Model;
use \DB;

class Likeapi extends \Model
{
	public static function checkToken($token='')
	{
		$sql = DB::select('*')-> from('login_token')-> where('token', $token)-> and_where('due_date', '<=', 'NOW()');
		$result = $sql->execute();
		
		return $result;
	}
	
	public static function checkPost($idmessage='')
	{
		$sql = DB::select('*')-> from('post')-> where('id', $idmessage);
		$result = $sql->execute();
		
		return $result;
	}
	
	public static function checkLike($idmessage='', $iduser='')
	{
		$sql = DB::select('*')-> from('like')-> where('idmessage', $idmessage)-> and_where('iduser', $iduser);
		$result = $sql->execute();
		
		return $result;
	}
	
	public static function insertLike($idmessage='', $iduser='')
	{
		$query = DB::insert('like')->columns(array('iduser','idmessage'));
		$query->values(array($iduser,$idmessage));
		$query->execute();
		
		return true;
	}
	
	public static function deleteLike($idmessage='', $iduser='')
	{
		$query = DB::delete('like')->where('iduser', $iduser)->and_where('idmessage', $idmessage)->execute();
		
		return true;
	}

}
?>
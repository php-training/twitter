<?php
namespace Model;
use \DB;

class Followapi extends \Model
{
	
	public static function searchToken($token){
		try{
			$query = DB::query("select iduser from login_token where due_date >= now() and token = '" . $token . "'", DB::SELECT);
			$result = $query->execute();
			$arrs = $result->as_array();			
			$rows = count($arrs);
			if($rows == 0){
				return 403;
			}else{
				return $arrs;
			}
		}catch (\Database_Exception $e) {
			return 500;		  		  
		}
	
	}
	public static function searchUser($iduser){
		try{
			$query = DB::query("select id from users where id =  " . $iduser,DB::SELECT);
			$result = $query->execute();
			$arrs = $result->as_array();
			$rows = count($arrs);
			if($rows == 0){
				return 402;
			}else{
				return $arrs;
			}
		}catch (\Database_Exception $e) {
			return 500;		  		  
		}
	
	}
	public static function isExistFollow($user_id_follow, $user_id_followed){
		try{
			$query = DB::query("select id, status from follow where user_id_follow =". $user_id_follow . " and user_id_followed = " . $user_id_followed,DB::SELECT);
			$result = $query->execute();
			$arrs = $result->as_array();
			$rows = count($arrs);
			if($rows == 0){
				return 402;
			}else{
				return $arrs;
			}
		}catch (\Database_Exception $e) {
			return 500;		  		  
		}
	}
	public static function insertFollow($user_id_follow, $user_id_followed, $status){
		try{
			$query = DB::query("insert into follow(user_id_follow, user_id_followed, status) values(" . $user_id_follow . ", " . $user_id_followed . ", " . $status . ")",DB::INSERT);			
			$result = $query->execute();
			return $result;
		}catch (\Database_Exception $e) {
			return 500;		  		  
		}
	}
	public static function updateFollow($user_id_follow, $user_id_followed, $currentStatus){
		try{
			if($currentStatus == 1){
				$status = 0;
			}else{
				$status = 1;
			}
			$query = DB::query("update follow set status = " . $status . " where user_id_follow = " . $user_id_follow . " and user_id_followed = " . $user_id_followed,DB::UPDATE);			
			$result = $query->execute();
			return $result;
		}catch (\Database_Exception $e) {
			return 500;		  		  
		}
	}
	public static function viewFollowing($user_id){
		try{
			$query = DB::query("select u.id, u.email, f.status from users u, follow f where u.id = f.user_id_follow and  f.user_id_followed =  " . $user_id, DB::SELECT);
			$result = $query->execute();
			$arrs = $result->as_array();					
			$rows = count($arrs);
			if($rows == 0){
				return 402;
			}else{
				return $arrs;
			}
		}catch (\Database_Exception $e) {
			return 500;		  		  
		}
	}
	public static function viewFollowed($user_id){
		try{
			$query = DB::query("select u.id, u.email, f.status from users u, follow f where u.id = f.user_id_followed and  f.user_id_follow =  " . $user_id, DB::SELECT);
			$result = $query->execute();
			$arrs = $result->as_array();
			
			$rows = count($arrs);			
			if($rows == 0){
				return 402;
			}else{
				return $arrs;
			}
		}catch (\Database_Exception $e) {
			return 500;		  		  
		}
	}
}
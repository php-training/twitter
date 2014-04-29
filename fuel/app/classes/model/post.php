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
        public static function checkPost($id_post='')
	{
		//$sql = DB::select('*')-> from('login_token')-> where('token', $token)-> and_where('due_date', '>=', 'NOW()');
		$sql = DB::query("Select `id_user` from `post` where id = '" . $id_post . "' AND delete_status= 0");
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
        public static function update($data){
            
            try
		{
			//$query = DB::update('post')->columns(array('delete_status'));
                        $query = DB::query("update post set delete_status = 1"  . " where id = " . $data['id']);
			$query->execute();
			
			return true;
		} catch(\Database_Exception $e) {
			return false;
		}	
        }
        public static function update2($data){
            
            try
		{
                        $sql = "update post set content = '" . $data['content'] . "', image_url = '" . $data['image_url'] . "' where id='" . $data['id'] . "'";
			$query = DB::query($sql);	
                        //$query = DB::query("update follow set status = " . $status . " where user_id_follow = " . $user_id_follow . " and user_id_followed = " . $user_id_followed,DB::UPDATE);	
                        $query->execute();
			return true;
		} catch(\Database_Exception $e) {
			return false;
		}	
        }
        public static function selectPost($id_user){
            
                $sql = DB::query("Select * from `post` where id_user = " . $id_user . " AND delete_status= 0");
              //  $sql = DB::query("Select `iduser` from `login_token` where token = '" . $token . "' AND due_date >= NOW()");
               // $query = DB::query($sql);
		$result = $sql->execute();
		
		return $result;
        }

}
?>
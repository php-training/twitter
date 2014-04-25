<?php

class Model_User extends Orm\Model
{
	public function checkToken($token='')
	{
		$sql = DB::select('*')-> from('login_token')-> where('token', $token)-> and_where('due_date', '<=', NOW());
		$result = $sql->execute();
		
		return $result;
	}

}
?>
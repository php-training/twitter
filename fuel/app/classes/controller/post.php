<?php
use \Model\Post;

class Controller_Post extends Controller_Rest
{

	//const variables
	const STATUS_SUCCESS = 200;
	const STATUS_VALID_ERR = 401;
	const STATUS_NOT_EXST_ACC = 402;
	const STATUS_NOT_EXST_POST = 403;
	const STATUS_DB_ERR = 500;
	
	//variables
	private $content = "";
	private $image_url = "";
	private $token = "";
	private $delete_status = "";
	
	public function action_index()
	{
        if(Input::method() == "POST")
		{
			//format config
			$this->format = 'xml';
			
			$this->content = Input::post('content');
			$this->image_url = Input::post('image_url');
			$this->token = Input::post('token');
			$this->delete_status = 0;
			
			//search token from table login_token
			if(!empty($this->token))
			{
				$user = Post::checkToken($this->token);

				//check token to get iduser
				if(count($user) > 0)
				{
					//upload image
					//Custom configuration for this upload
					$config = array(
						'path' => DOCROOT . 'assets\uploads',
						'randomize' => true,
						'ext_whitelist' => array('img', 'jpg', 'jpeg', 'gif', 'png'),
					);
					
					// process the uploaded files in $_FILES
					Upload::process($config);
					
					// if there are any valid files
					if (Upload::is_valid())
					{
						//save images base on the config 
						Upload::save();
						
						$value = Upload::get_files(0);
						$this->image_url = $value['saved_as'];
					}
					
					$data = array(
						'content' => $this->content,
						'image_url' => $this->image_url,
						'id_user' => $user['iduser'],
						'delete_status' => $this->delete_status
					);
					
					//insert post
					if(Post::insert($data))
					{
						return $this->response(
							array(
								'error' => array(
									'status' 	=> self::STATUS_SUCCESS,
									'message' 	=> '',
								)
							)
						);
					} else {
						return $this->response(
							array(
								'error' => array(
									'status' 	=> self::STATUS_DB_ERR,
									'message' 	=> 'Database Error',
								)
							)
						);
					}
					
				} else {
					return $this->response(
						array(
							'error' => array(
								'status' 	=> self::STATUS_NOT_EXST_ACC,
								'message' 	=> 'Please login before like this post',
							)
						)
					);
				}
			} else {
				return $this->response(
					array(
						'error' => array(
							'status' 	=> self::STATUS_NOT_EXST_ACC,
							'message' 	=> 'Please login before like this post',
						)
					)
				);
			}
		}
	}
    
	/* public function action_create()
	{
	
	}
        public function action_create(){
            
            $error_code = $this -> OK; 
            
            $val = Validation::forge('create_alidation');

            if (!$val->run()){
				$error_code = $this -> VALID_ERR; // invalid parameter
			}
                        
                        else{ //ok
				
				$content   = Input :: post('content');
				$token = Input :: post ('token');
                                
                                $allowedExts = array("gif", "jpeg", "jpg", "png");
                                $temp = explode(".", $_FILES["file"]["name"]);
                                $extension = end($temp);

                                if ((($_FILES["file"]["type"] == "image/gif")
                                || ($_FILES["file"]["type"] == "image/jpeg")
                                || ($_FILES["file"]["type"] == "image/jpg")
                                || ($_FILES["file"]["type"] == "image/pjpeg")
                                || ($_FILES["file"]["type"] == "image/x-png")
                                || ($_FILES["file"]["type"] == "image/png"))
                                && ($_FILES["file"]["size"] < 20000)
                                && in_array($extension, $allowedExts)) {
                                if ($_FILES["file"]["error"] > 0) {
                                  echo "Return Code: " . $_FILES["file"]["error"] . "<br>";
                                } else {
                                  echo "Upload: " . $_FILES["file"]["name"] . "<br>";
                                  echo "Type: " . $_FILES["file"]["type"] . "<br>";
                                  echo "Size: " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
                                  echo "Temp file: " . $_FILES["file"]["tmp_name"] . "<br>";
                                      if (file_exists("upload/" . $_FILES["file"]["name"])) {
                                        echo $_FILES["file"]["name"] . " already exists. ";
                                      } else {
                                              move_uploaded_file($_FILES["file"]["tmp_name"],
                                              "upload/" . $_FILES["file"]["name"]);
                                              echo "Stored in: " . "upload/" . $_FILES["file"]["name"];
                                              }
                                      }
                        } else {
                          echo "Invalid file";
                        }
                                //get the post variable first
                                try{
					//echo "def";
                                        $sql = DB::select('iduser')-> from('login_token')-> where('token', $token);
                                        $entry = $sql -> execute();
                                       // return $entry;
					
					if (!count($entry)>0) { //token is not existed
						$error_code = $this -> NOT_EXST_ACC;
					}
					else { 
                                            foreach ($entry as $user)
					
                                            {
								$newpost  =new Model_Post();
								$newpost -> content= $content;
								$newpost -> id_user =$user;
                                                                $newpost ->image_url= $_FILES["file"]["tmp_name"] . "upload/" . $_FILES["file"]["name"];
                                                                $newpost->delete_status=0;
								//insert into database
								$newpost -> save();
									
							}
				}
                                }
                                catch (Database_Exception $dbe){
				//	echo "hfsdh";
					$error_code = $this -> DB_ERR;
				}	
			}
			
			
			$result = array("error" => array("status" => $error_code,"message" => "")); //print result in XML format
			print_r(Format :: forge($result) -> to_xml());
			
		}
             

                public function action_delete(){
                        $error_code = $this -> OK;

			$val = Validation :: forge('delete_validation'); //validation			
			if (!$val -> run()){ //failed
				$error_code = $this -> VALID_ERR;
			}
			else{ // validation pass, go the next step
				$token = Input :: post ('token');
                                
				$id_post = Input :: post ('id_post');
				try{

					$sql = DB::select('iduser')-> from('login_token')-> where('token', $token);
                                        $entry = $sql -> execute();
                               
					if (!count($entry)>0) { //token is not existed
						$error_code = $this -> NOT_EXST_ACC;
                                                echo "van";
					}
					else { 

                                            foreach ($entry as $dbuser){ 
                                                    $sql = DB::select('*')-> from('post')-> where('id', $id_post)->and_where('id_user',$dbuser)->and_where('delete_status','0');
                                                    $post_result = $sql ->as_object('Model_Post') -> execute();
                                                    if(!count($post_result)>0){
                                                        $error_code=  $this->NOT_EXST_POST;
                                                        
                                                    } else {
                                                        foreach ($post_result as $post)
                                                        {
                                                            $post->delete_status=1;
                                                            $post->save();
                                                        }
                                                    }
                                                    
							
						}
					}
				}
				catch (Database_Exception $dbe){
					$error_code = $this -> DB_ERR;
				}
			}
			
			$result = array ("error" => array ("status" => $error_code, "message" => ""));
			print_r(Format :: forge ($result) -> to_xml()); //print the result in xml
                    
                }
                public function action_view(){
                        $error_code = $this -> OK;

			$val = Validation :: forge('view_validation'); //validation			
			if (!$val -> run()){ //failed
				$error_code = $this -> VALID_ERR;
			}
                        else{ // validation pass, go the next step
				$token = Input :: post ('token');
				
				try{

					$sql = DB::select('iduser')-> from('login_token')-> where('token', $token);
                                        $entry = $sql -> execute();
                                  
					if (!count($entry)>0) { //token is not existed
						$error_code = $this -> NOT_EXST_ACC;

					}
					else { 
					
						foreach ($entry as $dbuser){ //only one
                                                    $post_result= Model_Post::find('all', array (
											'where' => array (//search the database for token 
											array ('id_user', $dbuser),
											
											)
											));
                                          
                                                    if(!count($post_result)>0){
                                                        $error_code=  $this->NOT_EXST_POST;
                                                        
                                                    } else {
                                                     
                                                        echo "ji";
                                                    }
                                                    
							
						}
					}
				}
				catch (Database_Exception $dbe){
					$error_code = $this -> DB_ERR;
				}
			}
			
			$result = array ("error" => array ("status" => $error_code, "message" => "error"));
			print_r(Format :: forge ($result) -> to_xml()); //print the result in xml
                        
                }
                public function action_edit(){
                  
                        $error_code = $this -> OK;

			$val = Validation :: forge('update_validation'); //validation			
			if (!$val -> run()){ //failed
				$error_code = $this -> VALID_ERR;
			}
			else{ // validation pass, go the next step
				$token = Input :: post ('token');
                                $id_post = Input :: post ('id_post');
                                $content = Input :: post ('content');
                                
                                $allowedExts = array("gif", "jpeg", "jpg", "png");
                                $temp = explode(".", $_FILES["file"]["name"]);
                                $extension = end($temp);

                                if ((($_FILES["file"]["type"] == "image/gif")
                                || ($_FILES["file"]["type"] == "image/jpeg")
                                || ($_FILES["file"]["type"] == "image/jpg")
                                || ($_FILES["file"]["type"] == "image/pjpeg")
                                || ($_FILES["file"]["type"] == "image/x-png")
                                || ($_FILES["file"]["type"] == "image/png"))
                                && ($_FILES["file"]["size"] < 20000)
                                && in_array($extension, $allowedExts)) {
                                if ($_FILES["file"]["error"] > 0) {
                                  echo "Return Code: " . $_FILES["file"]["error"] . "<br>";
                                } else {
                                  echo "Upload: " . $_FILES["file"]["name"] . "<br>";
                                  echo "Type: " . $_FILES["file"]["type"] . "<br>";
                                  echo "Size: " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
                                  echo "Temp file: " . $_FILES["file"]["tmp_name"] . "<br>";
                                      if (file_exists("upload/" . $_FILES["file"]["name"])) {
                                        echo $_FILES["file"]["name"] . " already exists. ";
                                      } else {
                                              move_uploaded_file($_FILES["file"]["tmp_name"],
                                              "upload/" . $_FILES["file"]["name"]);
                                              echo "Stored in: " . "upload/" . $_FILES["file"]["name"];
                                              }
                                      }
                        } else {
                          echo "Invalid file";
                        }
                                    
				
				try{

					$sql = DB::select('iduser')-> from('login_token')-> where('token', $token);
                                        $entry = $sql -> execute();
					if (!count($entry)>0) { //token is not existed
						$error_code = $this -> NOT_EXST_ACC;
					}
					else { 
					
						foreach ($entry as $dbuser){ 
                                                    $sql = DB::select('*')-> from('post')-> where('id', $id_post)->and_where('id_user',$dbuser)->and_where('delete_status','0');
                                                    $post_result = $sql ->as_object('Model_Post') -> execute();
                                                    if(!count($post_result)>0){
                                                        $error_code=  $this->NOT_EXST_POST;
                                                        
                                                    } else {

                                                            foreach ($post_result as $post1)
                                                        {
                                                            $post1["content"]= $content;
                                                            $post1["image_url"]=$_FILES["file"]["tmp_name"] . "upload/" . $_FILES["file"]["name"];
                                                            $post1 ->save();

                                                        }
                                                    
							
                                                        }
                                                    }
                                        }
                                }
				
				catch (Database_Exception $dbe){
					$error_code = $this -> DB_ERR;
				}
			}
			
			$result = array ("error" => array ("status" => $error_code, "message" => ""));
			print_r(Format :: forge ($result) -> to_xml()); //print the result in xml
                }

 */



}

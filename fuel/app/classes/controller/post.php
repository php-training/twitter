<?php

use \Model\Post;

class Controller_Post extends Controller_Rest {

    const OK = 200;
    const STATUS_VALID_ERR = 401;
    const STATUS_NOT_EXST_ACC = 402;
    const STATUS_NOT_EXST_POST = 403;
    const STATUS_DB_ERR = 500;

    private $content = "";
    private $image_url = "";
    private $token = "";
    private $delete_status = "";
    private $id="";

    public function action_create() {
        if (Input::method() == "POST") {
            $this->format = 'xml';

            $this->content = Input::post('content');
            $this->image_url = Input::post('image_url');
            $this->token = Input::post('token');
            $this->delete_status = 0;

            //search token from table login_token
            if (!empty($this->token)) {
                $user = Post::checkToken($this->token);

                //get iduser
                if (count($user) > 0) {
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
                    if (Upload::is_valid()) {
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
                    if (Post::insert($data)) {
                        return $this->response(
                                        array(
                                            'error' => array(
                                                'status' => self::OK,
                                                'message' => '',
                                            )
                                        )
                        );
                    } else {
                        return $this->response(
                                        array(
                                            'error' => array(
                                                'status' => self::STATUS_DB_ERR,
                                                'message' => 'Database Error',
                                            )
                                        )
                        );
                    }
                } else {
                    return $this->response(
                                    array(
                                        'error' => array(
                                            'status' => self::STATUS_NOT_EXST_ACC,
                                            'message' => 'Please login before post',
                                        )
                                    )
                    );
                }
            } else {
                return $this->response(
                                array(
                                    'error' => array(
                                        'status' => self::STATUS_NOT_EXST_ACC,
                                        'message' => 'Please login before post ',
                                    )
                                )
                );
            }
        }
    }


    public function action_delete(){
        
        if (Input::method() == "POST") {
           
            $this->format = 'xml';

            $this->id = Input::post('id');
            $this->token = Input::post('token');
            
            if (!empty($this->token)) {
                $user = Post::checkToken($this->token);
                $this->delete_status = 1;
                //check token to get iduser
                if (count($user) > 0) {
              
                    $post_user=  Post::checkPost($this->id);
           
                    if($post_user['id_user']==$user['iduser'])
                    {
                        $data = array(
                           'id' => $this->id
                        );

                    if (Post::update($data)) {
                        return $this->response(
                                        array(
                                            'error' => array(
                                                'status' => self::OK,
                                                'message' => '',
                                            )
                                        )
                        );
                    } 
//               
                    }
                    
           
                } else {
                    return $this->response(
                                    array(
                                        'error' => array(
                                            'status' => self::STATUS_NOT_EXST_POST,
                                            'message' => 'Please login before post',
                                        )
                                    )
                    );
                }
            } else {
                return $this->response(
                                array(
                                    'error' => array(
                                        'status' => self::STATUS_NOT_EXST_ACC,
                                        'message' => 'Please login before post ',
                                    )
                                )
                );
            }
        }
            
    }
//    public function action_view(){
//        if (Input::method() == "POST") {
//            //format config
//            $this->format = 'xml';
//
//            $this->token = Input::post('token');
// 
//            //search token from table login_token
//            if (!empty($this->token)) {
//                $user = Post::checkToken($this->token);
//
//                //check token to get iduser
//                if (count($user) > 0) {
//                    //insert post
//                    if (Post::selectPost($user)) {
//                        return $this->response(
//                                        array(
//                                            'error' => array(
//                                                'status' => self::OK,
//                                                'message' => '',
//                                            )
//                                        )
//                        );
//                    } else {
//                        return $this->response(
//                                        array(
//                                            'error' => array(
//                                                'status' => self::STATUS_DB_ERR,
//                                                'message' => 'Database Error',
//                                            )
//                                        )
//                        );
//                    }
//                } else {
//                    return $this->response(
//                                    array(
//                                        'error' => array(
//                                            'status' => self::STATUS_NOT_EXST_ACC,
//                                            'message' => 'Please login before post',
//                                        )
//                                    )
//                    );
//                }
//            } else {
//                return $this->response(
//                                array(
//                                    'error' => array(
//                                        'status' => self::STATUS_NOT_EXST_ACC,
//                                        'message' => 'Please login before post ',
//                                    )
//                                )
//                );
//            }
//        }
//    }
    public function action_view(){
        if (Input::method() == "POST") {
            //format config
            $this->format = 'xml';

            $this->token = Input::post('token');
 
            //search token from table login_token
            if (!empty($this->token)) {
                $user = Post::checkToken($this->token);
              //  print_r($user['iduser']);
           //     check token to get iduser
                if (count($user) > 0) {
                    //insert post
                    if (Post::selectPost($user['iduser'])) {
                        $data=Post::selectPost($user['iduser']);
                       // $count=  count($data);
                        //print_r($count);
//                        foreach($data as $key => $value ){
//							if($value['delete_status'] == 1)
//								$data[$key]['delete_status'] = 'da xoa';
//							else 
//								$data[$key]['delete_status'] = 'chua xoa';
//						}
                        return $this->response(
                                        array(
                                            'error' => array(
                                                'status' => self::OK,
                                                'message' => '',
                                               
                                             ),'posts' => array(
                                                 $data,
                                                 
                                         
                                            ),
                                        )
                            
                        );
//                        foreach ($data as $d) {
//                            print_r($d);
//                            
//                        }
                    } else {
                        return $this->response(
                                        array(
                                            'error' => array(
                                                'status' => self::STATUS_DB_ERR,
                                                'message' => 'Database Error',
                                            )
                                        )
                        );
                    }
                } else {
                    return $this->response(
                                    array(
                                        'error' => array(
                                            'status' => self::STATUS_NOT_EXST_ACC,
                                            'message' => 'Please login before post',
                                        )
                                    )
                    );
                }
            } else {
                return $this->response(
                                array(
                                    'error' => array(
                                        'status' => self::STATUS_NOT_EXST_ACC,
                                        'message' => 'Please login before post ',
                                    )
                                )
                );
            }
        }
    }


//    public function action_view() {
//        $error_code = $this->OK;
//
//        $val = Validation :: forge('view_validation'); //validation			
//        if (!$val->run()) { //failed
//            $error_code = $this->STATUS_VALID_ERR;
//        } else { // validation pass, go the next step
//            $token = Input :: post('token');
//
//            try {
//
//                $sql = DB::select('iduser')->from('login_token')->where('token', $token);
//                $entry = $sql->execute();
//
//                if (!count($entry) > 0) { //token is not existed
//                    $error_code = $this->STATUS_NOT_EXST_ACC;
//                } else {
//
//                    foreach ($entry as $dbuser) { //only one
//                        $post_result = Model_Post::find('all', array(
//                                    'where' => array(//search the database for token 
//                                        array('id_user', $dbuser),
//                                    )
//                        ));
//
//                        if (!count($post_result) > 0) {
//                            $error_code = $this->STATUS_NOT_EXST_POST;
//                        } else {
//
//                            echo "ji";
//                        }
//                    }
//                }
//            } catch (Database_Exception $dbe) {
//                $error_code = $this->STATUS_DB_ERR;
//            }
//        }
//
//        $result = array("error" => array("status" => $error_code, "message" => "error"));
//        print_r(Format :: forge($result)->to_xml()); //print the result in xml
//    }


    public function action_edit() {

         if (Input::method() == "POST") {
            //format config
            $this->format = 'xml';

            $this->content = Input::post('content');
            $this->image_url = Input::post('image_url');
            $this->token = Input::post('token');
            $this->id = Input::post('id');
            $this->delete_status = 0;

            //search token from table login_token
            if (!empty($this->token)) {
                $user = Post::checkToken($this->token);

                //check token to get iduser
                if (count($user) > 0) {
                     $post_user=  Post::checkPost($this->id);
                    print_r($post_user);
                    print_r($user);
                    if($post_user['id_user']==$user['iduser'])
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
                    if (Upload::is_valid()) {
                        //save images base on the config 
                        Upload::save();

                        $value = Upload::get_files(0);
                        $this->image_url = $value['saved_as'];
                        print_r($value);
                    }
                   

                    $data = array(
                        'content' => $this->content,
                        'image_url' => $this->image_url,
                        'id' => $this->id,
                        );
                        print_r($data);
               
                    if (Post::update2($data)) {
                        return $this->response(
                                        array(
                                            'error' => array(
                                                'status' => self::OK,
                                                'message' => '',
                                            )
                                        )
                        );
                    } else {
                        return $this->response(
                                        array(
                                            'error' => array(
                                                'status' => self::STATUS_DB_ERR,
                                                'message' => 'Database Error',
                                            )
                                        )
                        );
                    }
                } else {
                    return $this->response(
                                    array(
                                        'error' => array(
                                            'status' => self::STATUS_NOT_EXST_ACC,
                                            'message' => 'Please login before post',
                                        )
                                    )
                    );
                }
            } else {
                return $this->response(
                                array(
                                    'error' => array(
                                        'status' => self::STATUS_NOT_EXST_ACC,
                                        'message' => 'Please login before post ',
                                    )
                                )
                );
            }
        }
    }
    }

}

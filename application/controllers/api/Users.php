<?php
//debemos colocar esta línea para extender de REST_Controller
require(APPPATH.'/libraries/REST_Controller.php');
header('Access-Control-Allow-Origin: *');
class users extends REST_Controller
{
    //con esto limitamos las consultas y los permisos a la api
    protected $methods = array(
        'users_get' => array('level' => 0),//devuelve todos los usuarios
        'users_backend_get' => array('level' => 0), //devuelve todos los usuarios backend
        'users_app_get' => array('level' => 0), //devuelve todos los usuarios app
        'user_id_get' => array('level' => 0),//devuelve usuario por id
        'user_post' => array('level' => 0),// usada para el login, recibe ITA y Password
        'new_user_post' => array('level' => 0),//crea un usuario nuevo
        'new_user_app_post' => array('level' => 0), //crea un usuario de app nuevo 
        'user_ita_get' => array('level' => 0), //devuelve el usuario por ITA
        'forget_post' => array('level' => 0), // para restableser el password
        'positions_get' => array('level' => 0), //devuelve posiciones 
        'questions_get' => array('level' => 0), //devuelve las preguntas de la tabla question
        'rols_get' => array('level' => 0), //devuelve los roles de la tabla rols
        'user_status_get' => array('level' => 0), //deveuelve el estado de del usuario 0 inactivo, 1 activo
        'update_status_post' => array('level' => 0), //cambia el estado del usuario
        'update_user_post' => array('level' => 0), //actualiza usuario backend
        'update_user_app_post' => array('level' => 0),//actualiza usuario app
        'change_password_post' => array('level' => 0), //cambia el password del usuario
    );
   
    //obtener user por id
    //users/user_id/id/id_user/X-API-KEY/miapikey
    public function user_id_get()
    {
        if(!$this->get("id")){
            $this->response(NULL, 400);
        }
        $this->load->model("users_model");
        
        $users = $this->users_model->user_id($this->get("id_user"));
        if($users){
            $this->response($users, REST_Controller::HTTP_OK);
        }else{
            $this->response(array(
                "status" => FALSE,
                "message" => "Usuario no encontrado..."
            ),REST_Controller::HTTP_NOT_FOUND);
        }
    }

    //obtener user por id
    //users/user_id/id/id_user/X-API-KEY/miapikey
    public function user_ita_get()
    {
        if(!$this->get("ita")){
            $this->response(NULL, 400);
        }
        $this->load->model("users_model");
        
        $users = $this->users_model->user_ita($this->get("ita"));
        if($users){
            $this->response($users, REST_Controller::HTTP_OK);
        }else{
            $this->response(array(
                "status" => FALSE,
                "message" => "Usuario no encontrado..."
            ),REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function change_password_post() {
    //$this->response( $this->project_model->get_all ());
    //$username = $this->get('username');
    $this->load->model("users_model");
    $user = json_decode($this->post("user"));
    //$password = $this->get("password");
    //$clave = rand(123456, 999999);
   
                
    $temp = $this->users_model->set_password($user->ita, $user->password);
    $user2 = $this->users_model->user_ita($user->ita);
    //$this->set_response(array('message' => 'user'.$username), REST_Controller::HTTP_NOT_FOUND);
    
    if (!empty($temp)){
        //enviar email
        //$this->send_email_pass_temp($user, $pass);

        $this->response(array(
                'status' => TRUE,
                'message' => 'Password cambiado...',
                'password_new' => $user->password, 
                'user' => $user2
        ), REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code			
    }else{				
    $this->response(array(
                'status' => FALSE,
                'message' => 'Usuario no encontrado'
        ), REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code	
    }	
    }

    //obtener 
    //users/users/X-API-KEY/miapikey
    public function users_get()
    {
        $this->load->model("users_model");
        $users = $this->users_model->users();
        if($users){
            $this->response($users, 200);
        }else{
            $this->response(NULL, 400);
        }
    }

    public function users_backend_get()
    {
        $this->load->model("users_model");
        $users = $this->users_model->users_backend();
        if($users){
            $this->response($users, 200);
        }else{
            $this->response(NULL, 400);
        }
    }

     public function users_app_get()
    {
        $this->load->model("users_model");
        $users = $this->users_model->users_app();
        if($users){
            $this->response($users, 200);
        }else{
            $this->response(NULL, 400);
        }
    }


    public function forget_2_post() {
    //$this->response( $this->project_model->get_all ());
    //$username = $this->get('username');
    $this->load->model("users_model");
    $user = json_decode($this->post("user"));
    //$password = $this->get("password");
    //$clave = rand(123456, 999999);
    //$pass = substr(md5(time()), 0, 6);
                
    //$temp = $this->users_model->set_password_temp($user, $pass);
    $check = $this->users_model->check_user_question($user->email, $user->ita, $user->id_question, $user->answer);
    $user = $this->users_model->user_ita($user->ita);
    //$this->set_response(array('message' => 'user'.$username), REST_Controller::HTTP_NOT_FOUND);
    
    if ( $check === true ){
        //enviar email
        //$this->send_email_pass_temp($user, $pass);

        $this->response(array(
                'status' => TRUE,
                'message' => 'Datos correctos',
                //'password_temp' => $pass, 
                'user' => $user
        ), REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code			
    }else{				
    $this->response(array(
                'status' => FALSE,
                'message' => 'Verifique los datos'
        ), REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code	
    }	
    }

    public function forget_post() {
    //$this->response( $this->project_model->get_all ());
    //$username = $this->get('username');
    $this->load->model("users_model");
    $user = json_decode($this->post("user"));
    //$password = $this->get("password");
    //$clave = rand(123456, 999999);
    $pass = substr(md5(time()), 0, 6);
                
    $temp = $this->users_model->set_password_temp($user, $pass);
    $user = $this->users_model->user_ita($user->ita);
    //$this->set_response(array('message' => 'user'.$username), REST_Controller::HTTP_NOT_FOUND);
    
    if (!empty($temp)){
        //enviar email
        //$this->send_email_pass_temp($user, $pass);

        $this->response(array(
                'status' => TRUE,
                'message' => 'Password cambiado...',
                'password_temp' => $pass, 
                'user' => $user
        ), REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code			
    }else{				
    $this->response(array(
                'status' => FALSE,
                'message' => 'Usuario no encontrado'
        ), REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code	
    }	
    }

    private function send_email_pass_temp($user, $pass){
		
        $this->load->library('email');
		$this->email->set_newline("\r\n");	

		$subject = 'Cambio de Password en '.$this->config->item('APP_NAME').'';
		$body = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
				<html xmlns="http://www.w3.org/1999/xhtml">
				<head>
			    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			    <title>'.htmlspecialchars($subject, ENT_QUOTES, $this->email->charset).'</title>
			    <style type="text/css">
							body{
			    		 	font-family: Arial, Verdana, Helvetica, sans-serif;
            				font-size: 14px;
							}
			    </style>
				</head>
    			<body>
			     <p>Hola <strong>' . ucwords(strtolower($user['name'])) . '</strong>, ' .
	     			'<br><br>'.
	     			' Su password temporal es : '.$pass.'<br><br>' .
	     			''.
	     			'<br><br>'.	     			
					  ' </strong><br><br>Graciar por su interes.<br><br>'.
					  '<a href=http://'.$this->config->item('APP_WEB').' >'.$this->config->item('APP_WEB').'</a></p>
    
				</body>
				</html>
						';
		 
		//echo $body;
		try {
	
		$result = $this->email
		->from($this->config->item('APP_EMAIL'))
		->reply_to($this->config->item('APP_EMAIL'))    // Optional, an account where a human being reads.
		->to($user['email'])
		->subject($subject)
		->message($body)
		->send();
		
		if($result)
		$this->response ( array (
				'status' => true,
				'message' => 'Su password temporal fue enviado a su email...' 
		), REST_Controller::HTTP_OK );
		
		//var_dump($result);
		//echo '<br />';
		//echo $this->email->print_debugger();	
		//print_r($result);
		$this->response ( array (
				'status' => false,
				'message' => 'Error enviando email...'
		), REST_Controller::HTTP_NOT_FOUND );
			
		}catch (phpmailerException $e) {
		  echo $e->errorMessage(); //Pretty error messages from PHPMailer
		  $this->response ( array (
		  		'status' => false,
		  		'message' => 'Error: '.$e->errorMessage()
		  ), REST_Controller::HTTP_NOT_FOUND );
		} catch (Exception $e) {
		  echo $e->getMessage(); //Boring error messages from anything else!
			$this->response ( array (
					'status' => false,
					'message' => 'Error: '.$e->getMessage()
			), REST_Controller::HTTP_NOT_FOUND );
			
		}
		/*
			if(!$result) {
			//return false;
			$this->response ( array (
					'status' => false,
					'message' => 'Error.'
			), REST_Controller::HTTP_NOT_FOUND );
		}else{
			//return true;
			$this->response ( array (
					'status' => true,
					'message' => 'Email enviado con exito...'
			), REST_Controller::HTTP_OK );
		}
		*/
	}


    public function users_rols_get()
    {
        $this->load->model("users_model");
        $users = $this->users_model->users_rols();
        if($users){
            $this->response($users, 200);
        }else{
            $this->response(NULL, 400);
        }
    }

    public function users_positions_get()
    {
        $this->load->model("users_model");
        $users = $this->users_model->users_positions();
        if($users){
            $this->response($users, 200);
        }else{
            $this->response(NULL, 400);
        }
    }

     public function users_questions_get()
    {
        $this->load->model("users_model");
        $users = $this->users_model->users_questions();
        if($users){
            $this->response($users, 200);
        }else{
            $this->response(NULL, 400);
        }
    }

    public function user_status_get()
    {
        $this->load->model("users_model");
        $user = $this->users_model->user_status($this->get("ita"));
        if($user){
            $this->response($user, REST_Controller::HTTP_OK); 
        }else{
            $this->response(array(
						'status' => FALSE,
						'message' => 'Error consultando estado.'
				), REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function user_post()
    {   

        if($this->post("user")){
            $this->load->model("users_model");
            $user = $this->users_model->user(json_decode($this->post("user")));
            if (empty($user)){
			$this->response(array(
					'status' => FALSE,
					'message' => 'Usuario o password incorrectos'
			), REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code			
		}
		if ( $user->status == 0 ){
				$this->response(array(
						'status' => FALSE,
						'message' => 'Cuenta no activa.'
				), REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
		}	
        }
        $this->users_model->set_last_login(json_decode($this->post("user")));
		$this->response($user, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code				
    }
    //crear un nueva pica
    //users/nuevo/X-API-KEY/miapikey
    public function new_user_post()
    {   

        if($this->post("user")){
            $this->load->model("users_model");
            $new = $this->users_model->new_user(json_decode($this->post("user")));
            if($new === false){
                $this->response(array(
						'status' => FALSE,
						'message' => 'Ya existe el ITA o Email'
				), REST_Controller::HTTP_NOT_FOUND);
            }else{
                $this->response(array(
						'status' => TRUE,
						'message' => 'Usuario creado'
				), REST_Controller::HTTP_OK);
            }
        }
    }


    public function new_user_app_post()
    {   

        if($this->post("user")){
            $this->load->model("users_model");
            $new = $this->users_model->new_user_app(json_decode($this->post("user")), json_decode($this->post("sponsor")), json_decode($this->post("platinum")));
            if($new === false){
                $this->response(array(
						'status' => FALSE,
						'message' => 'Ya existe el ITA o Email'
				), REST_Controller::HTTP_NOT_FOUND);
            }else{
                $this->response(array(
						'status' => TRUE,
						'message' => 'Usuario creado'
				), REST_Controller::HTTP_OK);
            }
        }
    }

    //actualizar pica
    //users/actualizar/X-API-KEY/miapikey
    public function update_user_post()
    {
        $this->load->model("users_model");
        $result = $this->users_model->update_user(json_decode($this->post("user")));

        if($result === false){
            $this->response(array(
                'status' => FALSE, 
                "message" => "Email ya existe en otro usuario"
            ), REST_Controller::HTTP_NOT_FOUND);
        }else{
            $this->response(array(
                'status' => TRUE, 
                "message" => "Usuario actualizado"
            ), REST_Controller::HTTP_OK);
        }
    }

        public function update_user_app_post()
    {
        $this->load->model("users_model");
        $result = $this->users_model->update_user_app(json_decode($this->post("user")), json_decode($this->post("sponsor")), json_decode($this->post("platinum")));

        if($result === false){
            $this->response(array(
                'status' => FALSE, 
                "message" => "Email ya existe en otro usuario"
            ), REST_Controller::HTTP_NOT_FOUND);
        }else{
            $this->response(array(
                'status' => TRUE, 
                "message" => "Usuario actualizado"
            ), REST_Controller::HTTP_OK);
        }
    }

    public function update_status_post()
    {
        $this->load->model("users_model");
        $result = $this->users_model->update_status(json_decode($this->post("user")));

        if($result === false){
            $this->response(array('status' => FALSE, "message" => "Error Actualizando Estado"));
        }else{
            $this->response(array('status' => FALSE, "message" => "Estado cambiado"));
        }
    }

}
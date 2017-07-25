<?php

class Users_model extends CI_Model
{
    public function user_id($id_user){
        $this->db->select("*");
        //$this->db->join('bancos', 'bancos.id_user=users.id_user');
        $query = $this->db->get_where("users", array("users.id_user" => $id_user));
        if($query->num_rows() == 1){
            return $query->row();
        }
    }

    public function user($user){
        $this->db->select("*");
        //$this->db->join('bancos', 'bancos.id_user=users.id_user');
        $query = $this->db->get_where("users", array("users.ita" => $user->username, "users.password"=>md5($user->password)));
        if($query->num_rows() == 1){
            return $query->row();
        }
    }

    public function users_rols()
    {
        //$this->db->select('rols.id_rol, users.id_user, users.name');
        //$this->db->join('rols', 'rols.id_rol=users.id_user');
        $this->db->select('*');
        $query = $this->db->get("rols");
       if($query->num_rows() > 0){
            return $query->result();
        }
    }

    public function user_status($ita){
        $this->db->select("users.id_user, users.ita, users.name, users.status ");
        //$this->db->join('bancos', 'bancos.id_user=users.id_user');
        $query = $this->db->get_where("users", "users.ita=".$ita);
        if($query->num_rows() == 1){
            return $query->row();
        }
    }

     public function users_positions()
    {
        //$this->db->select('rols.id_rol, users.id_user, users.name');
        //$this->db->join('rols', 'rols.id_rol=users.id_user');
        $this->db->select('*');
        $query = $this->db->get("positions");
       if($query->num_rows() > 0){
            return $query->result();
        }
    }

    public function users_questions()
    {
        //$this->db->select('rols.id_rol, users.id_user, users.name');
        //$this->db->join('rols', 'rols.id_rol=users.id_user');
        $this->db->select('*');
        $query = $this->db->get("questions");
       if($query->num_rows() > 0){
            return $query->result();
        }
    }

    function get_enum_values( $table, $field )
    {
        $type = $this->db->query( "SHOW COLUMNS FROM {$table} WHERE Field = '{$field}'" )->row( 0 )->Type;
        preg_match("/^enum\(\'(.*)\'\)$/", $type, $matches);
        $enum = explode("','", $matches[1]);
        return $enum;
    }

    public function new_user($user){
   //$this->db->select("*");
     $data = array(
            //"id_user" => $user->id_user,
            "name" => $user->name,
            "last" => $user->last,
            "ita" => $user->ita,
            "id_rol" => $user->id_rol,
            "id_position" => $user->id_position,
            "address" => $user->address,
            "phone" => $user->phone,
            "status" => 1, //activo
            "email" => $user->email,
            "password" => md5($user->password), 
            "date_create" => date('Y-m-d H:i:s')
        );
    
    //$query = $this->db->or_where("users", array("ita" => $user->ita, "email" => $user->email));
    $query = $this->db->where('ita', $user->ita)->or_where('email', $user->email)->get('users');

        if($query->num_rows() == 0){
            $this->db->insert("users", $data);
            return true;
        }else{
            return false;
        }
    }

    public function new_user_app($user, $sponsor, $platinum){
   //$this->db->select("*");
     $data = array(
            //"id_user" => $user->id_user,
            "name" => $user->name,
            "last" => $user->last,
            "ita" => $user->ita,
            "ita_sponsor" => $sponsor->ita,
            "ita_platinum" => $platinum->ita,
            "id_rol" =>  $user->id_rol,
            "id_position" =>  $user->id_position,
            "address" => $user->address,
            "phone" => $user->phone,
            "status" => 0, //inactivo
            "email" => $user->email,
            "password" => md5($user->password), 
            "date_create" => date('Y-m-d H:i:s')
        );
    
    //$query = $this->db->or_where("users", array("ita" => $user->ita, "email" => $user->email));
    $query = $this->db->where('ita', $user->ita)->or_where('email', $user->email)->get('users');

        if($query->num_rows() == 0){
            $this->db->insert("users", $data);
            return true;
        }else{
            return false;
        }
    }

   public function update_user_app($user, $sponsor, $platinum){
        if(empty($user->password))
        $data = array(
            //"id_user" => $user->id_user,
            "name" => $user->name,
            "last" => $user->last,
            "ita" => $user->ita,
            "ita_sponsor" => $sponsor->ita,
            "ita_platinum" => $platinum->ita,
            "id_rol" => $user->id_rol,
            "id_position" => $user->id_position,
            //"status" => 1, //activo
            "email" => $user->email,
            "phone" => $user->phone,
            "address" => $user->address,
            //"password" => md5($user->password), 
            "date_update" => date('Y-m-d H:i:s')
        );
        else
        $data = array(
            //"id_user" => $user->id_user,
            "name" => $user->name,
            "last" => $user->last,
            "ita" => $user->ita,
            "ita_sponsor" => $sponsor->ita,
            "ita_platinum" => $platinum->ita,
            "id_rol" => $user->id_rol,
            "id_position" => $user->id_position,
            //"status" => 1, //activo
            "email" => $user->email,
            "phone" => $user->phone,
            "address" => $user->address,
            "password" => md5($user->password), 
            "date_update" => date('Y-m-d H:i:s')
        );
        //$query = $this->db->update('users', $data, array('id_user' => $user->id_user));
        //$query = $this->db->where('email', $user->email)->get('users');

        if($this->check_email($user->email, $user->ita)){
            return false;
        }else{
            if($this->db->update('users', $data, array('ita' => $user->ita)))
            return true;
            else
            return false;
        }
       
    }

    public function update_user($user){
        if(empty($user->password))
        $data = array(
            //"id_user" => $user->id_user,
            "name" => $user->name,
            "last" => $user->last,
            "ita" => $user->ita,
            "id_rol" => $user->id_rol,
            "id_position" => $user->id_position,
            //"status" => 1, //activo
            "email" => $user->email,
            "phone" => $user->phone,
            "address" => $user->address,
            //"password" => md5($user->password), 
            "date_update" => date('Y-m-d H:i:s')
        );
        else
        $data = array(
            //"id_user" => $user->id_user,
            "name" => $user->name,
            "last" => $user->last,
            "ita" => $user->ita,
            "id_rol" => $user->id_rol,
            "id_position" => $user->id_position,
            //"status" => 1, //activo
            "email" => $user->email,
            "phone" => $user->phone,
            "address" => $user->address,
            "password" => md5($user->password), 
            "date_update" => date('Y-m-d H:i:s')
        );
        //$query = $this->db->update('users', $data, array('id_user' => $user->id_user));
        //$query = $this->db->where('email', $user->email)->get('users');

        if($this->check_email($user->email, $user->ita)){
            return false;
        }else{
            if($this->db->update('users', $data, array('ita' => $user->ita)))
            return true;
            else
            return false;
        }
       
    }

    public function check_email($email, $ita)
	{
			$id = $this->db->query(
					"select email from users where email = '$email' "
			);

		if($id->num_rows()>=2)
			return true;
		else
			return false;
		 
	}	
	

    public function check_user_question($email, $ita, $id_question, $answer)
	{
			$id = $this->db->query(
					"select email, ita, id_question, answer from users where email = '$email' and ita ='$ita' and id_question ='$id_question' and answer ='$answer'"
			);

		if($id->num_rows() == 1)
			return true;
		else
			return false;
		 
	}	

    public function update_status($user){
        if($user->status == 1)
        $data = array(
            "status" => 0, //inactivo
            "date_update" => date('Y-m-d H:i:s')
        );
        else
        $data = array(
            "status" => 1, //activo
            "date_update" => date('Y-m-d H:i:s')
        );
        $query = $this->db->update('users', $data, array('id_user' => $user->id_user));
        if($query){
            return true;
        }else{
            return false;
        }
       
    }

    public function users()
    {
        $this->db->select('users.*, rols.*, positions.*');
        $this->db->join('rols', 'rols.id_rol=users.id_rol');
        $this->db->join('positions', 'positions.id_position=users.id_position');
        $query = $this->db->get("users");
       if($query->num_rows() > 0){
            return $query->result();
        }
    }

    public function users_backend()
    {
        $this->db->select('users.*, rols.*, positions.*');
        $this->db->join('rols', 'rols.id_rol=users.id_rol');
        $this->db->join('positions', 'positions.id_position=users.id_position');
        //$query = $this->db->get("users");
        $query = $this->db->get_where("users", "users.id_rol<'4'");
       if($query->num_rows() > 0){
            return $query->result();
        }
    }

    public function users_app()
    {
        $this->db->select('users.*, rols.*, positions.*');
        //$this->db->join('users as user_master', 'users_master.ita=users.ita_master');
        $this->db->join('rols', 'rols.id_rol=users.id_rol');
        $this->db->join('positions', 'positions.id_position=users.id_position');
        //$query = $this->db->get("users");
        $query = $this->db->get_where("users", "users.id_rol = '4'");
       if($query->num_rows() > 0){
            return $query->result();
        }
    }
    public function user_ita($ita)
    {
        $this->db->select('users.*, rols.*, positions.*');
        $this->db->join('rols', 'rols.id_rol=users.id_rol');
        $this->db->join('positions', 'positions.id_position=users.id_position');
        //print_r($ita);
        //$query = $this->db->get("users");
        $query = $this->db->get_where("users", "users.ita='$ita'");
       if($query->num_rows() == 1){
            return $query->row();
        }
    }

     public function set_last_login($user)
    {
    	$this->db->query(
    			"update users set last_login = now() " .
    			"where users.ita = '$user->username'  "
    	);
    }

    public function set_password($ita, $pass)
    {
        //$ita = $user->ita;
    	//print_r($ita);
    	$id = $this->db->query(
    			"update users set password =  md5('$pass') " .
    			"where users.ita = '$ita'"
    	);
    	
    	$afftectedRows = $this->db->affected_rows();
		
		return $afftectedRows;
    }

     public function set_password_temp($user, $pass)
    {
        $ita = $user->ita;
        $email = $user->email;
    	
    	$id = $this->db->query(
    			"update users set password =  md5('$pass') " .
    			"where ita = '$ita' and email = '$email'  "
    	);
    	
    	$afftectedRows = $this->db->affected_rows();
		
		return $afftectedRows;
    }



}
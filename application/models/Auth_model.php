<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_model extends CI_Model {

	public function registerUser($form)
	{
		unset($form['register']);
		$this->db->insert('users', $form);
		$newUser_ID = $this->db->insert_id();
		return $newUser_ID;
    }

	public function userExist($login)
	{
		$this->db->select("id");
        $this->db->where('login', $login);
		$query = $this->db->get('users');

		if ($query->result())
			return true;
		else
			return false;
	}

	public function generateSecret($id, $code)
	{
		$data = array(
			'ga_secret' => $code
		);
		$this->db->where('id', $id);
		$this->db->update('users', $data);
	}

	public function getUserLoginData($login)
	{
		$this->db->select('id, pin, ga_secret');
		$this->db->where('login', $login);
		$this->db->limit(1);
		$query = $this->db->get('users');
		return $query->result();
	}

	public function getCountFailedAttempt($id, $time)
	{
		$this->db->select('id');
		$this->db->where('user_id =', $id);
		$this->db->where('status =', 1);
		$this->db->where('time >', $time);
		$query = $this->db->get('logs');
		return $query->num_rows();
	}

	public function setLastLogin($id)
	{
		$data = array('last_login' => date('Y-m-d H:i:s'));
		$this->db->where('id', $id);
		$query = $this->db->update('users', $data);
		return $query;
	}

	public function addLoginLog($id, $ip, $status)
	{
		$data['user_id'] = $id;
		$data['time'] = date('Y-m-d H:i:s');
		$data['ip'] = $ip;
		$data['status'] = $status;

		$this->db->insert('logs', $data);
		$query = $this->db->insert_id();
		return $query;
	}

	public function getUserLogs($id)
	{
		$this->db->select('ip, time, status');
		$this->db->where('user_id', $id);
		$this->db->order_by('time', 'DESC');
		$query = $this->db->get('logs');
		return $query->result();
	}

	public function getUserLastLogin($id)
	{
		$this->db->select('last_login');
		$this->db->where('id', $id);
		$query = $this->db->get('users');
		return $query->row();
	}
}
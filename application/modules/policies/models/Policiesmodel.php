<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Policiesmodel extends CI_Model {

var $table = 'policies p';
	var $column_order = array(null, 'policyname','description','department','policy_upload'); //set column field database for datatable orderable
	var $column_search = array('policyname','description','department','policy_upload'); //set column field database for datatable searchable 
	//var $order = array('id' => 'asc'); // default order 

	

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->user_departments='';

		$this->user_departments=$this->tank_auth->get_department(User::get_id());
	}

	private function _get_datatables_query()
	{

		$this->db->select('p.*, d.deptname');
		$this->db->from($this->table);
		$this->db->join('departments d', 'd.deptid = p.department', 'left');

		if($this->tank_auth->get_role_id()==3)
		{
			$user_department[]='00';
			$user_department[]=$this->user_departments;

			for ($i=0; $i <count($user_department) ; $i++) { 

				$this->db->or_where("FIND_IN_SET('".$user_department[$i]."', p.department)");
			}

			
		}

		

	
		//add custom filter here
			
		$this->db->where('p.status',0);
		$i = 0;
	
		foreach ($this->column_search as $product) // loop column 
		{
			if($_POST['search']['value']) // if datatable send POST for search
			{
				
				if($i===0) // first loop
				{
					$this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
					$this->db->like($product, $_POST['search']['value']);
				}
				else
				{
					$this->db->or_like($product, $_POST['search']['value']);
				}

				if(count($this->column_search) - 1 == $i) //last loop
					$this->db->group_end(); //close bracket
			}
			$i++;
		}
		
		if(isset($_POST['order'])) // here order processing
		{
			$this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order))
		{
			$order = $this->order;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}

	public function get_datatables()
	{
		$this->_get_datatables_query();
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	public function count_filtered()
	{
		$this->_get_datatables_query();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all()
	{	
		$this->db->where('p.status',0);
		$this->db->from($this->table);
		return $this->db->count_all_results();
	}

	public function get_by_id($id)
	{
		$this->db->from($this->table);
		$this->db->where('id',$id);
		$query = $this->db->get();

		return $query->row();
	}

	public function save($data)
	{
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}

	public function update($where, $data)
	{
		$this->db->update($this->table, $data, $where);
		return $this->db->affected_rows();
	}

	public function delete_by_id($id)
	{
		$this->db->where('id', $id);
		$this->db->delete($this->table);
	}

	


}
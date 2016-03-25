<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Person_model2 extends CI_Model {

	var $table = 'persons';
	var $column = array('firstName','lastName','gender','address','dob'); //set column field database for order and search
	var $order = array('id' => 'desc'); // default order 

	public function __construct()
	{
		parent::__construct();		
		$CI = &get_instance();
		//setting the second parameter to TRUE (Boolean) the function will return the database object.
		$this->db2 = $CI->load->database('second_database', TRUE);
		
	}

	private function _get_datatables_query()
	{
		

		


		$this->db2->from($this->table);

		$i = 0;
	
		foreach ($this->column as $item) // loop column 
		{
			if($_POST['search']['value']) // if datatable send POST for search
			{
				
				if($i===0) // first loop
				{
					$this->db2->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND. 
					$this->db2->like($item, $_POST['search']['value']);
				}
				else
				{
					$this->db2->or_like($item, $_POST['search']['value']);
				}

				if(count($this->column) - 1 == $i) //last loop
					$this->db2->group_end(); //close bracket
			}
			$column[$i] = $item; // set column array variable to order processing
			$i++;
		}
		
		if(isset($_POST['order'])) // here order processing
		{
			$this->db2->order_by($column[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order))
		{
			$order = $this->order;
			$this->db2->order_by(key($order), $order[key($order)]);
		}
	}

	function get_datatables()
	{
		$this->_get_datatables_query();
		if($_POST['length'] != -1)
		$this->db2->limit($_POST['length'], $_POST['start']);
		$query = $this->db2->get();
		return $query->result();
	}

	function count_filtered()
	{
		$this->_get_datatables_query();
		$query = $this->db2->get();
		return $query->num_rows();
	}

	public function count_all()
	{
		$this->db2->from($this->table);
		return $this->db2->count_all_results();
	}

	public function get_by_id($id)
	{
		$this->db2->from($this->table);
		$this->db2->where('id',$id);
		$query = $this->db2->get();

		return $query->row();
	}

	public function save($data)
	{
		$this->db2->insert($this->table, $data);
		return $this->db2->insert_id();
	}

	public function update($where, $data)
	{
		$this->db2->update($this->table, $data, $where);
		return $this->db2->affected_rows();
	}

	public function delete_by_id($id)
	{
		$this->db2->where('id', $id);
		$this->db2->delete($this->table);
	}


}

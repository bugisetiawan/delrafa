<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Crud_model extends CI_Model 
{	    
    // GET
    public function get_product_id($code)
    {
        $data = $this->db->select('id')->from('product')->where('code', $code)->get()->row_array();
        return $data['id'];
    }

    public function get_product_code($id)
    {
        $data = $this->db->select('code')->from('product')->where('id', $id)->get()->row_array();
        return $data['code'];
    }

    public function get($table)
    {
        return $this->db->get($table);
    }

    public function get_where($table, $where)
    {
        return $this->db->get_where($table, $where);
    }

    public function get_where_select($select, $table, $where)
    {
        $this->db->select($select);
        return $this->db->get_where($table, $where);
    }

    public function get_by_id($table, $id)
    {
        return $this->db->get_where($table, ['id' => $id]);
    }

    public function get_by_code($table, $code)
    {
        return $this->db->get_where($table, ['code' => $code]);
    }

    // INSERT
    public function insert_id($table, $data)
    {
        $this->db->insert($table, $data);
        return $this->db->insert_id();
    }

    public function insert($table, $data)
    {
        return $this->db->insert($table, $data);
    }

    // UPDATE
    public function update($table, $data, $where)
    {
        $this->db->where($where);
        return $this->db->update($table, $data);
    }

    public function update_by_id($table, $data, $id)
    {
        $this->db->where('id', $id);
        return $this->db->update($table, $data);
    }
    
    public function update_by_code($table, $data, $code)
    {
        $this->db->where('code', $code);
        return $this->db->update($table, $data);
    }

    // DELETE
    public function delete($table, $where)
    {
        return $this->db->delete($table, $where);
    }

    public function delete_by_id($table, $id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($table);
    }

    public function delete_by_code($table, $code)
    {
        $this->db->where('code', $code);
        return $this->db->delete($table);
    }
}

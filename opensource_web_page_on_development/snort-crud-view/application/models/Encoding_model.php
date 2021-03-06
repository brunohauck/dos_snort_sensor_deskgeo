<?php
/* 
 * Generated by CRUDigniter v2.3 Beta 
 * www.crudigniter.com
 */
 
class Encoding_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    /*
     * Get encoding by encoding_type
     */
    function get_encoding($encoding_type)
    {
        return $this->db->get_where('encoding',array('encoding_type'=>$encoding_type))->row_array();
    }
    
    /*
     * Get all encoding
     */
    function get_all_encoding()
    {
        return $this->db->get('encoding')->result_array();
    }
    
    /*
     * function to add new encoding
     */
    function add_encoding($params)
    {
        $this->db->insert('encoding',$params);
        return $this->db->insert_id();
    }
    
    /*
     * function to update encoding
     */
    function update_encoding($encoding_type,$params)
    {
        $this->db->where('encoding_type',$encoding_type);
        $response = $this->db->update('encoding',$params);
        if($response)
        {
            return "encoding updated successfully";
        }
        else
        {
            return "Error occuring while updating encoding";
        }
    }
    
    /*
     * function to delete encoding
     */
    function delete_encoding($encoding_type)
    {
        $response = $this->db->delete('encoding',array('encoding_type'=>$encoding_type));
        if($response)
        {
            return "encoding deleted successfully";
        }
        else
        {
            return "Error occuring while deleting encoding";
        }
    }
}

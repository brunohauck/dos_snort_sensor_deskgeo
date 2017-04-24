<?php
/* 
 * Generated by CRUDigniter v2.3 Beta 
 * www.crudigniter.com
 */
 
class Reference_system_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    /*
     * Get reference_system by ref_system_id
     */
    function get_reference_system($ref_system_id)
    {
        return $this->db->get_where('reference_system',array('ref_system_id'=>$ref_system_id))->row_array();
    }
    
    /*
     * Get all reference_system
     */
    function get_all_reference_system()
    {
        return $this->db->get('reference_system')->result_array();
    }
    
    /*
     * function to add new reference_system
     */
    function add_reference_system($params)
    {
        $this->db->insert('reference_system',$params);
        return $this->db->insert_id();
    }
    
    /*
     * function to update reference_system
     */
    function update_reference_system($ref_system_id,$params)
    {
        $this->db->where('ref_system_id',$ref_system_id);
        $response = $this->db->update('reference_system',$params);
        if($response)
        {
            return "reference_system updated successfully";
        }
        else
        {
            return "Error occuring while updating reference_system";
        }
    }
    
    /*
     * function to delete reference_system
     */
    function delete_reference_system($ref_system_id)
    {
        $response = $this->db->delete('reference_system',array('ref_system_id'=>$ref_system_id));
        if($response)
        {
            return "reference_system deleted successfully";
        }
        else
        {
            return "Error occuring while deleting reference_system";
        }
    }
}
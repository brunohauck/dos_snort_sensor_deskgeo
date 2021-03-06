<?php
/* 
 * Generated by CRUDigniter v2.3 Beta 
 * www.crudigniter.com
 */
 
class Event extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Event_model');
    } 

    /*
     * Listing of event
     */
    function index()
    {
        $data['event'] = $this->Event_model->get_all_event();

        $this->load->view('event/table',$data);
    }

    /*
     * Adding a new event
     */
    function add()
    {   
        if(isset($_POST) && count($_POST) > 0)     
        {   
            $params = array(
				'signature' => $this->input->post('signature'),
				'timestamp' => $this->input->post('timestamp'),
            );
            
            $event_id = $this->Event_model->add_event($params);
            redirect('event/index');
        }
        else
        {
            $this->load->view('event/add');
        }
    }  

    /*
     * Editing a event
     */
    function edit($sid)
    {   
        // check if the event exists before trying to edit it
        $event = $this->Event_model->get_event($sid);
        
        if(isset($event['sid']))
        {
            if(isset($_POST) && count($_POST) > 0)     
            {   
                $params = array(
					'signature' => $this->input->post('signature'),
					'timestamp' => $this->input->post('timestamp'),
                );

                $this->Event_model->update_event($sid,$params);            
                redirect('event/index');
            }
            else
            {   
                $data['event'] = $this->Event_model->get_event($sid);
    
                $this->load->view('event/edit',$data);
            }
        }
        else
            show_error('The event you are trying to edit does not exist.');
    } 

    /*
     * Deleting event
     */
    function remove($sid)
    {
        $event = $this->Event_model->get_event($sid);

        // check if the event exists before trying to delete it
        if(isset($event['sid']))
        {
            $this->Event_model->delete_event($sid);
            redirect('event/index');
        }
        else
            show_error('The event you are trying to delete does not exist.');
    }
    
}

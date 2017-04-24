<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Event_snort extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Event_snort_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $q = urldecode($this->input->get('q', TRUE));
        $start = intval($this->input->get('start'));
        
        if ($q <> '') {
            $config['base_url'] = base_url() . 'event_snort/index.html?q=' . urlencode($q);
            $config['first_url'] = base_url() . 'event_snort/index.html?q=' . urlencode($q);
        } else {
            $config['base_url'] = base_url() . 'event_snort/index.html';
            $config['first_url'] = base_url() . 'event_snort/index.html';
        }

        $config['per_page'] = 10;
        $config['page_query_string'] = TRUE;
        $config['total_rows'] = $this->Event_snort_model->total_rows($q);
        $event_snort = $this->Event_snort_model->get_limit_data($config['per_page'], $start, $q);

        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $data = array(
            'event_snort_data' => $event_snort,
            'q' => $q,
            'pagination' => $this->pagination->create_links(),
            'total_rows' => $config['total_rows'],
            'start' => $start,
        );
        $this->load->view('event_snort/event_list', $data);
    }

    public function read($id) 
    {
        $row = $this->Event_snort_model->get_by_id($id);
        if ($row) {
            $data = array(
		'sid' => $row->sid,
		'cid' => $row->cid,
		'signature' => $row->signature,
		'timestamp' => $row->timestamp,
	    );
            $this->load->view('event_snort/event_read', $data);
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('event_snort'));
        }
    }

    public function create() 
    {
        $data = array(
            'button' => 'Create',
            'action' => site_url('event_snort/create_action'),
	    'sid' => set_value('sid'),
	    'cid' => set_value('cid'),
	    'signature' => set_value('signature'),
	    'timestamp' => set_value('timestamp'),
	);
        $this->load->view('event_snort/event_form', $data);
    }
    
    public function create_action() 
    {
        $this->_rules();

        if ($this->form_validation->run() == FALSE) {
            $this->create();
        } else {
            $data = array(
		'signature' => $this->input->post('signature',TRUE),
		'timestamp' => $this->input->post('timestamp',TRUE),
	    );

            $this->Event_snort_model->insert($data);
            $this->session->set_flashdata('message', 'Create Record Success');
            redirect(site_url('event_snort'));
        }
    }
    
    public function update($id) 
    {
        $row = $this->Event_snort_model->get_by_id($id);

        if ($row) {
            $data = array(
                'button' => 'Update',
                'action' => site_url('event_snort/update_action'),
		'sid' => set_value('sid', $row->sid),
		'cid' => set_value('cid', $row->cid),
		'signature' => set_value('signature', $row->signature),
		'timestamp' => set_value('timestamp', $row->timestamp),
	    );
            $this->load->view('event_snort/event_form', $data);
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('event_snort'));
        }
    }
    
    public function update_action() 
    {
        $this->_rules();

        if ($this->form_validation->run() == FALSE) {
            $this->update($this->input->post('sid', TRUE));
        } else {
            $data = array(
		'signature' => $this->input->post('signature',TRUE),
		'timestamp' => $this->input->post('timestamp',TRUE),
	    );

            $this->Event_snort_model->update($this->input->post('sid', TRUE), $data);
            $this->session->set_flashdata('message', 'Update Record Success');
            redirect(site_url('event_snort'));
        }
    }
    
    public function delete($id) 
    {
        $row = $this->Event_snort_model->get_by_id($id);

        if ($row) {
            $this->Event_snort_model->delete($id);
            $this->session->set_flashdata('message', 'Delete Record Success');
            redirect(site_url('event_snort'));
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('event_snort'));
        }
    }

    public function _rules() 
    {
	$this->form_validation->set_rules('signature', 'signature', 'trim|required');
	$this->form_validation->set_rules('timestamp', 'timestamp', 'trim|required');

	$this->form_validation->set_rules('sid', 'sid', 'trim');
	$this->form_validation->set_error_delimiters('<span class="text-danger">', '</span>');
    }

}

/* End of file Event_snort.php */
/* Location: ./application/controllers/Event_snort.php */
/* Please DO NOT modify this information : */
/* Generated by Harviacode Codeigniter CRUD Generator 2016-12-16 07:29:40 */
/* http://harviacode.com */
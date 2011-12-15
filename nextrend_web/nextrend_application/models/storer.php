<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Storer
 *
 * This is an example of a few basic user interaction methods you could use
 * all done with a hardcoded array.
 *
 * @package		CodeIgniter
 * @category	Model
 * @author		leogs
 */
class Storer extends CI_Model {

    private $data_table = 'records';

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function store_track_info($input)
    {

        $query = $this->db->insert_string($this->data_table, $input);
        $res = $this->db->query($query);
        if ($res)
        {
            $result['message'] = 'the request complete and ' . $this->db->affected_rows() . ' new value(s) inserted';
            $result['code'] = 200;
            return $result;
        } else
        {
            $error_code = $this->db->_error_number();
            $error_message = $this->db->_error_message();
            $result = array(
                'error' => array(
                    'code' => $error_code,
                    'message' => $error_message),
                'code' => 403);
            return $result;
        }
    }

//   
}
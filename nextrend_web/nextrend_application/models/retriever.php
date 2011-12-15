<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * retreiver
 *
 * This is an example of a few basic user interaction methods you could use
 * all done with a hardcoded array.
 *
 * @package		CodeIgniter
 * @category	Model
 * @author		leogs
 */
class Retriever extends CI_Model {

    private $data_table = 'records';

    function __construct()
    {

        parent::__construct();
        $this->load->database();
    }

    function get_tag_tracks($param)
    {

        $query = $this->db->from($this->data_table)
                ->select('album_name,track_name,artist_name,timestamp')
                ->where('tag_id', $param['tag_id'])
                ->where('timestamp >=', $param['timestamp'])
                ->order_by($this->data_table . '.timestamp', 'asc')
                ->get();

        
//    echo $query->num_rows();

        $results['count'] = $query->num_rows();
        $results['tracks'] = $query->result();
        return $results;
    }

}
<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * request
 *
 * This is an example of a few basic user interaction methods you could use
 * all done with a hardcoded array.
 *
 * @package		CodeIgniter
 * @subpackage	Rest Server
 * @category	Controller

 */
// This can be removed if you use __autoload() in config.php OR use Modular Extensions


require APPPATH . '/libraries/REST_Controller.php';

class Request extends REST_Controller {

    function get_track_info_get()
    {

        $param['tag_id'] = $this->get('tag_id');

        $duration = $this->get('duration');
        $duration = (int) $duration;  //min
        //convert to seconds
        $duration = $duration * 60; //sec


        $current_time = strtotime($this->get('timestamp'));

        $time_limit = $current_time - $duration;

        $param['timestamp'] = date('Y-m-d H:i:s', $time_limit);

        $this->load->model('Retriever');
        $this->response($this->Retriever->get_tag_tracks($param), 200);
    }

}
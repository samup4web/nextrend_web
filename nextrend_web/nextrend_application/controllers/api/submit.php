<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Receive
 *
 * This is an example of a few basic user interaction methods you could use
 * all done with a hardcoded array.
 *
 * @package		CodeIgniter
 * @subpackage	Rest Server
 * @category	Controller
 * @author		leogs
 */
// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . '/libraries/REST_Controller.php';

class Submit extends REST_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->library('checker');
        $this->load->library('decoder');
    }

    function track_info_post()
    {

        //130.240.98.214
        //Content-type: application/x-www-form-urlencoded”
       //tag_id=3323&timestamp=12-12-2001&artist=sam&track=track&album=album
        
        $input['tag_id'] = $this->post('tag_id');
        $input['album_name'] = $this->post('album');
        $input['track_name'] = $this->post('track');
        $input['artist_name'] = $this->post('artist');
        $input['timestamp'] = $this->post('timestamp');
        
        $input['unique_key_field'] = $input['tag_id'].$input['album_name'].$input['track_name'].$input['artist_name'];
        
        $this->load->model('Storer');
        $storing_call = $this->Storer->store_track_info($input);
        $this->response($storing_call, $storing_call['code']);
    }

}

?>
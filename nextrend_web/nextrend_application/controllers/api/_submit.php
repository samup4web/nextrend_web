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



    
    
    
    
    
    
    
    
    
    function sensor_measurement_batch_post()
    {
        //variable to flag error in batch record
        $bulk_correct_input['success'] = false;
        //filtered_bulk msg from checker method
        $filtered_bulk = array();

        //get data from post...
        $param = $this->post('param');

        //check if there is param from post data, if none, report error
        if (!$param)
        {
            $input = false;
            $correct_input['success'] = false;
            $correct_input['error'] = "no post parameter";
            $this->response($correct_input, 403);
        } else
        {
            $input = $this->decoder->decode_input($param, $this->format());
        }

        //run loop to check if all measurements are correct, if not, return error message
        //Don't proceed to store value in database, if there is error...
        if ($input['measurements'])
        {
            foreach ($input['measurements'] as $input_row)
            {
                $correct_input = $this->_sensor_meas_checker($input_row);
                if ($correct_input['success'])
                {
                    $filtered_bulk[] = $correct_input['data'];
                    $bulk_correct_input['success'] = true;
                } else
                {
                    $bulk_correct_input['success'] = false;
                    $correct_input['extra_msg'] = " :: error occured in processing batch data";
                    $this->response($correct_input, 403);
                    break;
                }
            }
        } else
        {
            $correct_input['success'] = false;
            $correct_input['error'] = "invalid post parameter, 'measurements' field is missing";
            $this->response($correct_input, 403);
        }






        if ($bulk_correct_input['success'])
        {
            $measurement_count = 0;
            foreach ($filtered_bulk as $value)
            {
                $measurement_count++;
                $this->load->model('Storer');
                $storing_call = $this->Storer->store_measurement($value);
            }
//log the event anyway!
            if ($storing_call['code'] == 200)
            {
                $response['message'] = "the request complete and $measurement_count new value(s) inserted ::batch";
                $response['code'] = 200;
                $this->response($response, $storing_call['code']);
            }
        }
    }

    function sensor_measurement_post()
    {
//TODO: check the input to see if it has the correct format
//    echo 'is it false? '.($this->post('param')==false).' and this? '.($this->input->post('param')==false);
//var_dump($this->input->post()); echo '  <br />';
        $param = $this->post('param');
//    print_r($param);
        if (!$param)
        {
//        echo '$this->post(param) was false';
            $input = $this->post();
            if (empty($input))
            {
//            echo '$this->post() was empty';
                $input = false;
            }
        } else
        {
//        echo '$this->post(param) was not false';
            $input = $this->decoder->decode_input($param, $this->format());
        }

        $correct_input = $this->_sensor_meas_checker($input);
//    var_dump($correct_input); echo ' <br />';
        if ($correct_input['success'])
        {
//TODO: send the correct input to model/store.php
            $this->load->model('Storer');
            $storing_call = $this->Storer->store_measurement($correct_input['data']);
//log the event anyway!
//if everything went well with the DB: 
           
        } else
        {
//      $correct_input['message'] = 'input data invalid';
            unset($correct_input['success']);
            $correct_input['code'] = 403;
            $this->response(($correct_input), $correct_input['code']);
        }
    }

    /**
     * 
     * @param type $input all the post send to the method sensor_measurement_post
     * @return filtered post data or false if a compulsory field is missing
     */
    function _sensor_meas_checker($input)
    {
        $filtered = array();
        $result['success'] = true;
        if ($input == false)
        {
//no post['param'] available ... 
            $result['error'][] = 'no post parameter';
            $result['success'] = false;
        }
//do a manual check for all the parameters:
//sensor and gateway ids
//    var_dump($from_json); echo ' <br />';
        if (!$this->checker->check_string($input, $filtered, 'sensor_id'))
        {
//      echo 'sensor_id is  not ok <br />';
            $result['error'][] = 'sensor_id invalid ';
            $result['success'] = false;
        }
//    var_dump($filtered); echo ' <br />';
        if (!$this->checker->check_string($input, $filtered, 'gateway_id'))
        {
//      echo 'gateway_id is not ok <br />';
            $result['error'][] = 'gateway_id invalid ';
            $result['success'] = false;
        }
//    var_dump($filtered); echo ' <br />';
        if (!$this->checker->check_float($input, $filtered, 'pos_longitude'))
        {
//      echo 'pos_longitude is not ok <br />';
            $result['error'][] = 'pos_longitude invalid ';
            $result['success'] = false;
        }
//    var_dump($filtered); echo ' <br />';
        if (!$this->checker->check_float($input, $filtered, 'pos_latitude'))
        {
//      echo 'pos_latitude is not ok <br />';
            $result['error'][] = 'pos_latitude invalid ';
            $result['success'] = false;
        }
//    var_dump($filtered); echo ' <br />';
        if (!$this->checker->check_int($input, $filtered, 'data_value'))
        {
//      echo 'data_value is not ok <br />';
            $result['error'][] = 'data_value invalid ';
            $result['success'] = false;
        }
//    var_dump($filtered); echo ' <br />';
        if (!$this->checker->check_timestamp($input, $filtered, 'timestamp'))
        {
//      echo 'timestamp is not ok <br />';
            $result['error'][] = 'timestamp invalid ';
            $result['success'] = false;
        }
//    var_dump($filtered); echo ' <br />';
// if the system has not returned yet then return filtered data !
//    echo 'everything is ok';
        if ($result['success'])
        {
            $result['data'] = $filtered;
        }
        return $result;
    }

//  function sensor_measurement_get()
//  {
//    //dummy method to explain the required input for a post
//    $data['title']='how to use sensor_measurement';
//    $data['method_name']='sensor_measurement';
//    $data['access']='POST only';
//    $data['parameters']='id = int ; sensor_id, gateway_id = str ; '
//    . 'pos_longitude, pos_latitude = float ; data_value = int ; timestamp = timestamp';
//    $this->response($data,200);
//  }
}
<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Submit_test
 *
 * This is an example of a few basic user interaction methods you could use
 * all done with a hardcoded array.
 *
 * @package		CodeIgniter
 * @subpackage	unit testing
 * @category	Controller
 * @author		leogs
 */
require APPPATH . '/libraries/Unit_test_Controller.php';
class Submit_test extends Unit_test_Controller {

    function __construct() {
        parent::__construct();
        echo 'construct of Submit_test';
        $this->enable_strict_mode(true);
        echo 'construct over !';
    }

    function index() {
        echo ('starting the test');
        $this->_test_sensor_measurement();
        echo ('Here is the report --> ');
//        echo ($this->unit->report());
//        echo (' <-- This was the report');
        echo $this->build_report();
    }

    function _test_sensor_measurement() {
        $param = array(
            'sensor_id' => 'test_sensor_id',
            'gateway_id' => 'test_gateway_id',
            'data_value' => 9876543210,
            'pos_latitude' => 352.2145,
            'pos_longitude' => 13468.2132,
            'timestamp' => '2012-01-01 00:00:00'
        );
        $expected_answer = array( 'message'=>'the request complete and 1 new value(s) inserted','code'=>200);
        $this->_single_test_sensor_m($param, $expected_answer, 'correct call test');
        
        $param['data_value'] = 'a string';
        $expected_anser = array( 'error' => array("data_value invalid "), 'code'=> 403 );
        $this->_single_test_sensor_m($param, $expected_answer, 'wrong data_value test');
        
        $param['pos_latitude'] = 'another string';
        $param['pos_longitude'] = 'yet another string';
        $expected_anser = array( 'error' => array("data_value invalid ", "pos_latitude invalid ", "pos_longitude invalid "), 'code'=> 403 );
        $this->_single_test_sensor_m($param, $expected_answer, 'wrong data_value, pos_lat and pos_long test');
        
        $param['data_value'] =9876543210;
        $param['pos_latitude'] = 352.2145;
        $param['pos_longitude'] = 13468.2132;
        unset($param['sensor_id']);
        $expected_anser = array( 'error' => array("data_value invalid ", "pos_latitude invalid ", "pos_longitude invalid "), 'code'=> 403 );
        $this->_single_test_sensor_m($param, $expected_answer, 'sensor_id missing test');
        
    }

    function _single_test_sensor_m($param, $expected_answer, $test_name){
        $this->perform_post_tests('api/submit/sensor_measurement', $param, $expected_answer, $test_name);
    }
}

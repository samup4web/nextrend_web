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
    
    
    function get_track_info_get(){
        
    }
  
  function all_measurements_get(){
    $this->_drop_db();
  }
//  function all_measurements_post(){
//    $this->_drop_db();
//  }
  
  function _create_filter(){
      $filterString = $this->get('fields');
      $wishedfield = split(',', $filterString);
      return $wishedfield;
  }
  
  function _drop_db()
  {
    $this->load->model('Retriever');
    $this->response($this->Retriever->drop_measurements(),200);
  }
//  function measurements_period_post()
//  {
//    $before = $this->post('before');
//    $after = $this->post('after');
//    $this->_drop_period($after, $before);
//  }
  function measurements_period_get()
  {
    $end = $this->get('end');
    $start = $this->get('start');
    $this->_drop_period($start, $end);
  }
  function _drop_period($start, $end)
  {
    $this->load->library('checker');
//    echo 'drop period';
    if($this->checker->is_mysqldate($end) and $this->checker->is_mysqldate($start))
    {
        if( $this->_check_date_order($start,$end))
        {
//            echo 'retreiving mesurements taken between : '.$after.' and '.$before;
            $this->load->model('Retriever');
            $this->response($this->Retriever->drop_measurements_period($start, $end),200);
        }else{
//            echo 'not workingggggg';
            $this->response('please make sure that end_time if actually after the start_time', 403);
        }
    }else
    {
      $this->response('invalid parameters, please see the documentation', 403);
    }
  }
  
  function date_check_test_get()
  {
    $this->load->library('checker');
    print_r($this->checker->is_mysqldate($this->get('date')));
  }
  
 
  function _check_date_order($date1, $date2){
      $objTimeZone = new DateTimezone("Europe/London");

//      echo 'checking date order ...';
      $older_date = new DateTime($date1, $objTimeZone);
//      echo 'older date created';      var_dump($older_date);
      $newer_date = new DateTime($date2, $objTimeZone);
//      echo 'newer date created';      var_dump($newer_date);
      $diff = $older_date->diff($newer_date);
//      echo 'I have the diff between the dates!!! is older < newer ?'.var_export(($older_date < $newer_date), true).'<br /> diff = '.$diff->format('%Y-%m-%d %H:%i:%s');
//      return is_empty($diff->format('r'));
      return ($older_date < $newer_date);
//      return false;
  }
}
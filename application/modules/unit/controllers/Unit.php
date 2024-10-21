<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH . 'controllers/AppBackend.php');

class Unit extends AppBackend
{
  function __construct()
  {
    parent::__construct();
    $this->load->model(array(
      'AppMixModel',
      'UnitModel',
    ));
    $this->load->library('form_validation');
  }

  public function index()
  {
    $data = array(
      'app' => $this->app(),
      'main_js' => $this->load_main_js('unit'),
      'card_title' => 'Unit',
    );
    $this->template->set('title', $data['card_title'] . ' | ' . $data['app']->app_name, TRUE);
    $this->template->load_view('index', $data, TRUE);
    $this->template->render();
  }

  public function ajax_get_all()
  {
    $filter = $this->input->get('filter');
    $query = $this->UnitModel->getQuery($filter);
    $response = $this->AppMixModel->getdata_dtAjax($query);
    echo json_encode($response);
  }

  public function ajax_save($id = null)
  {
    $this->handle_ajax_request();
    $this->form_validation->set_rules($this->UnitModel->rules());

    if ($this->form_validation->run() === true) {
      if (is_null($id)) {
        echo json_encode($this->UnitModel->insert());
      } else {
        echo json_encode($this->UnitModel->update($id));
      };
    } else {
      $errors = validation_errors('<div>- ', '</div>');
      echo json_encode(array('status' => false, 'data' => $errors));
    };
  }

  public function ajax_delete($id)
  {
    $this->handle_ajax_request();
    echo json_encode($this->UnitModel->delete($id));
  }

  public function detail()
  {
    //$agent = new Mobile_Detect;
    $ref = $this->input->get('ref');
    $ref = (!is_null($ref) && is_numeric($ref)) ? $ref : null;
    $actionLabel = '<span class="badge badge-info">View</span> ';

    $unit = $this->UnitModel->getDetail(['unit.id' => $ref]);
    $data = array(
      'app' => $this->app(),
      'main_js' => $this->load_main_js('unit', false, array(
        'action_route' => 'detail',
        'key' => $ref,
        'namaUnit' => @$unit->nama_unit,
      )),
      'card_title' => 'Data Pegawai Unit '.@$unit->nama_unit,
      'controller' => $this,
      'isnull' => 'true',
      //'is_mobile' => $agent->isMobile(),
      'unit' => $unit,
    );
    //$this->template->set_template('sb_admin_partial');
    $this->template->set('title', $data['card_title'] . ' | ' . $data['app']->app_name, TRUE);
    $this->template->load_view('view', $data, TRUE);
    $this->template->render();
  }

}

<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MesinModel extends CI_Model
{
  private $_table = 'mesin';
  private $_tableView = '';

  public function rules()
  {
    return array(
      [
        'field' => 'namamesin',
        'label' => 'Nama Mesin',
        'rules' => 'required|trim'
      ],
      [
        'field' => 'ipadress',
        'label' => 'IP Adress',
        'rules' => 'required|trim'
      ],
      [
        'field' => 'lokasi',
        'label' => 'Lokasi',
        'rules' => 'required|trim'
      ],
    );
  }

  public function getQuery($filter = null)
  {
    $query = "
      SELECT mesin.* FROM mesin
      WHERE 1=1
    ";

    if (!is_null($filter)) $query .= $filter;
    return $query;
  }

  public function getAll($params = array(), $orderField = null, $orderBy = 'asc')
  {
    $this->db->where($params);

    if (!is_null($orderField)) {
      $this->db->order_by($orderField, $orderBy);
    };

    return $this->db->get($this->_table)->result();
  }

  public function getDetail($params = array())
  {
    $this->db->where($params);
    return $this->db->get($this->_table)->row();
  }

  public function insert()
  {
    $response = array('status' => false, 'data' => 'No operation.');
    
    $ip = $this->input->post('ipadress');
    $pingResult = $this->ping($ip);
    
    if ($pingResult) {
        $status = "success";
    } else {
        $status = "failed";
    }

    try {
      $this->namamesin = $this->input->post('namamesin');
      $this->ipadress = $this->input->post('ipadress');
      $this->commkey = $this->input->post('commkey');
      $this->lokasi = $this->input->post('lokasi');
      $this->status = $status;
      $this->created_by = $this->session->userdata('user')['id'];
      $this->db->insert($this->_table, $this);

      $response = array('status' => true, 'data' => 'Data has been saved.');
    } catch (\Throwable $th) {
      $response = array('status' => false, 'data' => 'Failed to save your data.');
    };

    return $response;
  }

  public function insertBatch($data)
  {
    $response = array('status' => false, 'data' => 'No operation.');

    try {
      $this->db->insert_batch($this->_table, $data);

      $response = array('status' => true, 'data' => 'Data has been saved.');
    } catch (\Throwable $th) {
      $response = array('status' => false, 'data' => 'Failed to save your data.');
    };

    return $response;
  }

  public function update($id)
  {
    $response = array('status' => false, 'data' => 'No operation.');

    $ip = $this->input->post('ipadress');
    $pingResult = $this->ping($ip);
    
    if ($pingResult) {
        $status = "success";
    } else {
        $status = "failed";
    }

    try {
      $this->namamesin = $this->input->post('namamesin');
      $this->ipadress = $this->input->post('ipadress');
      $this->commkey = $this->input->post('commkey');
      $this->lokasi = $this->input->post('lokasi');
      $this->status = $status;
      $this->updated_by = $this->session->userdata('user')['id'];
      $this->updated_date = date('Y-m-d H:i:s');
      $this->db->update($this->_table, $this, array('id' => $id));

      $response = array('status' => true, 'data' => 'Data has been saved.');
    } catch (\Throwable $th) {
      $response = array('status' => false, 'data' => 'Failed to save your data.');
    };

    return $response;
  }

  public function delete($id)
  {
    $response = array('status' => false, 'data' => 'No operation.');

    try {
      $this->db->delete($this->_table, array('id' => $id));

      $response = array('status' => true, 'data' => 'Data has been deleted.');
    } catch (\Throwable $th) {
      $response = array('status' => false, 'data' => 'Failed to delete your data.');
    };

    return $response;
  }

  public function truncate()
  {
    $response = array('status' => false, 'data' => 'No operation.');

    try {
      $this->db->truncate($this->_table);

      $response = array('status' => true, 'data' => 'Data has been truncated.');
    } catch (\Throwable $th) {
      $response = array('status' => false, 'data' => 'Failed to truncate your data.');
    };

    return $response;
  }

  function br2nl($text)
  {
    return str_replace("\r\n", '<br/>', htmlspecialchars_decode($text));
  }

  function clean_number($number)
  {
    return preg_replace('/[^0-9]/', '', $number);
  }

  public function checkConnect($ip)
{
    $response = array('status' => false, 'data' => 'No operation.');

    $pingResult = $this->ping($ip);
    
    if ($pingResult) {
        $status = "success";
    } else {
        $status = "failed";
    }

    try {
        $this->status = $status; 
        $this->updated_by = $this->session->userdata('user')['id'];
        $this->updated_date = date('Y-m-d H:i:s');
        $this->db->update($this->_table, $this, array('ipadress' => $ip));

        $response = array('status' => true, 'data' => 'Ping success.');
    } catch (\Throwable $th) {
        $response = array('status' => false, 'data' => 'Ping Failed.');
    }

    return $response;
}

public function ping($ip)
{
    $reply = 1;
    $ping = exec("ping -n $reply $ip", $output, $status);
    return $status === 0;
}

}

<?php
defined('BASEPATH') or exit('No direct script access allowed');

class PegawaiModel extends CI_Model
{
  private $_table = 'pegawai';
  private $_tableView = '';

  public function rules()
  {
    return array(
      [
        'field' => 'absen_pegawai_id',
        'label' => 'ID Finger',
        'rules' => 'required|trim'
      ],
      [
        'field' => 'nama_lengkap',
        'label' => 'Nama Pegawai',
        'rules' => 'required|trim'
      ],
    );
  }

  public function getQuery($filter = null)
  {
    $query = "
      SELECT t.* FROM (
        SELECT 
            p.id,
            p.absen_pegawai_id,
            p.nama_lengkap,
            COALESCE(NULLIF(p.departemen, ''), '-') AS dept,
            COALESCE(NULLIF(p.nomorpin, ''), '-') AS nopin,
            COUNT(att.tanggal_absen) AS datetime_count
        FROM pegawai p
        LEFT JOIN attendancelog att ON p.absen_pegawai_id = att.absen_id
        GROUP BY p.id, p.absen_pegawai_id, p.nama_lengkap, p.departemen, p.nomorpin
      ) t
      WHERE 1=1
    ";
    if (!is_null($filter)) $query .= $filter;
    return $query;
  }

  public function getSearch($value = null)
  {
      $this->db->select('
          pegawai.*,
          unit.nama_unit
      ');
      $this->db->from($this->_table);
      $this->db->join('unit', 'unit.nama_unit = pegawai.departemen', 'left');
      $this->db->group_start();
      $this->db->like('lower(nama_lengkap)', $value);
      $this->db->or_where("CAST(absen_pegawai_id AS TEXT) LIKE '%$value%'");
      $this->db->group_end();
  
      return $this->db->get()->result();
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
    return $this->db->where($params)->get($this->_table)->row();
  }

  public function insert()
  {
    $response = array('status' => false, 'data' => 'No operation.');

    try {
      $this->absen_pegawai_id = $this->input->post('absen_pegawai_id');
      $this->nama_lengkap = $this->input->post('nama_lengkap');
      $this->departemen = $this->input->post('departemen');
      $this->nomorpin = $this->input->post('nomorpin');
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
    $failedInsertions = [];
    $existingRecordsCount = 0;
    $importedCount = 0;

    $this->db->trans_start();

    try {
        foreach ($data as $row) {
            $userID = $row['absen_pegawai_id'];

            $this->db->where('absen_pegawai_id', $userID);
            $count = $this->db->count_all_results($this->_table);

            if ($count == 0) {
                $insertData = [
                    'absen_pegawai_id' => $userID,
                    'nama_lengkap' => $row['nama_lengkap'],
                    'departemen' => $row['departemen'],
                    'nomorpin' => $row['nomorpin'],
                    'created_by' => $row['created_by']
                ];

                if ($this->db->insert($this->_table, $insertData)) {
                    $importedCount++;
                } else {
                    $failedInsertions[] = [
                        'absen_pegawai_id' => $userID,
                        'error' => $this->db->error()['message']
                    ];
                }
            } else {
                $existingRecordsCount++;
            }
        }
        
        $this->db->trans_complete();
    } catch (Exception $e) {
        $this->db->trans_rollback();
        $failedInsertions[] = [
            'absen_pegawai_id' => isset($userID) ? $userID : 'N/A',
            'error' => $e->getMessage()
        ];
    }
    
    return [
        'importedCount' => $importedCount,
        'existingRecordsCount' => $existingRecordsCount,
        'failedInsertions' => $failedInsertions,
    ];
  }


  public function update($id)
  {
    $response = array('status' => false, 'data' => 'No operation.');

    try {
      $this->absen_pegawai_id = $this->input->post('absen_pegawai_id');
      $this->nama_lengkap = $this->input->post('nama_lengkap');
      $this->departemen = $this->input->post('departemen');
      $this->nomorpin = $this->input->post('nomorpin');
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
}

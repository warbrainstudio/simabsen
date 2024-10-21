<?php
defined('BASEPATH') or exit('No direct script access allowed');

class UnitModel extends CI_Model
{
  private $_table = 'unit';
  private $_tableView = '';

  public function rules()
  {
    return array(
      [
        'field' => 'nama_unit',
        'label' => 'Nama Unit',
        'rules' => 'required|trim'
      ]
    );
  }

  public function getQuery($filter = null)
  {
    $query = "
      SELECT t.* FROM (
        SELECT 
            u.id,
            u.nama_unit,
            COUNT(p.departemen) AS dep_count
        FROM unit u
        LEFT JOIN pegawai p ON u.nama_unit = p.departemen
        GROUP BY u.id, u.nama_unit, p.departemen
      ) t
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
    return $this->db->where($params)->get($this->_table)->row();
  }

  public function insert()
  {
    $response = array('status' => false, 'data' => 'No operation.');

    try {
      $this->nama_unit = $this->input->post('nama_unit');
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
            $namaUnit = $row['nama_unit'];

            $this->db->where('nama_unit', $namaUnit);
            $count = $this->db->count_all_results($this->_table);

            if ($count == 0) {
                $insertData = [
                    'nama_unit' => $namaUnit
                ];

                if ($this->db->insert($this->_table, $insertData)) {
                    $importedCount++;
                } else {
                    $failedInsertions[] = [
                        'nama_unit' => $namaUnit,
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
            'nama_unit' => isset($namaUnit) ? $namaUnit : 'N/A',
            'error' => $e->getMessage()
        ];
    }
  }

  /*public function insertBatch($data)
  {
    $response = array('status' => false, 'data' => 'No operation.');

    try {
      $this->db->insert_batch($this->_table, $data);

      $response = array('status' => true, 'data' => 'Data has been saved.');
    } catch (\Throwable $th) {
      $response = array('status' => false, 'data' => 'Failed to save your data.');
    };

    return $response;
  }*/

  public function update($id)
  {
    $response = array('status' => false, 'data' => 'No operation.');

    try {
      $this->nama_unit = $this->input->post('nama_unit');
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

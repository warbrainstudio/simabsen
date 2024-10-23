<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ApiAbsen extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('parse');
        $this->load->model('MesinModel');
    }

    public function index(){
        
        $api = 'Welcome to Simabsen API. to get input name and data finger machine, go to api/getdata. For fetch and import data into your database, use api/fetchdata. You need token to fetch data, ask developer for token';
        
        $input_list = [
            'token'     => 'Input for the token of this API',
            'host'      => 'Input for your host',
            'port'      => 'Input for your port',
            'database'  => 'Input for your database',
            'username'  => 'Input for your database username',
            'password'  => 'Input for your database password',
            'table'     => 'Input for the table to store data',
            'ip'        => 'Input for the IP of the fingerprint machine. If you let empty this input, this API will automatically use all IP from \'list_mesin\' ',
            'key'       => 'Input for the Comm key of the fingerprint machine',
            'alldata'   => 'Input for choosing whether you want to fetch all data or not. The value is boolean true or false.',
            'start_date'=> 'Input date to fetch data based on the start date you choose',
            'end_date'  => 'Input date to fetch data based on the end date you choose' ,
            'Note:' => 'You don\'t have to choose a date, but the default will be to fetch yesterday\'s data.'
        ];
        

        $this->output
             ->set_content_type('application/json')
             ->set_output(json_encode($api));

        return $input_list;
        
    }

    public function ping($ip) {
        $reply = 1;
        $ping = exec("ping -n $reply $ip", $output, $status);
        return $status === 0;
    }

    public function getData() {

            $this->load->database();
            $query = $this->db->get('mesin');
            $mesins = $query->result();
            $list_input = $this->index('input_list');
            $list_mesin = [];
        
            foreach ($mesins as $mesin) {
                $pingResult = $this->ping($mesin->ipadress);

                if ($pingResult) {
                    $status = "Connect";
                } else {
                    $status = "Disconnect";
                }

                $list_mesin[] = [
                    'ip' => $mesin->ipadress,
                    'commkey' => $mesin->commkey,
                    'name' => $mesin->namamesin,
                    'lokasi' => $mesin->lokasi,
                    'status' => $status
                ];
            }

            $data_api = [
                'list_input' => $list_input,
                'list_mesin' => $list_mesin,
            ];

            $this->output
             ->set_content_type('application/json')
             ->set_output(json_encode($data_api));

             return $list_mesin;
    }
    

    public function fetchData() {

        $listMesin = $this->getData('list_mesin');
        
        $token = 'XVd17lwEgOHcvKgjJWGWbuufQdte7WhiPLerllmSWcvr8jKLz6vqqkQkl4DIQzvbOUAtsxvl1TDviMlS3bQEewLszTxxGeAuv8XS';
        $getToken = $this->input->get('token');
        $host = $this->input->get('host');
        $port = $this->input->get('port');
        $user = $this->input->get('username');
        $pwd = $this->input->get('password');
        $dbs = $this->input->get('database');
        $table = $this->input->get('table');
        $IP = $this->input->get('ip');
        $Key = $this->input->get('key');
        $isAll = $this->input->get('alldata') === 'true';
        $startDate = $this->input->get('start_date');
        $endDate = $this->input->get('end_date');

        if($getToken!==$token){
            $response = array(
                'status' => false,
                'message' => "Gagal gunakan API. Token kosong atau salah"
            );
            $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode($response));
        }else{
            $arrayInput = array(
                'host' => $host,
                'port' => $port,
                'user' => $user,
                'password' => $pwd,
                'database' => $dbs,
                'table' => $table,
                'ip' => $IP,
                'key' => $Key,
                'start_date' => $startDate,
                'end_date' => $endDate,
            );
            if(!empty($IP)){
                $this->fetchingData($arrayInput);
            }else{
                foreach ($listMesin as $mesin) {
                    $arrayInput['ip'] = $mesin['ip'];
                    $arrayInput['key'] = $mesin['commkey'];
                    $this->fetchingData($arrayInput);  
                }
            }
        }
    }

    public function fetchingData($arrayInput){
        $IP = $arrayInput['ip'];
        $firstDate = date('2023-07-03');
        $currentDate = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $startDate = $arrayInput['start_date'];
        $endDate = $arrayInput['end_date'];
        if($this->checkIPMachine($IP)){
            if (empty($startDate) || empty($endDate)) {
                if($isAll){
                    $startDate = $firstDate;
                    $endDate = $currentDate;
                    $data = $this->fetchDataFromMachine($IP, $arrayInput['Key'], $startDate, $endDate);
                }else{
                    $startDate = $yesterday;
                    $endDate = $yesterday;
                    $data = $this->fetchDataFromMachine($IP, $arrayInput['key'], $startDate, $endDate);
                }
            }else{
                $data = $this->fetchDataFromMachine($IP, $arrayInput['key'], $startDate, $endDate);
            }
            if (!is_array($data)) {
                $data = [];
            }
            //$this->output->set_content_type('text/xml');
            //$responseXml = $this->createSoapResponse($data);
            //$this->output->set_output($responseXml);
            $filldata = $data;
            $dataCount['dataCount'] = count($filldata);
            $arrayDB = array(
                'host' => $arrayInput['host'],
                'port' => $arrayInput['port'],
                'user' => $arrayInput['user'],
                'password' => $arrayInput['password'],
                'database' => $arrayInput['database'],
                'table' => $arrayInput['table'],
                'ip' => $IP,
                'start_date' => $startDate,
                'end_date' => $endDate,
            );
            $this->checkConnectionDB($filldata, $arrayDB);
        }else{
            $response = array(
                'status' => false,
                'message' => "Gagal gunakan API. Cek IP Address atau Mesin Finger"
            );
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($response));
        }
    }

    public function checkIPMachine($IP) { 
        $timeout = 200;
        $Connect = @fsockopen($IP, 80, $errno, $errstr, $timeout);
        
        return $Connect !== false;
    }

    public function fetchDataFromMachine($IP, $Key, $startDate, $endDate) {
        $timeout = 200;
        $Connect = fsockopen($IP, "80", $errno, $errstr, $timeout);
        $filteredData = [];
    
        if ($Connect) {
            $formattedStartDate = date('Y-m-d\TH:i:s', strtotime($startDate . ' 00:00:00'));
            $formattedEndDate = date('Y-m-d\TH:i:s', strtotime($endDate . ' 23:59:59'));
    
            $soap_request = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
    <soap:Body>
        <GetAttLog xmlns="http://tempuri.org/">
            <ArgComKey xsi:type="xsd:integer">$Key</ArgComKey>
            <Arg></Arg>
            <DateTimeRange>
                <StartDate>$formattedStartDate</StartDate>
                <EndDate>$formattedEndDate</EndDate>
            </DateTimeRange>
        </GetAttLog>
    </soap:Body>
</soap:Envelope>
XML;

            $newLine = "\r\n";
            fputs($Connect, "POST /iWsService HTTP/1.1" . $newLine);
            fputs($Connect, "Host: $IP" . $newLine);
            fputs($Connect, "Content-Type: text/xml" . $newLine);
            fputs($Connect, "Content-Length: " . strlen($soap_request) . $newLine . $newLine);
            fputs($Connect, $soap_request . $newLine);
    
            $buffer = "";
            while (!feof($Connect)) {
                $Response = fgets($Connect, 1024);
                if ($Response === false) break;
                $buffer .= $Response;
            }
            fclose($Connect);
    
            if (strpos($buffer, '500 Internal Server Error') !== false) {
                return ["error" => "The server encountered an error while processing the request."];
            }
    
            $buffer = Parse_Data($buffer, "<GetAttLogResponse>", "</GetAttLogResponse>");
            $buffer = explode("\r\n", $buffer);
    
            foreach ($buffer as $line) {
                $data = Parse_Data($line, "<Row>", "</Row>");
                if ($data) {
                    $PIN = Parse_Data($data, "<PIN>", "</PIN>");
                    $DateTime = Parse_Data($data, "<DateTime>", "</DateTime>");
                    $Verified = Parse_Data($data, "<Verified>", "</Verified>");
                    $Status = Parse_Data($data, "<Status>", "</Status>");
            
                    $dataDateTime = date('Y-m-d', strtotime($DateTime));
            
                    if ($dataDateTime >= $startDate && $dataDateTime <= $endDate) {

                        /*$queryPegawai = $this->db->select('absen_pegawai_id, nama_lengkap')
                                          ->from('pegawai')
                                          ->where('absen_pegawai_id', $PIN)
                                          ->get();
            
                        if ($queryPegawai->num_rows() > 0) {
                            $result = $queryPegawai->row_array();
                            $namaPegawai = !empty($result['nama_lengkap']) ? $result['nama_lengkap'] : '-';
                        } else {
                            $namaPegawai = '-';
                        }*/

                        $queryMesin = $this->db->select('ipadress, namamesin')
                                          ->from('mesin')
                                          ->where('ipadress', $IP)
                                          ->get();
                        $result = $queryMesin->row_array();
                        $namaMesin = !empty($result['namamesin']) ? $result['namamesin'] : '-';

                        //$verifikasi = (htmlspecialchars($Verified) == '1') ? 'Finger' : 'Input';
                        //$status = (htmlspecialchars($Status) == '0') ? 'Masuk' : 'Pulang';
                        $verifikasi = htmlspecialchars($Verified);
                        $status = htmlspecialchars($Status);

                        $filteredData[] = [
                            'PIN' => htmlspecialchars($PIN),
                            //'nama_lengkap' => $namaPegawai,
                            'DateTime' => htmlspecialchars($DateTime),
                            'Verified' => $verifikasi,
                            'Status' => $status,
                            'Machine' => $namaMesin
                        ];
                    }
                }
            }                        
        } else {
            return ["error" => "Connection failed: $errstr ($errno)"];
        }
        return $filteredData;        
    }
    
    public function checkConnectionDB($filldata, $arrayDB) {
        $db_config = array(
            'dsn'      => '',
            'hostname' => $arrayDB['host'],
            'port'     => $arrayDB['port'],
            'username' => $arrayDB['user'],
            'password' => $arrayDB['password'],
            'database' => $arrayDB['database'],
            'dbdriver' => 'postgre',
            'dbprefix' => '',
            'pconnect' => FALSE,
            'db_debug' => (ENVIRONMENT !== 'production'),
            'cache_on' => FALSE,
            'cachedir' => '',
            'char_set' => 'utf8',
            'dbcollat' => 'utf8_general_ci',
            'swap_pre' => '',
            'encrypt' => FALSE,
            'compress' => FALSE,
            'strict_on' => FALSE,
            'failover' => array(),
            'save_queries' => TRUE
        );

        try {
            $this->db = $this->load->database($db_config, TRUE);
            if ($this->db->conn_id) {
                $data = $filldata;
                $dbarray = $arrayDB;
                $this->import_Data($data, $arrayDB);
            } else {
                $error = $this->db->error();
                $response = array(
                    'status' => false,
                    'message' => "Gagal gunakan API. Cek koneksi Database: " . $error['message']
                );
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode($response));
            }
        } catch (Exception $e) {
            $response = array(
                'status' => false,
                'message' => "Gagal gunakan API. Cek koneksi Database: " . $e->getMessage()
            );
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($response));
        }
    }

    

    public function import_Data($filldata, $arrayDB) {
        $dc = 0;
        $er = 0;
        $dataCount = 0;
        $dataCount = count($filldata);
        if ($dataCount > 0) {
            $data = $filldata;
            $failedInsertions = [];
            $existingRecordsCount = 0;
    
                $this->db->trans_start();
                try {
                    foreach ($data as $row) {
                        $userID = $row['PIN'];
                        //$namaPegawai = $row['nama_lengkap'];
                        $dateTime = $row['DateTime'];
                        $verified = $row['Verified'];
                        $status = $row['Status'];
                        $machine = $row['Machine'];
                            
                        $this->db->where('absen_id', $userID);
                        //$this->db->where('nama_lengkap', '-');
                        $count = $this->db->count_all_results($arrayDB['table']);
                    
                        /*if ($count > 0) {
                                
                            $this->db->where('absen_id', $userID);
                            $this->db->update($arrayDB['table'], ['nama_lengkap' => $namaPegawai]);
                            
                        } else {*/
                            
                            $this->db->where('absen_id', $userID);
                            $this->db->where('tanggal_absen', $dateTime);
                            $count = $this->db->count_all_results($arrayDB['table']);
                    
                            if ($count == 0) {
                                
                                $data = [
                                    'absen_id' => $userID,
                                    //'nama_lengkap' => $namaPegawai,
                                    'tanggal_absen' => $dateTime,
                                    'verified' => $verified,
                                    'status' => $status,
                                    'ipmesin' => $machine
                                ];
                                if (!$this->db->insert($arrayDB['table'], $data)) {
                                    $failedInsertions[] = [
                                        'absen_id' => $userID,
                                        'dateTime' => $dateTime,
                                        'error' => $this->db->error()['message']
                                    ];
                                }
                            } else {
                                $existingRecordsCount++;
                            }
                        //}
                    }                    
    
                    $this->db->trans_complete();
                } catch (Exception $e) {
                    $this->db->trans_rollback();
                    $failedInsertions[] = [
                        'absen_id' => isset($userID) ? $userID : 'N/A',
                        'dateTime' => isset($dateTime) ? $dateTime : 'N/A',
                        'error' => $e->getMessage()
                    ];
                }
                $dc += $dataCount;
                $er += $existingRecordsCount;
    
                $response = array(
                    'status' => true,
                    'data' => array(
                        'arrayDB' => $arrayDB,
                        'dataCount' => $dc,
                        'existingRecordsCount' => $er,
                        'failedInsertions' => $failedInsertions
                    )
                );
                $this->resetDatabase();
                $this->insertTarikDataLog($arrayDB,$dataCount,$existingRecordsCount);
        } else {
            $response = array(
                'status' => false,
                'message' => "No data to import."
            );
        }
    
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    public function insertTarikDataLog($arrayDB,$dataCount,$existingRecordsCount) {
        $_table = 'tarikdatalog';
        $startDate = date('d-m-Y', strtotime($arrayDB['start_date']));
        $endDate = date('d-m-Y', strtotime($arrayDB['end_date']));
        $insertData = [
            'host' => $arrayDB['host'],
            'ipmesin' => $arrayDB['ip'],
            'jumlahdata' => $dataCount,
            'existsdata' => $existingRecordsCount,
            'tanggaldata' => $startDate . ' to ' . $endDate,
            'created_by' => $this->created_by = $this->session->userdata('user')['id']
        ];
    
        return $this->db->insert($_table, $insertData);
    }

    public function resetDatabase() {
        $db_default = 'default';
        $this->db = $this->load->database($db_default, TRUE);
    }

    private function createSoapResponse($data) {
        $response = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
    <soap:Body>
        <FetchDataResponse xmlns="http://tempuri.org/">
            <FetchDataResult>
XML;

        foreach ($data as $item) {
            $response .= "<Row>
                <PIN>{$item['PIN']}</PIN>
                <DateTime>{$item['DateTime']}</DateTime>
                <Verified>{$item['Verified']}</Verified>
                <Status>{$item['Status']}</Status>
            </Row>";
        }

        $response .= "</FetchDataResult>
        </FetchDataResponse>
    </soap:Body>
</soap:Envelope>";

        return $response;
    }
    
}

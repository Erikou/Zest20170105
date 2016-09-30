<?php

/**
 * Created by IntelliJ IDEA.
 * User: louis aumaitre
 * Date: 23/05/2016
 * Time: 09:56
 */

namespace SE\InputBundle\SAP;

use SE\InputBundle\Entity\SapImports;
use SE\InputBundle\Entity\SAPRF;

class sapConnection
{
    // open and close input file
    private $fileName;
    private $file;

    public function fileOpen($fileName){
        $this->fileName = $fileName;
        $this->file = fopen($fileName, "r");
    }
    public function fileClose(){
        fclose($this->file);
    }

    // extract data from input file
    public $results;

    public function readTable()
    {
    	ini_set('memory_limit', '1000M'); //512M
    	ini_set('max_execution_time', 600); //10min
    	ini_set('display_errors', 1);
    	error_reporting(E_ALL);
    	
        $full_file = fread($this->file, filesize($this->fileName));
        if ($full_file[0] == "|"){
        	$split_data = explode("|", $full_file);  // Split information
        	$col = 26;                              // Total number of columns
        	$nb = sizeof($split_data) / $col; // Number of elements
        	$this->results = array();
        	$this->results[0] = array();
        	for ($i = 0; $i < $nb-1; $i++) {
        		$this->results[0][$i] = array();
        		$this->results[0][$i]['transfer_order'] = $split_data[$i * $col + 1];
        		$this->results[0][$i]['material'] = $split_data[$i * $col + 2];
        		$this->results[0][$i]['date_confirmation'] = $split_data[$i * $col + 3];
        		$this->results[0][$i]['time_confirmation'] = $split_data[$i * $col + 4];
        		$this->results[0][$i]['user'] = $split_data[$i * $col + 5];
        		$this->results[0][$i]['source_storage_type'] = $split_data[$i * $col + 6];
        		$this->results[0][$i]['source_storage_bin'] = $split_data[$i * $col + 7];
        		$this->results[0][$i]['destination_storage_type'] = $split_data[$i * $col + 8];
        		$this->results[0][$i]['destination_storage_bin'] = $split_data[$i * $col + 9];
        		$this->results[0][$i]['storage_location'] = $split_data[$i * $col + 10];
        	}
        } else {
        	$rows = explode("| TO Number|Material |Conf.dt. |Conf.t. |User |Typ|Source Bin|Typ|Dest. Bin |SLoc|WhN|Conf.dt. |ConfTme |Item|MvT|Created |Time |User |C| TR Number|Group |Actual qty|AUn|Delivery | Item|", $full_file);
        	foreach ($rows as $row => $row_data) {
            	if ($row == 0) continue;                // first header, no data here
            	$split_data = explode("|", $row_data);  // Split information
            	$col = 26;                              // Total number of columns
            	$nb = (sizeof($split_data) - 3) / $col; // Number of elements
            	for ($i = 0; $i < $nb; $i++) {
                	$this->results[$row-1][$i]['transfer_order'] = $split_data[$i * $col + 3];
                	$this->results[$row-1][$i]['material'] = $split_data[$i * $col + 4];
                	$this->results[$row-1][$i]['date_confirmation'] = $split_data[$i * $col + 5];
               	 	$this->results[$row-1][$i]['time_confirmation'] = $split_data[$i * $col + 6];
                	$this->results[$row-1][$i]['user'] = $split_data[$i * $col + 7];
                	$this->results[$row-1][$i]['source_storage_type'] = $split_data[$i * $col + 8];
                	$this->results[$row-1][$i]['source_storage_bin'] = $split_data[$i * $col + 9];
                	$this->results[$row-1][$i]['destination_storage_type'] = $split_data[$i * $col + 10];
                	$this->results[$row-1][$i]['destination_storage_bin'] = $split_data[$i * $col + 11];
                	$this->results[$row-1][$i]['storage_location'] = $split_data[$i * $col + 12];
            	}
            }
        }
        // Debug print
        //echo '<pre>'; print_r($this->results); echo '</pre>';
        return $this->results;
    }

    public function displayTable($data){

        for($i=0; $i<sizeof($data[2]) ; $i++){
            echo "<th>".$data[2][$i]['FIELDTEXT']."</th>";
        }
        echo "</tr></thead><tbody>";

        for ($i=0; $i<sizeof($data[1]); $i++){
            echo "<tr>";
            for($j=0; $j<sizeof($data[2]); $j++){
                echo "<td>".$data[1][$i][$j]."</td>";
            }
            echo "</tr>";
        }
    }

    public function dataPersist($em, $date){
    	ini_set('memory_limit', '1000M'); //512M
    	ini_set('max_execution_time', 600); //10min
    	ini_set('display_errors', 1);
    	error_reporting(E_ALL);
    	
    	$inputs = 0;
        for($i=0; $i<sizeof($this->results); $i++) {
            $data = $this->results[$i];
            for ($j = 0; $j < sizeof($data); $j++) {
                $_saprf = new SAPRF;
                $_saprf->setTransferOrder($data[$j]['transfer_order']);
                $_saprf->setMaterial($data[$j]['material']);
                //$_saprf->setDateConfirmation(\DateTime::createFromFormat('d.m.Y', $data[$j]['date_confirmation']));
                //$_saprf->setTimeConfirmation(\DateTime::createFromFormat('H:i:s', $data[$j]['time_confirmation']));
                $_saprf->setUser($data[$j]['user']);
                $_saprf->setSourceStorageType($data[$j]['source_storage_type']);
                $_saprf->setSourceStorageBin($data[$j]['source_storage_bin']);
                $_saprf->setDestinationStorageType($data[$j]['destination_storage_type']);
                $_saprf->setDestinationStorageBin($data[$j]['destination_storage_bin']);
                $_saprf->setDateImport($date);
                $_saprf->setStorageLocation($data[$j]['storage_location']);
                
                $em->persist($_saprf);
                $inputs++;
            }
        }

        $_sapImport = new SapImports();
        $_sapImport->setDate($date);
        $_sapImport->setImport(true);
        $_sapImport->setProcess(false);
       	$_sapImport->setReview(false);
        $_sapImport->setInputs($inputs);
        $em->persist($_sapImport);

        $em->flush();
        return true;
    }

    public function getDate(){
        return new \DateTime("now");
    }

    public function checkImportExist($dateI){
        try {
            $bdd = new PDO('mysql:host=10.211.27.130;dbname=zest;charset=utf8', 'importZest', 'zest@123', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
        } catch (Exception $e) {
            die('Error : ' . $e->getMessage());
        }

        $dupli = $bdd->prepare("SELECT * FROM sapimports WHERE date = ?");
        $dupli->execute(array($dateI));

        if($dupli->rowCount() == 0){
            return false;
        }
        else{
            return true;
        }
    }
}
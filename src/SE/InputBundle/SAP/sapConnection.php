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
        try {
            $full_file = fread($this->file, filesize($this->fileName));
            $rows = explode("| TO Number|Material |Conf.dt. |Conf.t. |User |Typ|Source Bin|Typ|Dest. Bin |SLoc|WhN|Conf.dt. |ConfTme |Item|MvT|Created |Time |User |C| TR Number|Group |Actual qty|AUn|Delivery | Item|", $full_file);
            foreach ($rows as $row => $row_data) {
                if ($row == 0) continue;                // first header, no data here
                $split_data = explode("|", $row_data);  // Split information
                $col = 26;                              // Total number of columns
                $nb = (sizeof($split_data) - 3) / $col;   // Number of elements
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
            // Debug print
            //echo '<pre>'; print_r($this->results); echo '</pre>';
            return $this->results;
        } catch (Exception $e) {
            return array(0 => false, 1 => "Exception type: " . $e . "\n" . "Exception key: " . $e->key . "\n" . "Exception message: " . $e->getMessage() . "\n");
            //return array(0 => false, 1 => "Exception type: ".$e."\n"."Exception key: ".$e->key."\n"."Exception code: ".$e->code."\n"."Exception message: ".$e->getMessage()."\n");
            throw new Exception('The function module failed.');
        }
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

    public function dataPersist($data, $date, $em){

        try
        {
            for($i=0; $i<sizeof($data); $i++){
                $_saprf = new SAPRF;
                $_saprf->setTransferOrder($data[$i]['transfer_order']);
                $_saprf->setMaterial($data[$i]['material']);
                $_saprf->setDateConfirmation(\DateTime::createFromFormat('d.m.Y', $data[$i]['date_confirmation']));
                $_saprf->setTimeConfirmation(\DateTime::createFromFormat('H:i:s', $data[$i]['time_confirmation']));
                $_saprf->setUser($data[$i]['user']);
                $_saprf->setSourceStorageType($data[$i]['source_storage_type']);
                $_saprf->setSourceStorageBin($data[$i]['source_storage_bin']);
                $_saprf->setDestinationStorageType($data[$i]['destination_storage_type']);
                $_saprf->setDestinationStorageBin($data[$i]['destination_storage_bin']);
                $_saprf->setDateImport($date);
                $_saprf->setStorageLocation($data[$i]['storage_location']);

                $em->persist($_saprf);
            }

            $_sapImport = new SapImports();
            $_sapImport->setDate($date);
            $_sapImport->setImport(true);
            $_sapImport->setProcess(false);
            $_sapImport->setReview(false);
            $_sapImport->setInputs(0);

            $em->flush();
            return true;
        }
        catch(Exception $e) //PDOException $e
        {
            echo( "Error" . $e->getMessage());
            return false;
        }
    }

    public function getDate(){
        return new \DateTime("now");
    }

    public function getDataSize($data){
        return sizeof($data[1]);
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

    public function consolidateData($data, $date){
        // prepare array
        $multiArray = array();
        $uniqueArray = array();
        $compareMultiArray = array();
        $compareUniqueArray = array();

        $temp = date_create_from_format('Ymd', $date);
        $temp->modify('+1 day');
        $tomorrow = date_format($temp,'Y-m-d');
        $today = date_create_from_format('Ymd', $date);
        $today = date_format($today,'Y-m-d');

        $time ='073000';
        $time = date_create_from_format('His', $time);
        //$time = $time->format('H:i:s');

        for($i=0; $i<sizeof($data); $i++){
            $cell = split("@",$data[$i]["WA"]);

            $cell[2] = date_create_from_format('Ymd', $cell[2]);
            $cell[2] = $cell[2]->format('Y-m-d');
            $cell[3] = date_create_from_format('His', $cell[3]);


            //if cell date/time included in the shift hours
            if ( ($cell[2] == $today and $cell[3] >= $time) or ($cell[2] == $tomorrow and $cell[3] < $time) ) {

                //format here to compare times
                $cell[3] = $cell[3]->format('H:i:s');

                // add to the prepared arrays
                for($j=0; $j<sizeof($cell); $j++){
                    $multiArray[$i][$j] = $cell[$j];
                    //if cell[5] = "902" then compare by destbin else by sourcebin
                    if($cell[5] != "902"){
                        if($j == 0 or $j == 1 or $j == 4 or $j == 5 or $j == 6){
                            $compareMultiArray[$i][$j] = $cell[$j];
                        }
                    }else{
                        if($j == 0 or $j == 1 or $j == 4 or $j == 7 or $j == 8){
                            $compareMultiArray[$i][$j] = $cell[$j];
                        }
                    }
                }

                //compare arrays with specific fields
                if(!in_array($compareMultiArray[$i], $compareUniqueArray)){
                    //if value not there in $compareuniquearray, add to compare and real array
                    $compareUniqueArray[] = $compareMultiArray[$i];
                    $uniqueArray[] = $multiArray[$i];
                }
            }
        }
        return $uniqueArray;
    }
}
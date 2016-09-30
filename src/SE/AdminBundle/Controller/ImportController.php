<?php

namespace SE\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use SE\InputBundle\Entity\SAPRF;
use SE\InputBundle\Entity\SapImports;
use SE\InputBundle\SAP;
use SE\InputBundle\SAP\sapConnection;
use Symfony\Component\HttpFoundation\Request;


class ImportController extends Controller
{
	private $info;
	
	public function sapnwrfcAction()
	{
    	$connection = new SapConnection();
   		// $connection->setUp();
    	$connection->sapConnect();

    	$em = $this->getDoctrine()->getManager();

    	$res = $connection->readTable();
    	$connection->saveTable($res, $em);
    	$connection->sapClose();

    	$listImport = $this->getDoctrine()
    	    ->getManager()
    	    ->getRepository('SEInputBundle:SAPRF')
    	    ->findAll()
      	;

    	return $this->render('SEAdminBundle:Import:sapnwrfc_import.html.twig', array(
        	'listImport' => $listImport
        ));
	}

    public function sapAction()
    {
        $em = $this->getDoctrine()->getManager();

		// Load all exports from BDD
        $listSapImport = $em->getRepository('SEInputBundle:SapImports')->getAll();
        $importErrors = $em->getRepository('SEInputBundle:InputReview')->getLastMonthImportErrors();

        foreach ($listSapImport as $import) {
            foreach ($importErrors as $error) {
                if ( $import->getDate()->format("Y-m-d") == $error->getDate()->format("Y-m-d")){
                    $error->setStatus(1);
                }
            }
        }
        $em->flush();

        return $this->render('SEAdminBundle:Import:sap_import.html.twig', array(
            'listSapImport' => $listSapImport,
        	'info' => $this->info
            ));
    }
    
    public function confirmAction(Request $request)
    {
    	set_time_limit(600);//ini_set('max_execution_time', 600);
    	//$date = $request->request->all()['dateinput'];
		//if ($date == null){
			$date = new \DateTime("now");
		//}
    	
    	$em = $this->getDoctrine()->getManager();
    	// Get sap exports into BDD
    	$sapCo = new SAP\sapConnection();
    	// FIXME: change path
    	$path = $this->get('kernel')->getRootDir() . '/../import_files/FUSION_TO_'.$date->format('Ymd').'.txt';
    	$sapCo->fileOpen($path);
    	$sapCo->readTable();
    	$sapCo->dataPersist($em, $date);
    	$sapCo->fileClose();
    	
    	$this->info = "";
    	
    	return $this->redirectToRoute('se_admin_import_sap');
    }
    
    public function autoAction()
    {
    	set_time_limit(600);//ini_set('max_execution_time', 600);
		$date = new \DateTime("now");
    	
    	$em = $this->getDoctrine()->getManager();
    	// Get sap exports into BDD
    	$sapCo = new SAP\sapConnection();
    	// FIXME: change path
    	$path = $this->get('kernel')->getRootDir() . '/../import_files/FUSION_TO_'.$date->format('Ymd').'.txt';
    	$sapCo->fileOpen($path);
    	$sapCo->readTable();
    	$sapCo->dataPersist($em, $date);
    	$sapCo->fileClose();
    	
    	$this->info = "";
    	
    	return $this->redirectToRoute('se_admin_import_sap');
    }
    
    public function deleteAction($import_id)
    {
    	$em = $this->getDoctrine()->getManager();
    	$idImport = $import_id;
    
    	$rowsNB=0;
    	$importsNB=0;
    	$inputsNB=0;
    	$entriesNB=0;
    
    	$deleteImport = $em->getRepository('SEInputBundle:SapImports')->findOneBy(array('id' => $idImport));
    	if($deleteImport){
    		$dateImport = $deleteImport->getDate();
    
    		// if duplicate import, take all of them
    		$duplicateImport = $em->getRepository('SEInputBundle:SapImports')->findBy(array('date' => $dateImport));
    		$allSAProws = $em->getRepository('SEInputBundle:SAPRF')->getDayLines($dateImport);
    
    		//delete all that shit
    		foreach ($allSAProws as $r) {
    			$rowsNB+=1;
    			$em->remove($r);
    		}
    		foreach ($duplicateImport as $d) {
    			$importsNB+=1;
    			$em->remove($d);
    		}
    		$resetInputs = $em->getRepository('SEInputBundle:UserInput')->findBy(array('dateInput' => $dateImport));
    		foreach ($resetInputs as $i) {
    			$i->setTotalToInput(0);
    			$i->setManualTo(0);
    			$i->setAutoTo(0);
    			$i->setProcess(0);
    			$inputsNB+=1;
    			foreach ($i->getInputEntries() as $e) {
    				$e->setTotalTo(0);
    				$entriesNB+=1;
    			}
    		}
    
    		$em->flush();
    
    		$this->info = array("code" => 100, "success" => true, "comment" => "Import(s) deleted: ".$importsNB." - SAP Lines deleted: ".$rowsNB." - Inputs resetted: ".$inputsNB." - Entries resetted: ".$entriesNB);
    
    	}else{
    		$this->info = array("code" => 400, "success" => false, "comment" => "Import not found");
    	}

    	return $this->redirectToRoute('se_admin_import_sap');
    }
}
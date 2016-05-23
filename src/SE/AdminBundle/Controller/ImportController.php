<?php

namespace SE\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use SE\InputBundle\Entity\SAPRF;
use SE\InputBundle\Entity\SapImports;
use SE\InputBundle\SAP;


class ImportController extends Controller
{
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

        // Get sap exports into BDD
        $sapCo = new SAP\sapConnection();
        // FIXME: change path
        $path = $this->get('kernel')->getRootDir() . '/testDocs/sap_test_import.txt';
        $sapCo->fileOpen($path);
        $sapCo->readTable();
        $sapCo->dataPersist($sapCo->results[0], $sapCo->getDate(), $em);
        $sapCo->fileClose();


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
            'listSapImport' => $listSapImport
            ));
    }
}
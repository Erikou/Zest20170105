<?php

namespace SE\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class EditorController extends Controller
{
    public function editorAction()
    {
    	$em = $this->getDoctrine()->getManager();
    	$requests = $em->getRepository('SEInputBundle:EditorEntry')->findAll();
        return $this->render('SEAdminBundle:Editor:editor.html.twig', array(
                'requests' => $requests,
            ));
    }

    public function currentEntryAction()
  	{ 
	    $em = $this->getDoctrine()->getManager();
	    $req = $this->get('request');        
	    $current = $req->get('current'); 
	    $request = $req->get('request');
	    
	   	$entry = $em->getRepository('SEInputBundle:InputEntry')->find($current);
	   	$editor = $em->getRepository('SEInputBundle:EditorEntry')->find($request);
		
		$response = array('table' => $this->render('SEAdminBundle:Editor:editor-details.html.twig', array('request' => $editor, 'entry' => $entry))->getContent());
	    
	    return new Response(json_encode($response)); 
	}

    public function acceptAction()
  	{ 
	    $em = $this->getDoctrine()->getManager();
	    $req = $this->get('request');        
	    $requestId = $req->get('id');
  	    $editor = $this->get('se_admin.editor.editor');
	    
	    if($requestId){
	   		$request = $em->getRepository('SEInputBundle:EditorEntry')->find($requestId);
	   		if($request){
	   			$editorType = $request->getEditorType()->getId();

				$response = [];
	   			//addition
	   			if($editorType == 1){$response = $editor->addRequest($request);}
	   			//edition
	   			elseif($editorType == 2){$response = $editor->editRequest($request);}
	   			//deletion
	   			elseif($editorType == 3){$response = $editor->deleteRequest($request);}

	   			if($response['code'] == 100){
	   				$res = $editor->editStatus($requestId, 2);
	   				$response['message'] .= " - ".$res['message'];
	   			}
	   			//[RECALCULATE HERE]
	   			return new JsonResponse($response);
	   		}
	   	}
		return new JsonResponse(array("code" => 400, "success" => false, "message" => "Request was not found"));
	}

    public function rejectAction()
  	{ 
	    $req = $this->get('request');        
	    $request = $req->get('id');

  		$editor = $this->get('se_admin.editor.editor');
	    $response = $editor->editStatus($request, 3);

		return new JsonResponse($response);
	}

    public function ignoreAction()
  	{
	    $req = $this->get('request');        
	    $request = $req->get('id');

  		$editor = $this->get('se_admin.editor.editor');
	    $response = $editor->editStatus($request, 4);

		return new JsonResponse($response);
	}
}

<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\TodoList;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class ListController extends FOSRestController
{
	/*
	 * @return TodoListRepository
	 */
	public function getTodoListRepository(){
		return $this->getDoctrine()->getRepository('AppBundle:TodoList');
	}
	
	/**
	 * @return TodoList
	 * @Rest\Get("api/list")
	 * @ApiDoc(
     *  description="Gets a collection of TodoList",
	 *	output="AppBundle\Entity\TodoList",
	 *	statusCode={
	 *		200 = "Returned when successful"
	 *	}
     * )
	 */
	public function cgetAction()
    {
		$user = $this->get('security.token_storage')->getToken()->getUser();
		return $user->getLists();
    }
	
	/**
	 * @param int $listId
	 * @return mixed
	 * @throws Symfony\Bundle\FrameworkBundle\Controller\Controller\createNotFoundException
	 * @Rest\Get("api/list/{listId}")
	 * @ApiDoc(
     *  description="Get an indvidual TodoList",
	 *	output="AppBundle\Entity\TodoList",
	 *  parameters = {
	 *      { "name" = "id", "dataType"="integer", "required"=true, "description"="list id" }
	 *  },
	 *	statusCode={
	 *		200 = "Returned when successful",
	 *      403 = "Returned when user tried to access list not belongs to him"
	 * 	}
     * )
	 */
	public function getAction(int $listId)
	{
		$list = $this->getTodoListRepository()->find($listId);
		
		$this->denyAccessUnlessGranted('view', $list);
		
		if (!$list) {
			throw $this->createNotFoundException(
				'No list found for id '.$listId
			);
		}
		
		return $list;
	}
	
	/**
	 * @param Request $request
	 * @return mixed
	 * @throws Symfony\Component\HttpKernel\Exception\BadRequestHttpException
	 * @Rest\Post("api/list/create")
	 * @ApiDoc(
     *  description="Create a new List",
     *  output="AppBundle\Entity\TodoList",
	 *  parameters = {
	 *      { "name" = "title", "dataType"="string", "required"=true, "description"="list string" }
	 *  },
	 *	statusCode={
	 *		200 = "Returned when successful",
	 *      400 = "Returned when parameter is missing"
	 * 	}
     * )
	 */
	public function createAction(Request $request)
	{
		$title = $request->get('title');
		
		if(!$title){
			throw new BadRequestHttpException(
				'title required!'
			);
		}
		
		$user = $this->get('security.token_storage')->getToken()->getUser();
		
		$list = new TodoList();
		$list->setTitle($title);
		
		$list->setOwner($user);
		
		$em = $this->getDoctrine()->getManager();
		$em->persist($list);
		$em->flush();
			
		return $list;
	}
	
	/**
	 * @param Request $request, int $id
	 * @return mixed
	 * @throws Symfony\Component\HttpKernel\Exception\BadRequestHttpException
	 * @Rest\Patch("api/list/{id}/edit")
	 * @ApiDoc(
     *  description="Edit an indvidual List",
     *  output="AppBundle\Entity\TodoList",
	 *  parameters = {
	 *      { "name" = "title", "dataType"="string", "required"=true, "description"="list string" }
	 *  },
	 *	statusCode={
	 *		200 = "Returned when successful",
	 *      400 = "Returned when parameter is missing"
	 * 	}
     * )
	 */
	public function patchAction(Request $request, int $id)
	{	
		$list = $this->getAction($id);
		
		$title = $request->get('title');
		
		if(!$title){
			throw new BadRequestHttpException(
				'title required!'
			);
		}

		$list->setTitle($title);
		
		$em = $this->getDoctrine()->getManager();
        $em->flush();
		
		return $list;
	}
	
	/**
	 * @param int $id
	 * @return null
	 * @throws Symfony\Component\HttpKernel\Exception\BadRequestHttpException
	 * @throws Symfony\Bundle\FrameworkBundle\Controller\Controller\createNotFoundException
	 * @Rest\Delete("api/list/{id}/delete")
	 * @ApiDoc(
     *  description="Delete an indvidual List",
	 *  parameters = {
	 *      { "name" = "id", "dataType"="integer", "required"=true, "description"="list id" }
	 *  },
	 *	statusCode={
	 *		204 = "Returned when successful",
	 *      403 = "Returned when user tried to access list not belongs to him"
	 * 	}
     * )
	 */
	public function deleteAction(int $id)
	{
		$list = $this->getAction($id);
		
		$em = $this->getDoctrine()->getManager();
        $em->remove($list);
        $em->flush();
	}
}
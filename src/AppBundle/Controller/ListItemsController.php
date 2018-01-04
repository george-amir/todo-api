<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\TodoList;
use AppBundle\Entity\TodoListItem;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class ListItemsController extends FOSRestController
{
	/*
	 * @return TodoListRepository
	 */
	public function getTodoListRepository(){
		return $this->getDoctrine()->getRepository('AppBundle:TodoList');
	}
	
	/*
	 * @return TodoList
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
	 * @param int $listId, Request $request
	 * @return mixed
	 * @throws Symfony\Component\HttpKernel\Exception\BadRequestHttpException
	 * @Rest\Post("api/list/{listId}/item")
	 * @ApiDoc(
     *  description="Create a new Item",
     *  output="AppBundle\Entity\TodoList",
	 *  parameters = {
	 *      { "name" = "description", "dataType"="string", "required"=true, "description"="item description" }
	 *  },
	 *	statusCode={
	 *		200 = "Returned when successful",
	 *      400 = "Returned when parameter is missing"
	 * 	}
     * )
	 */
	public function createAction(int $listId, Request $request)
	{
		$description = $request->get('description');
		
		if(!$description){
			throw new BadRequestHttpException(
				'description required!'
			);
		}
		
		$list = $this->getAction($listId);
		$item = new TodoListItem();
		$item->setDescription($description);
		$item->setList($list);
		
		$em = $this->getDoctrine()->getManager();
		$em->persist($item);
		$em->flush();
		
		return $list;
	}
	
	/**
	 * @param Request $request, int $listId, int $itemId
	 * @return mixed
	 * @throws Symfony\Component\HttpKernel\Exception\BadRequestHttpException
	 * @throws Symfony\Component\HttpKernel\Exception\NotFoundHttpException
	 * @Rest\Patch("api/list/{listId}/item/{itemId}/edit")
	 * @ApiDoc(
     *  description="Edit an indvidual List item",
     *  output="AppBundle\Entity\TodoList",
	 *  parameters = {
	 *      { "name" = "listId", "dataType"="integer", "required"=true, "description"="list id" },
	 *		{ "name" = "itemId", "dataType"="integer", "required"=true, "description"="item id" },
	 *		{ "name" = "description", "dataType"="string", "required"=false, "description"="item description" },
	 *		{ "name" = "is_completed", "dataType"="boolean", "required"=false, "description"="item id" }
	 *  },
	 *	statusCode={
	 *		200 = "Returned when successful",
	 *      400 = "Returned when parameter is missing",
	 *		404 = "Returned when item not found"
	 * 	}
     * )
	 */
	public function patchAction(int $listId, int $itemId, Request $request)
	{
		$list = $this->getAction($listId);
		$item = $this->getDoctrine()->getRepository('AppBundle:TodoListItem')->find($itemId);
		
		if(!$list->getItems()->contains($item))
		{
			throw new NotFoundHttpException('requested item doesn\'t exist');
		}
		
		if(!$request->request->has('description') && !$request->request->has('is_completed')){
			throw new BadRequestHttpException(
				'description or is_completed is required!'
			);
		}
		
		if($request->request->has('description')){
			$item->setDescription($request->get('description'));
		}
		
		if($request->request->has('is_completed')){
			$item->setIsCompleted($request->get('is_completed'));
		}

		$em = $this->getDoctrine()->getManager();
        $em->flush();
		
		return $list;
	}
	
	/**
	 * @param int $listId, int $itemId
	 * @return null
	 * @throws Symfony\Component\HttpKernel\Exception\BadRequestHttpException
	 * @throws Symfony\Component\HttpKernel\Exception\NotFoundHttpException
	 * @Rest\Delete("api/list/{listId}/item/{itemId}/delete")
	 * @ApiDoc(
     *  description="Delete an indvidual List item",
	 *  parameters = {
	 *      { "name" = "listId", "dataType"="integer", "required"=true, "description"="list id" },
	 *		{ "name" = "itemId", "dataType"="integer", "required"=true, "description"="item id" },
	 *  },
	 *	statusCode={
	 *		204 = "Returned when successful",
	 *      403 = "Returned when user tried to access list not belongs to him"
	 * 	}
     * )
	 */
	public function deleteAction(int $listId, int $itemId)
	{
		$list = $this->getAction($listId);
		$item = $this->getDoctrine()->getRepository('AppBundle:TodoListItem')->find($itemId);
		
		if(!$list->getItems()->contains($item))
		{
			throw new NotFoundHttpException('requested item doesn\'t exist');
		}
		
		$list->getItems()->removeElement($item);
		
		$em = $this->getDoctrine()->getManager();
        $em->remove($item);
		$em->persist($list);
        $em->flush();
	}
}
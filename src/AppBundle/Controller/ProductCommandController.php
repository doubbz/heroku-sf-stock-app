<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Event;
use AppBundle\Event\AddProductStockEvent;
use AppBundle\Event\RemoveProductStockEvent;
use AppBundle\Events;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/product", methods={"POST"})
 */
class ProductCommandController extends Controller
{
    /**
     * @Route("/add-stock")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function addStockAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['productId']) || !isset($data['quantity'])) {
            throw new BadRequestHttpException('Invalid payload');
        }

        /** @var EntityManager $em */
        $em = $this->container->get('doctrine.orm.entity_manager');

        $event = new Event();
        $event->setPayload(json_encode([
            'type' => 'addProductStock',
            'resourceId' => $data['productId'],
            'quantity' => $data['quantity'],
        ]));

        $em->persist($event);

        /** @var EventDispatcher $eventDispatcher */
        $eventDispatcher = $this->container->get('event_dispatcher');
        $eventDispatcher->dispatch(Events::ADD_PRODUCT_STOCK, new AddProductStockEvent($data['productId'], $data['quantity']));

        return new JsonResponse([
            'eventId' => $event->getId(),
        ], Response::HTTP_CREATED);
    }

    /**
     * @Route("/remove-stock")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function removeStockAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['productId']) || !isset($data['quantity'])) {
            throw new BadRequestHttpException('Invalid payload');
        }

        /** @var EntityManager $em */
        $em = $this->container->get('doctrine.orm.entity_manager');

        $event = new Event();
        $event->setPayload(json_encode([
            'type' => 'removeProductStock',
            'resourceId' => $data['productId'],
            'quantity' => $data['quantity'],
        ]));

        $em->persist($event);

        /** @var EventDispatcher $eventDispatcher */
        $eventDispatcher = $this->container->get('event_dispatcher');
        $eventDispatcher->dispatch(Events::REMOVE_PRODUCT_STOCK, new RemoveProductStockEvent($data['productId'], $data['quantity']));

        return new JsonResponse([
            'eventId' => $event->getId(),
        ], Response::HTTP_CREATED);
    }
}
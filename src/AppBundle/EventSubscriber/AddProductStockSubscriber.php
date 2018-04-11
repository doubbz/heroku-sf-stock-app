<?php

namespace AppBundle\EventSubscriber;

use AppBundle\Entity\Product;
use AppBundle\Event\AddProductStockEvent;
use AppBundle\Events;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AddProductStockSubscriber implements EventSubscriberInterface
{
    /** @var  EntityManagerInterface */
    private $em;

    /** @var  EntityRepository */
    private $productRepository;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->productRepository = $em->getRepository(Product::class);
    }

    public static function getSubscribedEvents()
    {
        return [Events::ADD_PRODUCT_STOCK => 'onAddProductStock'];
    }

    public function onAddProductStock(AddProductStockEvent $event)
    {
        /** @var Product $product */
        $productRepo = $this->em->getRepository(Product::class);
        $product = $productRepo->findOneBy(['id' => $event->getProductId()]);

        $product->setStock($product->getStock() + $event->getQuantity());

        $this->em->persist($product);
        $this->em->flush();
    }
}
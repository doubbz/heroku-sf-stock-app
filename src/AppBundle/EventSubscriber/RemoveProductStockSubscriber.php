<?php

namespace AppBundle\EventSubscriber;

use AppBundle\Entity\Product;
use AppBundle\Event\RemoveProductStockEvent;
use AppBundle\Events;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RemoveProductStockSubscriber implements EventSubscriberInterface
{
    /** @var  EntityManagerInterface */
    private $em;

    /** @var  EntityRepository */
    private $productRepository;

    /** @var  ValidatorInterface */
    private $validator;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em, ValidatorInterface $validator)
    {
        $this->em = $em;
        $this->validator = $validator;
        $this->productRepository = $em->getRepository(Product::class);
    }

    public static function getSubscribedEvents()
    {
        return [Events::REMOVE_PRODUCT_STOCK => 'onRemoveProductStock'];
    }

    public function onRemoveProductStock(RemoveProductStockEvent $event)
    {
        /** @var Product $product */
        $product = $this->em->getRepository(Product::class)->findOneBy(['id' => $event->getProductId()]);

        $product->setStock($product->getStock() - $event->getQuantity());

        if (0 != count($violations = $this->validator->validate($product))) {
            throw new BadRequestHttpException('Stock cannot be below zero');
        }

        $this->em->persist($product);
        $this->em->flush();
    }
}
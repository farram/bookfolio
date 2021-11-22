<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    /**
     * @Route("/dashboard/products/{slug}", name="product_show")
     */
    public function showAction(Product $product)
    {
        return $this->render('dashboard/product/show.html.twig', [
            'product' => $product,
        ]);
    }
}

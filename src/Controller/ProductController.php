<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\PaginationService;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/products")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/{page<\d+>?1}", name="products_list", methods={"GET"})
     * @param Request $request
     * @param ProductRepository $repository
     * @param PaginationService $paginationService
     * @return Response
     */
    public function readProducts(Request $request, ProductRepository $repository, PaginationService $paginationService)
    {
        $limit = 10;
        $page = $request->query->get('page');
        $maxPage = $paginationService->getPages($repository, $limit);

        if (is_null($page) || $page < 1) {
            $page = 1;
        } else if ($page > $maxPage) {
            return $this->redirectToRoute('products_list', ['page' => 1], 302);
        }

        return $this->json(
            $paginationService->paginateResults($repository, $page, $limit),
            200, [],
            ['groups' => 'list']);
    }
}

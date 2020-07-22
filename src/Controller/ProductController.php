<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\PaginationService;
use Psr\Cache\InvalidArgumentException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;

/**
 * @Route("/products")
 * @IsGranted("ROLE_USER")
 */
class ProductController extends AbstractController
{
    private int $limit = 10;

    /**
     * Allow a client to view the details of a particular product.
     *
     * @Route("/{id}", name="product_detail", methods={"GET"})
     * @param Product $product
     * @param CacheInterface $cache
     * @return JsonResponse
     * @throws InvalidArgumentException
     */
    public function readProduct(Product $product, CacheInterface $cache)
    {
        $data = $cache->get('product' . $product->getId(), function () use ($product) {
            return $product;
        });

        return $this->json(
            $data,
            200, [],
            ['groups' => 'detail']);
    }

    /**
     * Allow a client to view the list of all the registered products.
     *
     * @Route("/{page<\d+>?1}", name="products_list", methods={"GET"})
     * @param Request $request
     * @param ProductRepository $repository
     * @param PaginationService $paginationService
     * @param CacheInterface $cache
     * @return Response
     * @throws InvalidArgumentException
     */
    public function readProducts(Request $request, ProductRepository $repository, PaginationService $paginationService, CacheInterface $cache)
    {
        $page = $request->query->get('page');
        $maxPage = $paginationService->getPages($repository, $this->limit);

        if (is_null($page) || $page < 1) {
            $page = 1;
        } else if ($page > $maxPage) {
            return $this->redirectToRoute('products_list', ['page' => 1], 302);
        }

        $data = $cache->get('listOfAllProductsPage' . $page, function() use ($page, $repository, $paginationService) {
            return $paginationService->paginateResults($repository, $page, $this->limit);
        });

        return $this->json(
            $data,
            200, [],
            ['groups' => 'list']);
    }
}

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
    private CacheInterface $cache;
    private int $limit = 10;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Allow a client to view the details of a particular product.
     *
     * @Route("/{id}", name="product_detail", methods={"GET"})
     * @param Product $product
     * @return JsonResponse
     * @throws InvalidArgumentException
     */
    public function readProduct(Product $product)
    {
        return $this->json(
            $this->cache->get('product' . $product->getId(),
                function () use ($product) {
                    return $product;
            }),
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
     * @return Response
     * @throws InvalidArgumentException
     */
    public function readProducts(Request $request, ProductRepository $repository, PaginationService $paginationService)
    {
        $page = $request->query->get('page');
        $maxPage = $paginationService->getPages($repository, $this->limit);

        $page = $paginationService->checkPageValue($page, $maxPage);

        return $this->json(
            $this->cache->get('listOfAllProductsPage' . $page,
                function() use ($page, $repository, $paginationService) {
                    return $paginationService->paginateResults($repository, $page, $this->limit);
            }),
            200, [],
            ['groups' => 'list']);
    }
}

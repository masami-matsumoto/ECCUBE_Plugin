<?php
// src/Customize/Controller/PaginatorController.php

namespace Customize\Controller;

use Eccube\Controller\AbstractController;
use Eccube\Entity\Product;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Customize\Entity\News;
use Customize\Entity\NewsCategory;

class PaginatorController extends AbstractController
{
    /**
     * @Method("GET")
     * @Route("/user_data/info", name="user_data_info")
     */
    public function info(Request $request, PaginatorInterface $paginator, EntityManagerInterface $em)
    {
        // カテゴリーIDを取得
        $categoryId = $request->query->get('category');
        $category = null;
        
        if ($categoryId) {
            $category = $em->getRepository(NewsCategory::class)->find($categoryId);
        }
        
        // 検索キーワードを取得
        $keyword = $request->query->get('keyword', '');
        
        // Customized News Repository を使用
        $newsRepository = $em->getRepository(News::class);
        
        if ($keyword) {
            // キーワード検索
            $queryBuilder = $newsRepository->searchNews($keyword, $category);
        } else {
            // 通常の一覧取得
            $queryBuilder = $newsRepository->getNewsQueryBuilder($category);
        }

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            6  // 1ページあたりの表示件数を6に変更
        );

        // カテゴリー一覧を取得
        $categories = $em->getRepository(NewsCategory::class)->getVisibleCategories();

        return $this->render('@user_data/info.twig', [
            'pagination' => $pagination,
            'categories' => $categories,
            'current_category' => $category,
            'keyword' => $keyword,
        ]);
    }

    /**
     * カテゴリー別ニュース一覧
     * @Method("GET")
     * @Route("/user_data/info/category/{id}", name="user_data_info_category", requirements={"id" = "\d+"})
     */
    public function infoByCategory(Request $request, PaginatorInterface $paginator, EntityManagerInterface $em, $id)
    {
        $category = $em->getRepository(NewsCategory::class)->find($id);
        
        if (!$category || !$category->isVisible()) {
            throw $this->createNotFoundException('カテゴリーが見つかりません。');
        }

        $newsRepository = $em->getRepository(News::class);
        $queryBuilder = $newsRepository->getNewsQueryBuilder($category);

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            6
        );

        $categories = $em->getRepository(NewsCategory::class)->getVisibleCategories();

        return $this->render('@user_data/info.twig', [
            'pagination' => $pagination,
            'categories' => $categories,
            'current_category' => $category,
            'keyword' => '',
        ]);
    }

    /**
     * ニュース詳細ページ
     * @Method("GET")
     * @Route("/user_data/info/detail/{id}", name="user_data_info_detail", requirements={"id" = "\d+"})
     */
    public function detail(Request $request, EntityManagerInterface $em, $id)
    {
        $news = $em->getRepository(News::class)->find($id);
        
        if (!$news || !$news->isVisible()) {
            throw $this->createNotFoundException('ニュースが見つかりません。');
        }

        // 関連ニュース（同じカテゴリーの他のニュース）
        $relatedNews = [];
        if ($news->getCategory()) {
            $relatedNews = $em->getRepository(News::class)
                ->getLatestNews(3, $news->getCategory())
                ->filter(function($item) use ($news) {
                    return $item->getId() !== $news->getId();
                })
                ->slice(0, 3);
        }

        return $this->render('@user_data/info_detail.twig', [
            'news' => $news,
            'relatedNews' => $relatedNews,
        ]);
    }
}

// 既に /user_data/info など別のパスが使われていたため、それと区別するために 2 を付けた。
// return $this->render('@user_data/info.twig'  @付くかつかないか
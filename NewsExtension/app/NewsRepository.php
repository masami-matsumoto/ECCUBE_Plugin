<?php

namespace Customize\Repository;

use Eccube\Repository\NewsRepository as BaseNewsRepository;

class NewsRepository extends BaseNewsRepository
{
    // カテゴリで絞り込む検索など、拡張用メソッドをここに追加
    public function findByInformationCategory($category)
    {
        $qb = $this->createQueryBuilder('n')
            ->innerJoin('n.informationCategories', 'c')
            ->where('c = :category')
            ->setParameter('category', $category)
            ->orderBy('n.publish_date', 'DESC');

        return $qb->getQuery()->getResult();
    }
}
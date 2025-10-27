<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Entity\Page;
use App\Model\Enum\PageType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Page>
 */
final class PageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Page::class);
    }

    public function persist(Page $page): void
    {
        $this->getEntityManager()->persist($page);
    }

    public function save(Page $page): void
    {
        $this->getEntityManager()->persist($page);
        $this->getEntityManager()->flush();
    }

    /**
     * Batch saves multiple pages for performance optimization.
     * Uses single flush operation for all pages.
     *
     * @param Page[] $pages
     */
    public function saveBatch(array $pages): void
    {
        $em = $this->getEntityManager();

        foreach ($pages as $page) {
            $em->persist($page);
        }

        $em->flush();
    }

    public function findByShopIdAndType(string $shopId, PageType $type): ?Page
    {
        return $this->createQueryBuilder('p')
            ->where('p.shop = :shopId')
            ->andWhere('p.type = :type')
            ->setParameter('shopId', $shopId)
            ->setParameter('type', $type)
            ->getQuery()
            ->getOneOrNullResult();
    }
}

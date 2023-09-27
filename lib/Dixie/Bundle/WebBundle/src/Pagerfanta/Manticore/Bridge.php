<?php

declare(strict_types=1);

namespace Talav\WebBundle\Pagerfanta\Manticore;

use Doctrine\ORM\EntityManagerInterface;
use Foolz\SphinxQL\Drivers\ResultSetInterface;
use Pagerfanta\Pagerfanta;
class Bridge
{
    /**
     * The results obtained from Manticore.
     */
    private ?array $results = null;

    public function __construct(private EntityManagerInterface $entityManager, private string $entityClass)
    {
    }

    private function extractPksFromResults(): array
    {
        $matches = $this->results['matches'] ?? [];
        $pks = [];

        foreach ($matches as $match) {
            $pks[] = $match['id'];
        }

        return $pks;
    }

    public function setManticoreResult(ResultSetInterface $result, ResultSetInterface $resultMeta): self
    {
        $this->results = [
            'matches' => $result->fetchAllAssoc(),
            'meta' => $this->parseMeta($resultMeta),
        ];

        return $this;
    }

    private function parseMeta(ResultSetInterface $resultMeta): array
    {
        $data = [];

        foreach ($resultMeta as $value) {
            $data[$value['Variable_name']] = $value['Value'];
        }

        return $data;
    }

    public function getPager(): Pagerfanta
    {
        if (null === $this->results) {
            throw new \RuntimeException('You should define manticore results on '.self::class);
        }

        $adapter = new Adapter($this->getResults());
        $adapter->setNbResults($this->results['meta']['total_found'] ?? 0);

        return new Pagerfanta($adapter);
    }

    private function getResults(): array
    {
        $pks = $this->extractPksFromResults();

        $results = [];

        if ($pks) {
            $pkColumn = $this->entityManager->getClassMetadata($this->entityClass)->getSingleIdentifierColumnName();

            $qb = $this->entityManager->createQueryBuilder();
            $q = $qb->select('r')
                ->from($this->entityClass, 'r INDEX BY r.'.$pkColumn)
                ->where($qb->expr()->in('r.'.$pkColumn, $pks))
                // ->addOrderBy('FIELD(r.id,...)', 'ASC')
                ->getQuery();

            $unorderedResults = $q->getResult();

            foreach ($pks as $pk) {
                if (isset($unorderedResults[$pk])) {
                    $results[$pk] = $unorderedResults[$pk];
                }
            }
        }

        return $results;
    }
}

<?php

namespace App\Repository;

use App\Entity\CatalogCategory as Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

class CatalogCategoryRepository extends NestedTreeRepository implements ServiceEntityRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        $entityClass = Category::class;
        $manager = $registry->getManagerForClass($entityClass);

        if ($manager === null) {
            throw new \LogicException(sprintf(
                'Could not find the entity manager for class "%s". Check your Doctrine configuration to make sure it is configured to load this entity’s metadata.',
                $entityClass
            ));
        }

        parent::__construct($manager, $manager->getClassMetadata($entityClass));
    }

    // удалить лишние категории (которых нет в импорте)
    /*
    public function removeNotInArrayCodes(array $codes, bool $flush = true): void
    {
        $categories = $this->getCategoriesNotInArrayCodes($codes);

        foreach ($categories as $category) {
            $this->remove($category, $flush);
        }
    }
    */

    public function getNotInArrayCodes(array $codes): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.code NOT IN (:codes)')
            ->setParameter('codes', $codes)
            ->getQuery()
            ->getResult();
    }


    public function findByProductIdJoinedToCategory(int $productId): array
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.products', 'p', 'WITH', 'p.id = :productId')
            ->setParameter('productId', $productId)
            ->getQuery()
            ->getResult();
    }

    public function getCategoriesByIds(array $categoriesIds = [])
    {
        if (empty($categoriesIds)) {
            return new ArrayCollection();
        }

        $em = $this->getEntityManager();
        $meta = $this->getClassMetadata();
        $config = $this->listener->getConfiguration($em, $meta->name);

        $qb = $em->createQueryBuilder()
            ->select('node')
            ->from($config['useObjectClass'], 'node')
            ->where('node.id IN (:categoriesIds)');

        $qb->setParameter('categoriesIds', $categoriesIds);

        $result = $qb->getQuery()->getResult();
        $result = new ArrayCollection($result);

        return $result;
    }

    public function getCategoriesByCodes(array $categoriesCodes = [])
    {
        if (empty($categoriesCodes)) {
            return new ArrayCollection();
        }

        $em = $this->getEntityManager();
        $meta = $this->getClassMetadata();
        $config = $this->listener->getConfiguration($em, $meta->name);

        $qb = $em->createQueryBuilder()
            ->select('node')
            ->from($config['useObjectClass'], 'node')
            ->where('node.code IN (:categoriesCodes)');

        $qb->setParameter('categoriesCodes', $categoriesCodes);

        $result = $qb->getQuery()->getResult();
        $result = new ArrayCollection($result);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getTreeFromParents(array $parentsIds)
    {
        if (count($parentsIds) === 0) {
            return [];
        }

        $em = $this->getEntityManager();
        $meta = $this->getClassMetadata();
        $config = $this->listener->getConfiguration($em, $meta->name);

        $qb = $em->createQueryBuilder()
            ->select('node')
            ->from($config['useObjectClass'], 'node')
            ->where('node.id IN (:parentsIds) OR node.parent IN (:parentsIds)')
            ->orderBy('node.left');

        $qb->setParameter('parentsIds', $parentsIds);

        $nodes = $qb->getQuery()->getResult();

        return $this->buildTreeNode($nodes);
    }

    public function getFilledTree(Category $root, Collection $categories)
    {
        $parentsIds = [];
        foreach ($categories as $category) {
            $categoryParentsIds = [];
            $path = $this->getPath($category);

            if ($path[0]->getId() === $root->getId()) {
                foreach ($path as $pathItem) {
                    $categoryParentsIds[] = $pathItem->getId();
                }
            }
            $parentsIds = array_merge($parentsIds, $categoryParentsIds);
        }
        $parentsIds = array_unique($parentsIds);

        return $this->getTreeFromParents($parentsIds);
    }

    public function getAllChildrenIds(Category $parent, $includeNode = false)
    {
        $categoryQb = $this->getAllChildrenQueryBuilder($parent, $includeNode);
        $rootAlias = current($categoryQb->getRootAliases());
        $rootEntity = current($categoryQb->getRootEntities());
        $categoryQb->select($rootAlias . '.id');
        $categoryQb->resetDQLPart('from');
        $categoryQb->from($rootEntity, $rootAlias, $rootAlias . '.id');

        return array_keys($categoryQb->getQuery()->execute([], AbstractQuery::HYDRATE_ARRAY));
    }

    public function getAllChildrenCodes(Category $parent, $includeNode = false)
    {
        $categoryQb = $this->getAllChildrenQueryBuilder($parent, $includeNode);
        $rootAlias = current($categoryQb->getRootAliases());
        $rootEntity = current($categoryQb->getRootEntities());
        $categoryQb->select($rootAlias . '.code');
        $categoryQb->resetDQLPart('from');
        $categoryQb->from($rootEntity, $rootAlias, $rootAlias . '.id');

        $categories = $categoryQb->getQuery()->execute(null, AbstractQuery::HYDRATE_SCALAR);
        $codes = [];
        foreach ($categories as $category) {
            $codes[] = $category['code'];
        }

        return $codes;
    }

    public function getCategoryIdsByCodes(array $categoriesCodes)
    {
        if (empty($categoriesCodes)) {
            return [];
        }

        $em = $this->getEntityManager();
        $meta = $this->getClassMetadata();
        $config = $this->listener->getConfiguration($em, $meta->name);

        $qb = $em->createQueryBuilder()
            ->select('node.id')
            ->from($config['useObjectClass'], 'node')
            ->where('node.code IN (:categoriesCodes)');

        $qb->setParameter('categoriesCodes', $categoriesCodes);

        $categories = $qb->getQuery()->execute(null, AbstractQuery::HYDRATE_SCALAR);
        $ids = [];
        foreach ($categories as $category) {
            $ids[] = (int)$category['id'];
        }

        return $ids;
    }

    public function findOneByIdentifier($code)
    {
        return $this->findOneBy(['code' => $code]);
    }

    public function getIdentifierProperties()
    {
        return ['code'];
    }

    public function getChildrenByParentId($parentId)
    {
        $parent = $this->find($parentId);

        return $this->getChildren($parent, true);
    }

    public function getChildrenGrantedByParentId(Category $parent, array $grantedCategoryIds = [])
    {
        return $this->getChildrenQueryBuilder($parent, true)
            ->andWhere('node.id IN (:ids)')
            ->setParameter('ids', $grantedCategoryIds)
            ->getQuery()
            ->getResult();
    }

    public function getChildrenTreeByParentId($parentId, $selectNodeId = false, array $grantedCategoryIds = [])
    {
        $children = [];

        if ($selectNodeId === false) {
            $parent = $this->find($parentId);
            $children = $this->childrenHierarchy($parent);
        } else {
            $selectNode = $this->find($selectNodeId);
            if ($selectNode != null) {
                $em = $this->getEntityManager();
                $meta = $this->getClassMetadata();
                $config = $this->listener->getConfiguration($em, $meta->name);

                $selectPath = $this->getPath($selectNode);
                $parent = $this->find($parentId);
                $qb = $this->getNodesHierarchyQueryBuilder($parent);

                // Remove the node itself from his ancestor
                array_pop($selectPath);

                $ancestorsIds = [];

                foreach ($selectPath as $ancestor) {
                    $ancestorsIds[] = $ancestor->getId();
                }

                $qb->andWhere(
                    $qb->expr()->in('node.' . $config['parent'], $ancestorsIds)
                );

                if (!empty($grantedCategoryIds)) {
                    $qb->andWhere('node.id IN (:ids)')
                        ->setParameter('ids', $grantedCategoryIds);
                }

                $nodes = $qb->getQuery()->getResult();
                $children = $this->buildTreeNode($nodes);
            }
        }

        return $children;
    }

    public function buildTreeNode(array $nodes)
    {
        $vectorMap = [];
        $tree = [];
        $childrenIndex = $this->repoUtils->getChildrenIndex();

        foreach ($nodes as $node) {
            if (!isset($vectorMap[$node->getId()])) {
                // Node does not exist, and none of his children has
                // already been in the loop, so we create it.
                $vectorMap[$node->getId()] = [
                    'item' => $node,
                    $childrenIndex => []
                ];
            } else {
                // Node already existing in the map because a child has been
                // added to his children array. We still need to add the node
                // itself, as only its children property has been created.
                $vectorMap[$node->getId()]['item'] = $node;
            }

            if ($node->getParent() != null) {
                if (!isset($vectorMap[$node->getParent()->getId()])) {
                    // The parent does not exist in the map, create its
                    // children property
                    $vectorMap[$node->getParent()->getId()] = [
                        $childrenIndex => []
                    ];
                }

                $vectorMap[$node->getParent()->getId()][$childrenIndex][] =& $vectorMap[$node->getId()];
            } else {
                $tree[$node->getId()] =& $vectorMap[$node->getId()];
            }
        }

        if (empty($tree)) {
            // No node found with getParent() == null, meaning the absolute tree
            // root was not part of the set. We try to find the lowest level nodes
            // or a node without item part, meaning that it's a referenced parent but without
            // the node present itself in the set
            $nodeIt = 0;
            $foundItemLess = false;
            $nodeIds = array_keys($vectorMap);
            $nodesByLevel = [];

            while ($nodeIt < count($nodeIds) && !$foundItemLess) {
                $nodeId = $nodeIds[$nodeIt];
                $nodeEntry = $vectorMap[$nodeId];

                if (isset($nodeEntry['item'])) {
                    //$nodesByLevel[$nodeEntry['item']->getLevel()][] = $nodeIds[$i];
                } else {
                    $tree =& $vectorMap[$nodeId][$childrenIndex];
                }
                $nodeIt++;
            }
            // $tree still empty there, means we need to pick the lowest level nodes as tree roots
            if (empty($tree)) {
                $lowestLevel = min(array_keys($nodesByLevel));
                foreach ($nodesByLevel[$lowestLevel] as $nodeId) {
                    $tree[$nodeId] =& $vectorMap[$nodeId];
                }
            }
        }

        return $tree;
    }

    public function getTrees()
    {
        return $this->getChildren(null, true, 'created', 'DESC');
    }

    public function getGrantedTrees(array $grantedCategoryIds = [])
    {
        $qb = $this->getChildrenQueryBuilder(null, true, 'created', 'DESC');
        $result = $qb
            ->andWhere('node.id IN (:ids)')
            ->setParameter('ids', $grantedCategoryIds)
            ->getQuery()
            ->getResult();

        return $result;
    }

    public function isAncestor(Category $parentNode, Category $childNode)
    {
        $sameRoot = $parentNode->getRoot() === $childNode->getRoot();

        $isAncestor = $childNode->getLeft() > $parentNode->getLeft()
            && $childNode->getRight() < $parentNode->getRight();

        return $sameRoot && $isAncestor;
    }

    public function getOrderedAndSortedByTreeCategories()
    {
        $queryBuilder = $this->createQueryBuilder('c');
        $queryBuilder = $queryBuilder->orderBy('c.root')->addOrderBy('c.left');

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Shortcut to get all children query builder
     */
    protected function getAllChildrenQueryBuilder(Category $category, $includeNode = false)
    {
        return $this->getChildrenQueryBuilder($category, false, null, 'ASC', $includeNode);
    }
}

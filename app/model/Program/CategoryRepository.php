<?php

namespace App\Model\Program;

use App\Model\User\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Kdyby\Doctrine\EntityRepository;


/**
 * Třída spravující kategorie programových bloků.
 *
 * @author Jan Staněk <jan.stanek@skaut.cz>
 */
class CategoryRepository extends EntityRepository
{
    /**
     * Vrací kategorii podle id.
     * @param $id
     * @return Category|null
     */
    public function findById($id)
    {
        return $this->findOneBy(['id' => $id]);
    }

    /**
     * Vrací kategorie seřazené podle názvu.
     * @return array
     */
    public function findAllOrderedByName()
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.name')
            ->getQuery()
            ->getResult();
    }

    /**
     * Vrací názvy všech kategorií.
     * @return array
     */
    public function findAllNames()
    {
        $names = $this->createQueryBuilder('c')
            ->select('c.name')
            ->getQuery()
            ->getScalarResult();
        return array_map('current', $names);
    }

    /**
     * Vrací názvy kategorií, kromě kategorie s id.
     * @param $id
     * @return array
     */
    public function findOthersNames($id)
    {
        $names = $this->createQueryBuilder('c')
            ->select('c.name')
            ->where('c.id != :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getScalarResult();
        return array_map('current', $names);
    }

    /**
     * Vrací kategorie, ze kterých si uživatel může vybírat programy.
     * @param User $user
     * @return Collection|Category[]
     */
    public function findUserAllowed(User $user): Collection
    {
        $result = $this->createQueryBuilder('c')
            ->join('c.registerableRoles', 'r')
            ->join('r.users', 'u')
            ->where('u = :user')->setParameter('user', $user)
            ->getQuery()
            ->getResult();
        return new ArrayCollection($result);
    }

    /**
     * Vrací kategorie jako možnosti pro select.
     * @return array
     */
    public function getCategoriesOptions()
    {
        $categories = $this->createQueryBuilder('c')
            ->select('c.id, c.name')
            ->orderBy('c.name')
            ->getQuery()
            ->getResult();

        $options = [];
        foreach ($categories as $category) {
            $options[$category['id']] = $category['name'];
        }
        return $options;
    }

    /**
     * Uloží kategorii.
     * @param Category $category
     */
    public function save(Category $category)
    {
        $this->_em->persist($category);
        $this->_em->flush();
    }

    /**
     * Odstraní kategorii.
     * @param Category $category
     */
    public function remove(Category $category)
    {
        foreach ($category->getBlocks() as $block) {
            $block->setCategory(NULL);
            $this->_em->persist($block);
        }

        $this->_em->remove($category);
        $this->_em->flush();
    }
}

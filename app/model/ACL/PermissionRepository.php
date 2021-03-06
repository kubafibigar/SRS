<?php

namespace App\Model\ACL;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Kdyby\Doctrine\EntityRepository;


/**
 * Třída spravující oprávnění.
 *
 * @author Jan Staněk <jan.stanek@skaut.cz>
 */
class PermissionRepository extends EntityRepository
{
    /**
     * Vrací názvy všech oprávnění.
     * @return Collection|string[]
     */
    public function findAllNames(): Collection
    {
        $result = $this->createQueryBuilder('p')
            ->select('p.name')
            ->addSelect('role.name AS roleName')->join('p.roles', 'role')
            ->addSelect('resource.name AS resourceName')->join('p.resource', 'resource')
            ->getQuery()
            ->getResult();
        return new ArrayCollection($result);
    }

    /**
     * Vrací oprávnění podle id.
     * @param int[] $ids
     * @return Collection|Permission[]
     */
    public function findPermissionsByIds(array $ids): Collection
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->in('id', $ids))
            ->orderBy(['name' => 'ASC']);
        return $this->matching($criteria);
    }

    /**
     * Vrací id oprávnění.
     * @param Collection $permissions
     * @return array
     */
    public function findPermissionsIds(Collection $permissions): array
    {
        return $permissions->map(function (Permission $permission) {return $permission->getId();})->toArray();
    }

    /**
     * Vrací oprávnění podle názvu oprávnění a prostředku.
     * @param string $permissionName
     * @param string $resourceName
     * @return Permission|null
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByPermissionAndResourceName(string $permissionName, string $resourceName): ?Permission
    {
        return $this->createQueryBuilder('p')
            ->select('p')
            ->join('p.resource', 'r')
            ->where('p.name = :permissionName')->setParameter('permissionName', $permissionName)
            ->andWhere('r.name = :resourceName')->setParameter('resourceName', $resourceName)
            ->getQuery()
            ->getSingleResult();
    }
}

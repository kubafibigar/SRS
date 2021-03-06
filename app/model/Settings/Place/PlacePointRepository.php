<?php

namespace App\Model\Settings\Place;

use Kdyby\Doctrine\EntityRepository;


/**
 * Třída spravující mapové body.
 *
 * @author Jan Staněk <jan.stanek@skaut.cz>
 */
class PlacePointRepository extends EntityRepository
{
    /**
     * @param $id
     * @return PlacePoint|null
     */
    public function findById($id)
    {
        return $this->findOneBy(['id' => $id]);
    }

    /**
     * @param PlacePoint $placePoint
     */
    public function save(PlacePoint $placePoint)
    {
        $this->_em->persist($placePoint);
        $this->_em->flush();
    }

    /**
     * @param PlacePoint $placePoint
     */
    public function remove(PlacePoint $placePoint)
    {
        $this->_em->remove($placePoint);
        $this->_em->flush();
    }
}

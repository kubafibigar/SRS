<?php

namespace App\WebModule\Components;


/**
 * Factory komponenty s místem.
 *
 * @author Michal Májský
 * @author Jan Staněk <jan.stanek@skaut.cz>
 */
interface IPlaceContentControlFactory
{
    /**
     * @return PlaceContentControl
     */
    public function create();
}

<?php

namespace App\WebModule\Components;


/**
 * Factory komponenty s informací o pořadateli.
 *
 * @author Jan Staněk <jan.stanek@skaut.cz>
 */
interface IOrganizerContentControlFactory
{
    /**
     * @return OrganizerContentControl
     */
    public function create();
}

<?php

namespace App\WebModule\Components;


/**
 * Factory komponenty s textem.
 *
 * @author Michal Májský
 * @author Jan Staněk <jan.stanek@skaut.cz>
 */
interface ITextContentControlFactory
{
    /**
     * @return TextContentControl
     */
    public function create();
}

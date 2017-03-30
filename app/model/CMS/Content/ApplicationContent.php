<?php

namespace App\Model\CMS\Content;

use Doctrine\ORM\Mapping as ORM;


/**
 * Entita obsahu s přihláškou.
 *
 * @author Michal Májský
 * @author Jan Staněk <jan.stanek@skaut.cz>
 * @ORM\Entity
 * @ORM\Table(name="application_content")
 */
class ApplicationContent extends Content implements IContent
{
    protected $type = Content::APPLICATION;
}

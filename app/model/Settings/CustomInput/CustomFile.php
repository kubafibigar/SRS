<?php

namespace App\Model\Settings\CustomInput;

use Doctrine\ORM\Mapping as ORM;


/**
 * Entita vlastní příloha přihlášky.
 *
 * @author Jan Staněk <jan.stanek@skaut.cz>
 * @ORM\Entity
 * @ORM\Table(name="custom_file")
 */
class CustomFile extends CustomInput
{
    /**
     * Adresář pro ukládání souborů.
     */
    const PATH = "/custom_input";

    protected $type = CustomInput::FILE;
}

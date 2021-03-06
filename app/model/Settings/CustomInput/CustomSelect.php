<?php

namespace App\Model\Settings\CustomInput;

use Doctrine\ORM\Mapping as ORM;


/**
 * Entita vlastní výběrové pole přihlášky.
 *
 * @author Jan Staněk <jan.stanek@skaut.cz>
 * @ORM\Entity
 * @ORM\Table(name="custom_select")
 */
class CustomSelect extends CustomInput
{
    protected $type = CustomInput::SELECT;

    /**
     * Možnosti výběrového pole oddělené čárkou.
     * @ORM\Column(type="string")
     * @var string
     */
    protected $options;


    /**
     * @return string
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param string $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }

    /**
     * Vrátí možnosti jako možnosti pro select.
     * @return array
     */
    public function prepareSelectOptions()
    {
        $options = [];

        if (!$this->isMandatory())
            $options[0] = '';

        $optionaArray = explode(', ', $this->options);
        for ($i = 0; $i < count($optionaArray); $i++)
            $options[$i+1] = $optionaArray[$i];

        return $options;
    }
}

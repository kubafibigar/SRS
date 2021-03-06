<?php

namespace App\WebModule\Components;

use Nette\Application\UI\Control;


/**
 * Komponenta s obrázkem.
 *
 * @author Michal Májský
 * @author Jan Staněk <jan.stanek@skaut.cz>
 */
class ImageContentControl extends Control
{
    /**
     * @param $content
     */
    public function render($content)
    {
        $template = $this->template;
        $template->setFile(__DIR__ . '/templates/image_content.latte');

        $template->heading = $content->getHeading();
        $template->image = $content->getImage();
        $template->align = $content->getAlign();
        $template->width = $content->getWidth();
        $template->height = $content->getHeight();

        $template->render();
    }
}

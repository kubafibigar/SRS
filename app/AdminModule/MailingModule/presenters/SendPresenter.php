<?php

namespace App\AdminModule\MailingModule\Presenters;

use App\AdminModule\MailingModule\Forms\SendForm;
use Nette\Forms\Form;


/**
 * Presenter obsluhující rozesílání e-mailu vytvořeného ve formuláři.
 *
 * @author Michal Májský
 * @author Jan Staněk <jan.stanek@skaut.cz>
 */
class SendPresenter extends MailingBasePresenter
{
    /**
     * @var SendForm
     * @inject
     */
    public $sendFormFactory;


    protected function createComponentSendForm()
    {
        $form = $this->sendFormFactory->create();

        $form->onSuccess[] = function (Form $form, \stdClass $values) {
            if ($this->sendFormFactory->mailSuccess)
                $this->flashMessage('admin.mailing.send_sent', 'success');
            else
                $this->flashMessage('admin.mailing.send_error', 'danger');

            $this->redirect('this');
        };

        return $form;
    }
}

<?php

namespace App\WebModule\Forms;

use App\Model\CMS\Faq;
use App\Model\CMS\FaqRepository;
use App\Model\User\User;
use App\Model\User\UserRepository;
use Nette;
use Nette\Application\UI\Form;


/**
 * Formulář pro položení otázky.
 *
 * @author Michal Májský
 * @author Jan Staněk <jan.stanek@skaut.cz>
 */
class FaqForm extends Nette\Object
{
    /**
     * Přihlášený uživatel.
     * @var User
     */
    private $user;

    /** @var BaseForm */
    private $baseFormFactory;

    /** @var FaqRepository */
    private $faqRepository;

    /** @var UserRepository */
    private $userRepository;


    /**
     * FaqForm constructor.
     * @param BaseForm $baseFormFactory
     * @param FaqRepository $faqRepository
     * @param UserRepository $userRepository
     */
    public function __construct(BaseForm $baseFormFactory, FaqRepository $faqRepository, UserRepository $userRepository)
    {
        $this->baseFormFactory = $baseFormFactory;
        $this->faqRepository = $faqRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Vytvoří formulář.
     * @param $id
     * @return Form
     */
    public function create($id)
    {
        $this->user = $this->userRepository->findById($id);

        $form = $this->baseFormFactory->create();

        $form->addTextArea('question', 'web.faq_content.question')
            ->addRule(Form::FILLED, 'web.faq_content.question_empty');

        $form->addSubmit('submit', 'web.faq_content.add_question');

        $form->onSuccess[] = [$this, 'processForm'];

        return $form;
    }

    /**
     * Zpracuje formulář.
     * @param Form $form
     * @param \stdClass $values
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function processForm(Form $form, \stdClass $values)
    {
        $faq = new Faq();

        $faq->setQuestion($values['question']);
        $faq->setAuthor($this->user);

        $this->faqRepository->save($faq);
    }
}

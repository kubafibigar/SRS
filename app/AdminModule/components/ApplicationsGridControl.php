<?php

namespace App\AdminModule\Components;

use App\Model\ACL\RoleRepository;
use App\Model\Enums\ApplicationState;
use App\Model\Enums\PaymentType;
use App\Model\Program\ProgramRepository;
use App\Model\Settings\SettingsRepository;
use App\Model\Structure\SubeventRepository;
use App\Model\User\Application;
use App\Model\User\ApplicationRepository;
use App\Model\User\User;
use App\Model\User\UserRepository;
use App\Services\ApplicationService;
use App\Services\MailService;
use App\Services\PdfExportService;
use App\Services\ProgramService;
use App\Utils\Validators;
use Kdyby\Translation\Translator;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Ublaboo\DataGrid\DataGrid;


/**
 * Komponenta pro správu přihlášek.
 *
 * @author Jan Staněk <jan.stanek@skaut.cz>
 */
class ApplicationsGridControl extends Control
{
    /** @var Translator */
    private $translator;

    /** @var ApplicationRepository */
    private $applicationRepository;

    /** @var UserRepository */
    private $userRepository;

    /** @var RoleRepository */
    private $roleRepository;

    /** @var SubeventRepository */
    private $subeventRepository;

    /** @var ApplicationService */
    private $applicationService;

    /** @var ProgramRepository */
    private $programRepository;

    /** @var MailService */
    private $mailService;

    /** @var SettingsRepository */
    private $settingsRepository;

    /** @var User */
    private $user;

    /** @var PdfExportService */
    private $pdfExportService;

    /** @var ProgramService */
    private $programService;

    /** @var Validators */
    private $validators;


    /**
     * ApplicationsGridControl constructor.
     * @param Translator $translator
     * @param ApplicationRepository $applicationRepository
     * @param UserRepository $userRepository
     * @param RoleRepository $roleRepository
     * @param SubeventRepository $subeventRepository
     * @param ApplicationService $applicationService
     * @param ProgramRepository $programRepository
     * @param MailService $mailService
     * @param SettingsRepository $settingsRepository
     * @param PdfExportService $pdfExportService
     * @param ProgramService $programService
     */
    public function __construct(Translator $translator, ApplicationRepository $applicationRepository,
                                UserRepository $userRepository, RoleRepository $roleRepository,
                                SubeventRepository $subeventRepository, ApplicationService $applicationService,
                                ProgramRepository $programRepository, MailService $mailService,
                                SettingsRepository $settingsRepository, PdfExportService $pdfExportService,
                                ProgramService $programService, Validators $validators)
    {
        parent::__construct();

        $this->translator = $translator;
        $this->applicationRepository = $applicationRepository;
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
        $this->subeventRepository = $subeventRepository;
        $this->applicationService = $applicationService;
        $this->programRepository = $programRepository;
        $this->mailService = $mailService;
        $this->settingsRepository = $settingsRepository;
        $this->pdfExportService = $pdfExportService;
        $this->programService = $programService;
        $this->validators = $validators;
    }

    /**
     * Vykreslí komponentu.
     */
    public function render()
    {
        $this->template->render(__DIR__ . '/templates/applications_grid.latte');
    }

    /**
     * Vytvoří komponentu.
     * @param $name
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Ublaboo\DataGrid\Exception\DataGridException
     */
    public function createComponentApplicationsGrid($name)
    {
        $this->user = $this->userRepository->findById($this->getPresenter()->getParameter('id'));

        $explicitSubeventsExists = $this->subeventRepository->explicitSubeventsExists();

        $grid = new DataGrid($this, $name);
        $grid->setTranslator($this->translator);
        $grid->setDataSource($this->applicationRepository->createQueryBuilder('a')
            ->join('a.user', 'u')
            ->where('u = :user')
            ->andWhere('a.validTo IS NULL')
            ->setParameter('user', $this->user)
            ->orderBy('a.applicationId')
        );
        $grid->setPagination(FALSE);
        $grid->setItemsDetail()
            ->setTemplateParameters(['applicationRepository' => $this->applicationRepository]);
        $grid->setTemplateFile(__DIR__ . '/templates/applications_grid_template.latte');


        $grid->addColumnDateTime('applicationDate', 'admin.users.users_applications_application_date')
            ->setFormat('j. n. Y H:i');

        $grid->addColumnText('roles', 'admin.users.users_applications_roles', 'rolesText');

        $grid->addColumnText('subevents', 'admin.users.users_applications_subevents', 'subeventsText');

        $grid->addColumnNumber('fee', 'admin.users.users_applications_fee');

        $grid->addColumnText('variableSymbol', 'admin.users.users_applications_variable_symbol', 'variableSymbolText');

        $grid->addColumnDateTime('maturityDate', 'admin.users.users_applications_maturity_date')
            ->setFormat('j. n. Y');

        $grid->addColumnText('paymentMethod', 'admin.users.users_applications_payment_method')
            ->setRenderer(function ($row) {
                $paymentMethod = $row->getPaymentMethod();
                if ($paymentMethod)
                    return $this->translator->translate('common.payment.' . $paymentMethod);
                return NULL;
            });

        $grid->addColumnDateTime('paymentDate', 'admin.users.users_applications_payment_date');

        $grid->addColumnDateTime('incomeProofPrintedDate', 'admin.users.users_applications_income_proof_printed_date');

        $grid->addColumnText('state', 'admin.users.users_applications_state')
            ->setRenderer(function ($row) {
                return $this->translator->translate('common.application_state.' . $row->getState());
            });


        if ($explicitSubeventsExists) {
            $grid->addInlineAdd()->onControlAdd[] = function ($container) {
                $container->addMultiSelect('subevents', '',
                    $this->subeventRepository->getNonRegisteredSubeventsOptionsWithCapacity($this->user)
                )
                    ->setAttribute('class', 'datagrid-multiselect')
                    ->addRule(Form::FILLED, 'admin.users.users_applications_subevents_empty');
            };
            $grid->getInlineAdd()->onSubmit[] = [$this, 'add'];
        }

        $grid->addInlineEdit()->onControlAdd[] = function ($container) use ($explicitSubeventsExists) {
            $container->addMultiSelect('subevents', '',
                $this->subeventRepository->getSubeventsOptionsWithCapacity()
            )
                ->setAttribute('class', 'datagrid-multiselect');

            $container->addText('variableSymbol', 'admin.users.users_variable_symbol')
                ->addRule(Form::FILLED, 'admin.users.users_applications_variable_symbol_empty')
                ->addRule(Form::PATTERN, 'admin.users.users_edit_variable_symbol_format', '^\d{1,10}$');

            $paymentMethodSelect = $container->addSelect('paymentMethod', 'admin.users.users_payment_method',
                $this->preparePaymentMethodOptions());

            $paymentDateText = $container->addDatePicker('paymentDate', 'admin.users.users_payment_date');

            $paymentMethodSelect
                ->addConditionOn($paymentDateText, Form::FILLED)
                ->addRule(Form::FILLED, 'admin.users.users_applications_payment_method_empty');

            $container->addDatePicker('incomeProofPrintedDate', 'admin.users.users_income_proof_printed_date');

            $container->addDatePicker('maturityDate', 'admin.users.users_maturity_date');
        };
        $grid->getInlineEdit()->onSetDefaults[] = function ($container, Application $item) {
            $container->setDefaults([
                'subevents' => $this->subeventRepository->findSubeventsIds($item->getSubevents()),
                'variableSymbol' => $item->getVariableSymbolText(),
                'paymentMethod' => $item->getPaymentMethod(),
                'paymentDate' => $item->getPaymentDate(),
                'incomeProofPrintedDate' => $item->getIncomeProofPrintedDate(),
                'maturityDate' => $item->getMaturityDate()
            ]);
        };
        $grid->getInlineEdit()->onSubmit[] = [$this, 'edit'];
        $grid->allowRowsInlineEdit(function(Application $item) {
            return !$item->isCanceled();
        });


        $grid->addAction('generatePaymentProofCash', 'admin.users.users_applications_download_payment_proof_cash');
        $grid->allowRowsAction('generatePaymentProofCash', function ($item) {
            return $item->getState() == ApplicationState::PAID
                && $item->getPaymentMethod() == PaymentType::CASH
                && $item->getPaymentDate();
        });

        $grid->addAction('generatePaymentProofBank', 'admin.users.users_applications_download_payment_proof_bank');
        $grid->allowRowsAction('generatePaymentProofBank', function ($item) {
            return $item->getState() == ApplicationState::PAID
                && $item->getPaymentMethod() == PaymentType::BANK
                && $item->getPaymentDate();
        });

        $grid->addAction('cancelApplication', 'admin.users.users_applications_cancel_application')
            ->addAttributes([
                'data-toggle' => 'confirmation',
                'data-content' => $this->translator->translate('admin.users.users_applications_cancel_application_confirm')
            ])->setClass('btn btn-xs btn-danger');
        $grid->allowRowsAction('cancelApplication', function (Application $item) {
            return $item->getType() == Application::SUBEVENTS && !$item->isCanceled();
        });


        $grid->setColumnsSummary(['fee'], function (Application $item, $column) {
            return $item->isCanceled() ? 0 : $item->getFee();
        });
    }

    /**
     * Zpracuje přidání podakcí.
     * @param $values
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Nette\Application\AbortException
     * @throws \Throwable
     */
    public function add($values)
    {
        $selectedSubevents = $this->subeventRepository->findSubeventsByIds($values['subevents']);

        $p = $this->getPresenter();

        if (!$this->validators->validateSubeventsCapacities($selectedSubevents, $this->user)) {
            $p->flashMessage('admin.users.users_applications_subevents_occupied', 'danger');
            $this->redirect('this');
        }

        if (!$this->validators->validateSubeventsRegistered($selectedSubevents, $this->user)) {
            $p->flashMessage('admin.users.users_applications_subevents_registered', 'danger');
            $this->redirect('this');
        }

        $loggedUser = $this->userRepository->findById($this->getPresenter()->user->id);

        $this->applicationService->addSubeventsApplication($this->user, $selectedSubevents, $loggedUser);

        $p->flashMessage('admin.users.users_applications_saved', 'success');
        $this->redirect('this');
    }

    /**
     * Zpracuje úpravu přihlášky.
     * @param $id
     * @param $values
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Nette\Application\AbortException
     * @throws \Throwable
     */
    public function edit($id, $values)
    {
        $application = $this->applicationRepository->findById($id);

        $selectedSubevents = $this->subeventRepository->findSubeventsByIds($values['subevents']);

        $p = $this->getPresenter();

        if ($application->getType() == Application::ROLES) {
            if (!$selectedSubevents->isEmpty()) {
                $p->flashMessage('admin.users.users_applications_subevents_not_empty', 'danger');
                $this->redirect('this');
            }
        } else {
            if ($selectedSubevents->isEmpty()) {
                $p->flashMessage('admin.users.users_applications_subevents_empty', 'danger');
                $this->redirect('this');
            }
        }

        if (!$this->validators->validateSubeventsCapacities($selectedSubevents, $this->user)) {
            $p->flashMessage('admin.users.users_applications_subevents_occupied', 'danger');
            $this->redirect('this');
        }

        if (!$this->validators->validateSubeventsRegistered($selectedSubevents, $this->user, $application)) {
            $p->flashMessage('admin.users.users_applications_subevents_registered', 'danger');
            $this->redirect('this');
        }

        $loggedUser = $this->userRepository->findById($this->getPresenter()->user->id);

        $this->applicationRepository->getEntityManager()->transactional(function ($em) use ($application, $selectedSubevents, $values, $loggedUser) {
            if ($application->getType() == Application::SUBEVENTS)
                $this->applicationService->updateSubeventsApplication($application, $selectedSubevents, $loggedUser);
            $this->applicationService->updatePayment($application, $values['variableSymbol'],
                $values['paymentMethod'] ?: NULL, $values['paymentDate'],
                $values['incomeProofPrintedDate'], $values['maturityDate'], $loggedUser);
        });

        $p->flashMessage('admin.users.users_applications_saved', 'success');
        $this->redirect('this');
    }

    /**
     * Vygeneruje příjmový pokladní doklad.
     * @param $id
     * @throws \App\Model\Settings\SettingsException
     * @throws \Throwable
     */
    public function handleGeneratePaymentProofCash($id)
    {
        $this->pdfExportService->generateApplicationsPaymentProof(
            $application = $this->applicationRepository->findById($id), "prijmovy-pokladni-doklad.pdf",
            $this->userRepository->findById($this->getPresenter()->getUser()->id)
        );
    }

    /**
     * Vygeneruje potvrzení o přijetí platby.
     * @param $id
     * @throws \App\Model\Settings\SettingsException
     * @throws \Throwable
     */
    public function handleGeneratePaymentProofBank($id)
    {
        $this->pdfExportService->generateApplicationsPaymentProof(
            $application = $this->applicationRepository->findById($id), "potvrzeni-o-prijeti-platby.pdf",
            $this->userRepository->findById($this->getPresenter()->getUser()->id)
        );
    }

    /**
     * Zruší přihlášku.
     * @param $id
     * @throws \Nette\Application\AbortException
     * @throws \Throwable
     */
    public function handleCancelApplication($id)
    {
        $application = $this->applicationRepository->findById($id);

        if ($application->getType() == Application::SUBEVENTS && !$application->isCanceled()) {
            $loggedUser = $this->userRepository->findById($this->getPresenter()->user->id);
            $this->applicationService->cancelSubeventsApplication($application, ApplicationState::CANCELED, $loggedUser);
            $this->getPresenter()->flashMessage('admin.users.users_applications_application_canceled', 'success');
        }

        $this->redirect('this');
    }

    /**
     * Vrátí platební metody jako možnosti pro select.
     * @return array
     */
    private function preparePaymentMethodOptions()
    {
        $options = [];
        $options[''] = '';
        foreach (PaymentType::$types as $type)
            $options[$type] = 'common.payment.' . $type;
        return $options;
    }
}



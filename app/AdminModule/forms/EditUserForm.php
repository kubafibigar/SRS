<?php

namespace App\AdminModule\Forms;

use App\AdminModule\Forms\BaseForm;
use App\Model\ACL\Permission;
use App\Model\ACL\PermissionRepository;
use App\Model\ACL\Resource;
use App\Model\ACL\Role;
use App\Model\ACL\RoleRepository;
use App\Model\CMS\PageRepository;
use App\Model\Enums\PaymentType;
use App\Model\Program\Block;
use App\Model\Program\BlockRepository;
use App\Model\Program\CategoryRepository;
use App\Model\Settings\CustomInput\CustomInput;
use App\Model\Settings\CustomInput\CustomInputRepository;
use App\Model\Settings\Settings;
use App\Model\Settings\SettingsRepository;
use App\Model\User\CustomInputValue\CustomCheckboxValue;
use App\Model\User\CustomInputValue\CustomInputValueRepository;
use App\Model\User\CustomInputValue\CustomTextValue;
use App\Model\User\User;
use App\Model\User\UserRepository;
use Kdyby\Translation\Translator;
use Nette;
use Nette\Application\UI\Form;

class EditUserForm extends Nette\Object
{
    /** @var User */
    private $user;

    /** @var BaseForm */
    private $baseFormFactory;

    /** @var UserRepository */
    private $userRepository;

    /** @var RoleRepository */
    private $roleRepository;

    /** @var CustomInputRepository */
    private $customInputRepository;

    /** @var CustomInputValueRepository */
    private $customInputValueRepository;

    /** @var SettingsRepository */
    private $settingsRepository;

    public function __construct(BaseForm $baseFormFactory, UserRepository $userRepository,
                                RoleRepository $roleRepository, CustomInputRepository $customInputRepository,
                                CustomInputValueRepository $customInputValueRepository,
                                SettingsRepository $settingsRepository)
    {
        $this->baseFormFactory = $baseFormFactory;
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
        $this->customInputRepository = $customInputRepository;
        $this->customInputValueRepository = $customInputValueRepository;
        $this->settingsRepository = $settingsRepository;
    }

    public function create($id)
    {
        $this->user = $this->userRepository->findById($id);

        $form = $this->baseFormFactory->create();

        $form->addHidden('id');

        $rolesSelect = $form->addMultiSelect('roles', 'admin.users.users_roles',
            $this->roleRepository->getRolesWithoutRolesOptionsWithCapacity([Role::GUEST, Role::UNAPPROVED]));

        $approvedCheckbox = $form->addCheckbox('approved', 'admin.users.users_approved_form');

        $rolesSelect
            ->addRule(Form::FILLED, 'admin.users.users_edit_roles_empty')
            ->addRule([$this, 'validateRolesCapacities'], 'admin.users.users_edit_roles_occupied', [$approvedCheckbox])
            ->addRule([$this, 'validateRolesCombination'], 'admin.users.users_edit_roles_nonregistered');

        $form->addCheckbox('attended', 'admin.users.users_attended_form');

        if ($this->user->isPaying()) {
            $form->addText('variableSymbol', 'admin.users.users_variable_symbol')
                ->addRule(Form::FILLED)
                ->addRule(Form::PATTERN, 'admin.users.users_edit_variable_symbol_format', '^\d{8}$');

            $form->addSelect('paymentMethod', 'Platební metoda', $this->preparePaymentMethodOptions());

            $form->addDatePicker('paymentDate', 'admin.users.users_payment_date');

            $form->addDatePicker('incomeProofPrintedDate', 'admin.users.users_income_proof_printed_date');
        }

        if ($this->user->hasDisplayArrivalDepartureRole()) {
            $form->addDateTimePicker('arrival', 'admin.users.users_arrival');

            $form->addDateTimePicker('departure', 'admin.users.users_departure');
        }

        foreach ($this->customInputRepository->findAllOrderedByPosition() as $customInput) {
            $customInputValue = $this->user->getCustomInputValue($customInput);

            switch ($customInput->getType()) {
                case 'text':
                    $customText = $form->addText('custom' . $customInput->getId(), $customInput->getName());
                    if ($customInputValue)
                        $customText->setDefaultValue($customInputValue->getValue());
                    break;

                case 'checkbox':
                    $customCheckbox = $form->addCheckbox('custom' . $customInput->getId(), $customInput->getName());
                    if ($customInputValue)
                        $customCheckbox->setDefaultValue($customInputValue->getValue());
                    break;
            }
        }

        $form->addTextArea('privateNote', 'admin.users.users_private_note');

        $form->addSubmit('submit', 'admin.common.save');

        $form->addSubmit('submitAndContinue', 'admin.common.save_and_continue');


        $form->setDefaults([
            'id' => $id,
            'roles' => $this->roleRepository->findRolesIds($this->user->getRoles()),
            'approved' => $this->user->isApproved(),
            'attended' => $this->user->isAttended(),
            'variableSymbol' => $this->user->getVariableSymbol(),
            'paymentMethod' => $this->user->getPaymentMethod(),
            'paymentDate' => $this->user->getPaymentDate(),
            'incomeProofPrintedDate' => $this->user->getIncomeProofPrintedDate(),
            'arrival' => $this->user->getArrival(),
            'departure' => $this->user->getDeparture(),
            'privateNote' => $this->user->getNote()
        ]);


        $form->onSuccess[] = [$this, 'processForm'];

        return $form;
    }

    public function processForm(Form $form, \stdClass $values) {
        $this->user->setRolesAndRemoveNotAllowedPrograms($this->roleRepository->findRolesByIds($values['roles']));
        $this->user->setApproved($values['approved']);
        $this->user->setAttended($values['attended']);

        if (array_key_exists('variableSymbol', $values)) {
            $this->user->setVariableSymbol($values['variableSymbol']);
        }

        if (array_key_exists('paymentMethod', $values))
            $this->user->setPaymentMethod($values['paymentMethod']);

        if (array_key_exists('paymentDate', $values))
            $this->user->setPaymentDate($values['paymentDate']);

        if (array_key_exists('incomeProofPrintedDate', $values))
            $this->user->setIncomeProofPrintedDate($values['incomeProofPrintedDate']);


        foreach ($this->customInputRepository->findAllOrderedByPosition() as $customInput) {
            $customInputValue = $this->user->getCustomInputValue($customInput);

            if ($customInputValue) {
                $customInputValue->setValue($values['custom' . $customInput->getId()]);
                continue;
            }

            switch ($customInput->getType()) {
                case CustomInput::TEXT:
                    $customInputValue = new CustomTextValue();
                    break;
                case CustomInput::CHECKBOX:
                    $customInputValue = new CustomCheckboxValue();
                    break;
            }
            $customInputValue->setValue($values['custom'. $customInput->getId()]);
            $customInputValue->setUser($this->user);
            $customInputValue->setInput($customInput);
            $this->customInputValueRepository->save($customInputValue);
        }


        if (array_key_exists('arrival', $values))
            $this->user->setArrival($values['arrival']);

        if (array_key_exists('departure', $values))
            $this->user->setDeparture($values['departure']);

        $this->user->setNote($values['privateNote']);

        $this->userRepository->save($this->user);
    }

    private function preparePaymentMethodOptions() {
        $options = [];
        $options[''] = '';
        foreach (PaymentType::$types as $type)
            $options[$type] = 'common.payment.' . $type;
        return $options;
    }

    public function validateRolesCapacities($field, $args)
    {
        $approved = $args[0];
        if ($approved) {
            foreach ($this->roleRepository->findRolesByIds($field->getValue()) as $role) {
                if ($role->hasLimitedCapacity()) {
                    if ($this->roleRepository->countUnoccupiedInRole($role) < 1 && !$this->user->isInRole($role))
                        return false;
                }
            }
        }
        return true;
    }

    public function validateRolesCombination($field, $args)
    {
        $selectedRoles = $this->roleRepository->findRolesByIds($field->getValue());
        $nonregisteredRole = $this->roleRepository->findBySystemName(Role::NONREGISTERED);

        if ($selectedRoles->contains($nonregisteredRole) && $selectedRoles->count() > 1)
            return false;

        return true;
    }
}

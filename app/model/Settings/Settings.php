<?php

namespace App\Model\Settings;

use Doctrine\ORM\Mapping as ORM;


/**
 * Entita nastavení.
 *
 * @author Michal Májský
 * @author Jan Staněk <jan.stanek@skaut.cz>
 * @ORM\Entity(repositoryClass="SettingsRepository")
 * @ORM\Table(name="settings")
 */
class Settings
{
    /**
     * Administrátor byl vytvořen.
     */
    const ADMIN_CREATED = 'admin_created';

    /**
     * Název semináře.
     */
    const SEMINAR_NAME = 'seminar_name';

    /**
     * E-mail semináře.
     */
    const SEMINAR_EMAIL = 'seminar_email';

    /**
     * Neověřený změněný e-mail semináře.
     */
    const SEMINAR_EMAIL_UNVERIFIED = 'seminar_email_unverified';

    /**
     * Ověřovací kód pro změnu e-mailu.
     */
    const SEMINAR_EMAIL_VERIFICATION_CODE = 'seminar_email_verification_code';

    /**
     * Začátek semináře.
     */
    const SEMINAR_FROM_DATE = 'seminar_from_date';

    /**
     * Konec semináře.
     */
    const SEMINAR_TO_DATE = 'seminar_to_date';

    /**
     * Povoleno přidávat programové bloky.
     */
    const IS_ALLOWED_ADD_BLOCK = 'is_allowed_add_block';

    /**
     * Povoleno upravovat harmonogram.
     */
    const IS_ALLOWED_MODIFY_SCHEDULE = 'is_allowed_modify_schedule';

    /**
     * Povoleno přihlašovat se na programy před zaplacením.
     */
    const IS_ALLOWED_REGISTER_PROGRAMS_BEFORE_PAYMENT = 'is_allowed_register_programs_before_payment';

    /**
     * Povoleno přidávání podakcí po zaplacení.
     */
    const IS_ALLOWED_ADD_SUBEVENTS_AFTER_PAYMENT = 'is_allowed_add_subevents_after_payment';

    /**
     * Id propojené skautIS akce.
     */
    const SKAUTIS_EVENT_ID = 'skautis_event_id';

    /**
     * Typ propojené skautIS akce.
     */
    const SKAUTIS_EVENT_TYPE = 'skautis_event_type';

    /**
     * Název propojené skautIS akce.
     */
    const SKAUTIS_EVENT_NAME = 'skautis_event_name';

    /**
     * Adresa obrázku s logem.
     */
    const LOGO = 'logo';

    /**
     * Text patičky.
     */
    const FOOTER = 'footer';

    /**
     * Dodavatel.
     */
    const COMPANY = 'company';

    /**
     * IČO.
     */
    const ICO = 'ico';

    /**
     * Jméno pokladníka.
     */
    const ACCOUNTANT = 'accountant';

    /**
     * Místo vystavení dokladu.
     */
    const PRINT_LOCATION = 'print_location';

    /**
     * Číslo účtu.
     */
    const ACCOUNT_NUMBER = 'account_number';

    /**
     * Předvolba variabilního symbolu. 0-4 číslice před generovaným variabilním symbolem.
     */
    const VARIABLE_SYMBOL_CODE = 'variable_symbol_code';

    /**
     * Typ generování variablního symbolu - datum narození / pořadí přihlášky.
     */
    const VARIABLE_SYMBOL_TYPE = 'variable_symbol_type';

    /**
     * Způsob povolení zápisu na programy.
     */
    const REGISTER_PROGRAMS_TYPE = 'register_programs_type';

    /**
     * Přihlašování na programy od.
     */
    const REGISTER_PROGRAMS_FROM = 'register_programs_from';

    /**
     * Přihlašování na programy do.
     */
    const REGISTER_PROGRAMS_TO = 'register_programs_to';

    /**
     * Odhlášení ze semináře a změna rolí povolena do.
     */
    const EDIT_REGISTRATION_TO = 'edit_registration_to';

    /**
     * Text souhlasu u přihlášky.
     */
    const APPLICATION_AGREEMENT = 'application_agreement';

    /**
     * Stránka, na kterou budou přesměrováni uživatelé po přihlášení, pokud není jinak specifikováno u jejich role.
     */
    const REDIRECT_AFTER_LOGIN = 'redirect_after_login';

    /**
     * Popis místa a cesty.
     */
    const PLACE_DESCRIPTION = 'place_description';

    /**
     * Způsob výpočtu splatnosti.
     */
    const MATURITY_TYPE = 'maturity_type';

    /**
     * Datum splatnosti.
     */
    const MATURITY_DATE = 'maturity_date';

    /**
     * Počet dní pro výpočet splatnosti.
     */
    const MATURITY_DAYS = 'maturity_days';

    /**
     * Počet pracovních dní pro výpočet splatnosti.
     */
    const MATURITY_WORK_DAYS = 'maturity_work_days';

    /**
     * Počet dní, kdy zaslat připomenutí splatnosti.
     */
    const MATURITY_REMINDER = 'maturity_reminder';

    /**
     * Počet dní od splatnosti, kdy zrušit nezaplacené registrace.
     */
    const CANCEL_REGISTRATION_AFTER_MATURITY = 'cancel_registration_after_maturity';

    /**
     * Úprava vlastních polí povolena do.
     */
    const EDIT_CUSTOM_INPUTS_TO = 'edit_custom_inputs_to';


    /**
     * Název položky nastavení.
     * @ORM\Column(type="string", unique=true)
     * @ORM\Id
     * @var string
     */
    protected $item;

    /**
     * Hodnota položky nastavení.
     * @ORM\Column(type="text", nullable=true)
     * @var string
     */
    protected $value;


    /**
     * Settings constructor.
     * @param string $item
     * @param string $value
     */
    public function __construct($item, $value)
    {
        $this->item = $item;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @param string $item
     */
    public function setItem($item)
    {
        $this->item = $item;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }
}


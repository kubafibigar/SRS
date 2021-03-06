<?php

namespace App\Model\Enums;


class MaturityType
{
    /**
     * Neomezená splatnost.
     */
    const UNLIMITED = "unlimited";

    /**
     * Pevné datum splatnosti.
     */
    const DATE = "date";

    /**
     * Splatnost vypočtená podle počtu dní od odeslání přihlášky.
     */
    const DAYS = "days";

    /**
     * Splatnost vypočtená podle počtu pracovních dní od odeslání přihlášky.
     */
    const WORK_DAYS = "work_days";

    public static $types = [
        self::UNLIMITED,
        self::DATE,
        self::DAYS,
        self::WORK_DAYS
    ];
}

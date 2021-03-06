<?php

namespace App\AdminModule\Components;


/**
 * Factory komponenty pro správu uživatelů.
 *
 * @author Jan Staněk <jan.stanek@skaut.cz>
 */
interface IUsersGridControlFactory
{
    /**
     * Vytvoří komponentu.
     * @return UsersGridControl
     */
    public function create();
}

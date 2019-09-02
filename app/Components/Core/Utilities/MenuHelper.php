<?php

namespace App\Components\Core\Utilities;

use App\Components\Core\Menu\MenuManager;
use App\Components\User\Models\User;

class MenuHelper
{
    public static function initMenu()
    {
        /**
         * @var User $currentUser
         */
        $currentUser = \Auth::user();
        $menus = config('menu', []);
        $menuManager = new MenuManager();
        $menuManager->setUser($currentUser);
        $menuManager->addMenus($menus);

        $menus = $menuManager->getFiltered();

        view()->share('nav', $menus);
    }
}

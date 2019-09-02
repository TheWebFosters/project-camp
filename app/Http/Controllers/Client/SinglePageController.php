<?php

namespace App\Http\Controllers\Client;

use App\Components\Core\Menu\MenuItem;
use App\Components\Core\Menu\MenuManager;
use App\Components\User\Models\User;
use App\Http\Controllers\Controller;

class SinglePageController extends Controller
{
    public function displaySPA()
    {
        /**
         * @var User
         */
        $currentUser = \Auth::user();
        $menuManager = new MenuManager();
        $menuManager->setUser($currentUser);

        $menuManager->addMenus([
            new MenuItem([
                'label' => trans('messages.dashboard'),
                'nav_type' => MenuItem::$NAV_TYPE_NAV,
                'icon' => 'dashboard',
                'route_type' => 'vue',
                'route_name' => 'dashboard',
            ]),
        ]);

        $menuManager->addMenus([
            new MenuItem([
                'label' => trans('messages.projects'),
                'nav_type' => MenuItem::$NAV_TYPE_NAV,
                'icon' => 'assessment',
                'route_type' => 'vue',
                'route_name' => 'projects.list',
            ]),
        ]);

        $menuManager->addMenus([
            new MenuItem([
                'label' => trans('messages.tickets'),
                'nav_type' => MenuItem::$NAV_TYPE_NAV,
                'icon' => 'live_help',
                'route_type' => 'vue',
                'route_name' => 'tickets.list',
            ]),
        ]);

        if ($currentUser->can('setting')) {
            $menuManager->addMenus([
                new MenuItem([
                    'label' => trans('messages.settings'),
                    'nav_type' => MenuItem::$NAV_TYPE_NAV,
                    'icon' => 'settings',
                    'route_type' => 'vue',
                    'route_name' => 'settings',
                ]),
            ]);
        }

        $menuManager->addMenus([
            new MenuItem([
                'nav_type' => MenuItem::$NAV_TYPE_DIVIDER,
            ]),
        ]);
        $menus = $menuManager->getFiltered();

        view()->share('nav', $menus);

        return view('layouts.admin');
    }
}

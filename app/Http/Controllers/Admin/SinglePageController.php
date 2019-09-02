<?php

namespace App\Http\Controllers\Admin;

use App\Components\Core\Menu\MenuItem;
use App\Components\Core\Menu\MenuManager;
use App\Components\User\Models\User;

class SinglePageController extends AdminController
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
                'route_name' => 'dashboard.list',
            ]),
        ]);

        if ($currentUser->can('employee.create')) {
            $menuManager->addMenus([
                new MenuItem([
                    'label' => trans('messages.employees'),
                    'nav_type' => MenuItem::$NAV_TYPE_PARENT,
                    'icon' => 'supervised_user_circle',
                    'route_type' => 'vue',
                    'route_name' => '',
                    'children' => [
                        [
                        'label' => trans('messages.employees'),
                        'nav_type' => MenuItem::$NAV_TYPE_PARENT,
                        'icon' => 'person',
                        'route_type' => 'vue',
                        'route_name' => 'users.list',
                        ],
                        [
                        'label' => trans('messages.roles'),
                        'nav_type' => MenuItem::$NAV_TYPE_PARENT,
                        'icon' => 'control_point',
                        'route_type' => 'vue',
                        'route_name' => 'roles.list',
                        ],
                    ],
                ]),
            ]);
        }

        if ($currentUser->can('customer.create')) {
            $menuManager->addMenus([
                new MenuItem([
                    'label' => trans('messages.customers'),
                    'nav_type' => MenuItem::$NAV_TYPE_NAV,
                    'icon' => 'business_center',
                    'route_type' => 'vue',
                    'route_name' => 'customers.list',
                ]),
            ]);
        }

        if ($currentUser->can('customer.create')) {
            $menuManager->addMenus([
                new MenuItem([
                    'label' => trans('messages.leads'),
                    'nav_type' => MenuItem::$NAV_TYPE_NAV,
                    'icon' => 'verified_user',
                    'route_type' => 'vue',
                    'route_name' => 'leads.list',
                ]),
            ]);
        }

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
                'label' => trans('messages.tasks'),
                'nav_type' => MenuItem::$NAV_TYPE_NAV,
                'icon' => 'assignment',
                'route_type' => 'vue',
                'route_name' => 'tasks.list',
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

        if ($currentUser->can('sales.invoices')) {
            $menuManager->addMenus([
                new MenuItem([
                    'label' => trans('messages.sales'),
                    'nav_type' => MenuItem::$NAV_TYPE_PARENT,
                    'icon' => 'shopping_cart',
                    'route_type' => 'vue',
                    'route_name' => '',
                    'children' => [
                        [
                        'label' => trans('messages.invoices'),
                        'nav_type' => MenuItem::$NAV_TYPE_PARENT,
                        'icon' => 'receipt',
                        'route_type' => 'vue',
                        'route_name' => 'invoices.list',
                        ],
                        [
                        'label' => trans('messages.estimates'),
                        'nav_type' => MenuItem::$NAV_TYPE_PARENT,
                        'icon' => 'layers',
                        'route_type' => 'vue',
                        'route_name' => 'estimates.list',
                        ],
                        [
                        'label' => trans('messages.invoice_schemes'),
                        'nav_type' => MenuItem::$NAV_TYPE_PARENT,
                        'icon' => 'money',
                        'route_type' => 'vue',
                        'route_name' => 'invoice_scheme.list',
                        ]
                    ],
                ]),
            ]);
        }

        if ($currentUser->can('expense.create')) {
            $menuManager->addMenus([
                new MenuItem([
                    'label' => trans('messages.expenses'),
                    'nav_type' => MenuItem::$NAV_TYPE_NAV,
                    'icon' => 'remove_circle_outline',
                    'route_type' => 'vue',
                    'route_name' => 'expenses.list',
                ]),
            ]);
        }

        if ($currentUser->can('knowledge_base.view')) {
            $menuManager->addMenus([
                new MenuItem([
                    'label' => trans('messages.knowladge_base'),
                    'nav_type' => MenuItem::$NAV_TYPE_NAV,
                    'icon' => 'ballot',
                    'route_type' => 'vue',
                    'route_name' => 'Knowledgebase.list',
                ]),
            ]);
        }

        if ($currentUser->can('leaves.create')) {
            $menuManager->addMenus([
                new MenuItem([
                    'label' => trans('messages.leaves'),
                    'nav_type' => MenuItem::$NAV_TYPE_NAV,
                    'icon' => 'work_off',
                    'route_type' => 'vue',
                    'route_name' => 'leaves.list',
                ]),
            ]);
        }
        
        $menuManager->addMenus([
            new MenuItem([
                'nav_type' => MenuItem::$NAV_TYPE_DIVIDER,
            ]),
        ]);

        if ($currentUser->can('backup')) {
            $menuManager->addMenus([
                new MenuItem([
                    'label' => trans('messages.backups'),
                    'nav_type' => MenuItem::$NAV_TYPE_NAV,
                    'icon' => 'backup',
                    'route_type' => 'vue',
                    'route_name' => 'backups.list',
                ]),
            ]);
        }

        if ($currentUser->can('setting')) {
            $menuManager->addMenus([
                new MenuItem([
                    'label' => trans('messages.settings'),
                    'nav_type' => MenuItem::$NAV_TYPE_NAV,
                    'icon' => 'settings',
                    'route_type' => 'vue',
                    'route_name' => 'settings.create',
                ]),
            ]);
        }

        $menus = $menuManager->getFiltered();

        view()->share('nav', $menus);

        return view('layouts.admin');
    }
}

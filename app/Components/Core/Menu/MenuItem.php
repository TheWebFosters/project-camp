<?php
namespace App\Components\Core\Menu;

class MenuItem
{
    /**
     * the navigation routing types, whether the nav uses the vue routing or
     * laravel routing
     *
     * @var string
     */
    public static $ROUTING_TYPE_LARAVEL = "laravel";
    public static $ROUTING_TYPE_VUE = "vue";

    /**
     * the navigation types, whether it is a nav link or a divider
     *
     * @var string
     */
    public static $NAV_TYPE_NAV = "nav";
    public static $NAV_TYPE_DIVIDER = "divider";
    public static $NAV_TYPE_PARENT = "parent";

    /**
     * the label of the menu
     *
     * @var string
     */
    public $label = '';

    /**
     * the navigation type
     *
     * @var string
     */
    public $navType = '';

    /**
     * the navigation icon. See https://material.io/icons/
     *
     * @var string
     */
    public $icon = '';

    /**
     * the routing type (laravel || vue)
     *
     * @var string
     */
    public $routeType = '';

    /**
     * the route name defined in laravel routes if this is a route type laravel
     * or vue-router route name if this is a route type vue.
     *
     * @var string
     */
    public $routeName = '';

    /**
     * the array of menu item
     * to create sub menu.
     *
     * @var array
     */
    public $children = [];

    /**
     * MenuItem constructor.
     * @param array $menuData
     */
    public function __construct(array $menuData = [])
    {
        if (!empty($menuData)) {
            if ($menuData['nav_type'] === self::$NAV_TYPE_NAV) {
                $this->label = $menuData['label'];
                $this->navType = $menuData['nav_type'];
                $this->icon = $menuData['icon'];
                $this->routeType = $menuData['route_type'];
                $this->routeName = $menuData['route_name'];
            } elseif ($menuData['nav_type'] === self::$NAV_TYPE_PARENT) {
                $this->label = $menuData['label'];
                $this->navType = $menuData['nav_type'];
                $this->icon = $menuData['icon'];
                $this->routeType = $menuData['route_type'];
                $this->routeName = $menuData['route_name'];
                $this->children = $menuData['children'];
            } else {
                $this->navType = self::$NAV_TYPE_DIVIDER;
            }
        }
    }

    /**
     * check if this menu item is a divider
     *
     * @return bool
     */
    public function isDivider():bool
    {
        return $this->navType === self::$NAV_TYPE_DIVIDER;
    }
}

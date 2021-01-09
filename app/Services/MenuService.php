<?php

namespace App\Services;

use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Support\Facades\Cache;

class MenuService
{
    private $cacheTime = 86400;

    public function render(string $name)
    {
        $menu = Cache::remember('menu_' . $name, $this->cacheTime, function () use ($name) {
            return Menu::where('name', $name)->firstOrFail();
        });

        $items = Cache::remember('items_' . $menu->id, $this->cacheTime, function () use ($menu) {
            return MenuItem::where('menu_id', $menu->id)
                ->orderBY('parent_id', 'asc')
                ->orderBY('order', 'asc')
                ->get();
        });

        $items = $this->setMenuChildrenKey($items);

        $matchUri = $this->getMatchUri($items);

        $items = $this->setMenuItemActive($matchUri, $items);

        $view = ($name == 'admin') ? 'admin.menu.menu_main' : 'menu.menu_bs4';

        return view($view, [
            'items' => $items,
        ]);
    }

    /**
     * Set children key to menu item if that have children items
     * @param $items
     * @return mixed
     */
    public function setMenuChildrenKey($items)
    {
        $arrayItemHaveChildren = [];
        foreach ($items as $item) {
            if ($item->parent_id != 0) {
                $arrayItemHaveChildren[] = $item->parent_id;
            }
        }

        $arrayItemHaveChildren = array_unique($arrayItemHaveChildren);

        foreach ($items as $item) {
            if (in_array($item->id, $arrayItemHaveChildren)) {
                $item->children = true;
            } else {
                $item->children = false;
            }
        }

        return $items;
    }

    /**
     * Order menu items
     * @param array $menuItemsOrder
     * @param $menuItems
     * @param int $parentId
     */
    public function updateMenuOrder(array $menuItemsOrder, $menuItems, int $parentId = 0)
    {
        foreach ($menuItemsOrder as $key => $Item) {
            $item = $menuItems->find($Item->id);
            $item->order = $key + 1;
            $item->parent_id = $parentId;
            $item->save();

            if (isset($Item->children)) {
                $this->updateMenuOrder($Item->children, $menuItems, $item->id);
            }
        }
    }

    /**
     * Get children ids for menu item
     * @param MenuItem $item
     * @return array
     */
    public function getChildrenIds(MenuItem $item)
    {
        $return_ids = [$item->id];
        $menuItems = MenuItem::where('menu_id', $item->menu_id)->get();
        $haveChildren = true;

        while ($haveChildren) {
            $children = $menuItems->whereIn('parent_id', $return_ids);

            if ($children->count() > 0) {
                $newItems = $children->pluck('id')->toArray();
                $return_ids = array_merge($return_ids, $newItems);
                $menuItems = $menuItems->whereNotIn('id', $return_ids);
            } else {
                $haveChildren = false;
            }
        }

        return $return_ids;
    }

    /**
     * Get menu item url accord current uri
     *
     * @param $items
     * @return string
     */
    private function getMatchUri($items)
    {
        $currentUri = request()->path();
        $delimiter = '/';
        $segments = explode($delimiter, $currentUri);

        $matchUri = '';
        $checkUri = '';
        foreach ($segments as $segment) {
            $checkUri .= $delimiter . $segment;

            foreach ($items as $item) {
                if ($checkUri == $item->url) {
                    $matchUri = $item->url;
                }
            }
        }

        return $matchUri;
    }

    /**
     * Add active to menu item
     * @param string $matchUri
     * @param $items
     * @return mixed
     */
    private function setMenuItemActive(string $matchUri, $items)
    {
        foreach ($items as $item) {
            if ($matchUri == $item->url) {
                $item->active = true;
            }
        }

        return $items;
    }
}

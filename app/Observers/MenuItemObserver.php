<?php

namespace App\Observers;

use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Support\Facades\Cache;

class MenuItemObserver
{
    /**
     * Handle the menu item "created" event.
     *
     * @param  \App\MenuItem  $menuItem
     * @return void
     */
    public function created(MenuItem $menuItem)
    {
        $this->resetCacheMenuItems($menuItem);
    }

    /**
     * Handle the menu item "updated" event.
     *
     * @param  \App\MenuItem  $menuItem
     * @return void
     */
    public function updated(MenuItem $menuItem)
    {
        $this->resetCacheMenuItems($menuItem);
    }

    /**
     * Handle the menu item "deleted" event.
     *
     * @param  \App\MenuItem  $menuItem
     * @return void
     */
    public function deleted(MenuItem $menuItem)
    {
        $this->resetCacheMenuItems($menuItem);
    }

    /**
     * Reset menu items from cache
     * @param MenuItem $menuItem
     */
    private function resetCacheMenuItems(MenuItem $menuItem)
    {
        Cache::forget('items_' . $menuItem->menu_id);
    }
}

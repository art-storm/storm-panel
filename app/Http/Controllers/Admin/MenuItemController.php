<?php

namespace App\Http\Controllers\Admin;

use App\Facades\Menu as MenuFacade;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MenuItemRequest;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Traits\AdminCommon;
use Illuminate\Http\Request;

class MenuItemController extends Controller
{
    use AdminCommon;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('admin.auth');
    }

    /**
     * Create menu item
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create(Request $request)
    {
        if ($this->user()->cant('update', Menu::class)) {
            return $this->adminNoAccess();
        }

        $menu_id = (int) $request->menu_id;
        $menu = Menu::findOrFail($menu_id);


        return view('admin.menus_item_create', [
            'menu' => $menu,
            'array_target' => config('menu.menu_target'),
        ]);
    }

    /**
     * Store menu item
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function store(MenuItemRequest $request)
    {
        if ($this->user()->cant('update', Menu::class)) {
            return $this->adminNoAccess();
        }

        $menu_id = (int) $request->menu_id;
        $menu = Menu::findOrFail($menu_id);

        $order = MenuItem::where('menu_id', $menu->id)->where('parent_id', 0)->count() + 1;
        $divider = (isset($request->divider)) ? 1 : 0;

        try {
            // See MenuItemObserver created events for details
            MenuItem::create([
                'menu_id' => $menu->id,
                'title' => $request->title,
                'url' => $request->url,
                'target' => $request->target,
                'icon_class' => $request->icon_class,
                'color' => $request->color,
                'divider' => $divider,
                'order' => $order,
            ]);

            $flash_status = __('common.flash_status.menu_item_add');
        } catch (\Exception $e) {
            report($e);
            $flash_status = __('common.flash_status.menu_item_not_add');
        }

        return redirect()->route('admin.menus.edit', ['menu_id' => $menu->id])->with('status', $flash_status);
    }

    /**
     * Edit menu item
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function edit(Request $request)
    {
        if ($this->user()->cant('update', Menu::class)) {
            return $this->adminNoAccess();
        }

        $item = MenuItem::findOrFail($request->item_id);
        $menu = Menu::findOrFail($item->menu_id);

        return view('admin.menus_item_edit', [
            'item' => $item,
            'menu' => $menu,
            'array_target' => config('menu.menu_target'),
        ]);
    }

    /**
     * Update menu item
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function update(MenuItemRequest $request)
    {
        if ($this->user()->cant('update', Menu::class)) {
            return $this->adminNoAccess();
        }

        $item = MenuItem::findOrFail($request->item_id);
        $menu = Menu::findOrFail($item->menu_id);

        $divider = (isset($request->divider)) ? 1 : 0;

        $item->title = $request->title;
        $item->url = $request->url;
        $item->target = $request->target;
        $item->icon_class = $request->icon_class;
        $item->color = $request->color;
        $item->divider = $divider;

        try {
            // See MenuItemObserver updated events for details
            $item->save();
            $flash_status = __('common.flash_status.menu_item_update');
        } catch (\Exception $e) {
            report($e);
            $flash_status = __('common.flash_status.menu_item_not_update');
        }

        return redirect()->route('admin.menus.edit', ['menu_id' => $menu->id])->with('status', $flash_status);
    }

    /**
     * Destroy menu item
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if ($this->user()->cant('update', Menu::class)) {
            return $this->adminNoAccess();
        }

        $item = MenuItem::findOrFail($request->item_id);
        $menu = Menu::findOrFail($item->menu_id);

        $itemDeleteIds = MenuFacade::getChildrenIds($item);

        try {
            // See MenuItemObserver deleted events for details
            MenuItem::destroy($itemDeleteIds);
            $flash_status = __('common.flash_status.menu_item_delete');
        } catch (\Exception $e) {
            report($e);
            $flash_status = __('common.flash_status.menu_item_not_delete');
        }

        return redirect()->route('admin.menus.edit', ['menu_id' => $menu->id])->with('status', $flash_status);
    }
}

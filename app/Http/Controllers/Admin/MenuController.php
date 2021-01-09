<?php

namespace App\Http\Controllers\Admin;

use App\Facades\Menu as MenuFacade;
use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Traits\AdminCommon;
use Illuminate\Http\Request;

class MenuController extends Controller
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
     * Show menus list
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        if ($this->user()->cant('viewAny', Menu::class)) {
            return $this->adminNoAccess();
        }

        $menus = Menu::orderBy('name', 'asc')->get();

        return view('admin.menus', ['menus' => $menus]);
    }

    /**
     * Edit menu
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function edit(Request $request)
    {
        if ($this->user()->cant('update', Menu::class)) {
            return $this->adminNoAccess();
        }

        $request->menu_id = (int) $request->menu_id;

        $menu = Menu::findOrFail($request->menu_id);
        $items = MenuItem::where('menu_id', $menu->id)
            ->orderBY('parent_id', 'asc')
            ->orderBY('order', 'asc')
            ->get();

        $items = MenuFacade::setMenuChildrenKey($items);

        return view('admin.menus_edit', [
            'menu' => $menu,
            'items' => $items,
        ]);
    }

    /**
     * Update menu
     * @param Request $request
     * @return string
     */
    public function update(Request $request)
    {
        if ($this->user()->cant('update', Menu::class)) {
            return $this->adminNoAccess();
        }

        $request->menu_id = (int) $request->menu_id;
        $menuItemOrder = json_decode($request->order);
        $menuItems = MenuItem::where('menu_id', $request->menu_id)->get();

        MenuFacade::updateMenuOrder($menuItemOrder, $menuItems);

        return __('common.flash_status.menu_update');
    }
}

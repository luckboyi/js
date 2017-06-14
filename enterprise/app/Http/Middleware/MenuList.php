<?php

namespace App\Http\Middleware;

use App\Models\CommonModel;
use Cache;
use Closure;
use Route;

class MenuList
{

    protected $menuList = [];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $routes_name = Route::currentRouteName();
        //var_dump(route("gcomment/edit"));exit;
        $menuArr = $request->session()->get("menus");
        $menuArr = [];

        if (!$menuArr) {

            $menuArr = [];

            $menus = CommonModel::set("Menu")->getAdminMenuList();

            foreach ($menus as $menu) {

                $menuArr["menuTree"][$menu->pid][] = $menu->id;

                $menuArr["map"][$menu->routes_name] = $menu->id;

                $menuArr["menuList"][$menu->id] = [
                    "id"          => $menu->id,
                    "name"        => $menu->name,
                    "routes_name" => $menu->routes_name,
                    "pid"         => $menu->pid,
                    "icon"        => $menu->icon,
                    "level"       => $menu->level,
                ];

            }

            $request->session()->put('menus', $menuArr);

        }

        $this->menuList = isset($menuArr["menuList"]) ? $menuArr["menuList"] : "";

        if (isset($menuArr["map"][$routes_name])) {
            $id = $menuArr["map"][$routes_name];
            $this->activeParent($id);
        }

        view()->share([
            "menuTree" => $menuArr["menuTree"],
            "menuList" => $this->menuList,
        ]);

        return $next($request);
    }

    public function activeParent($id)
    {
        if (isset($this->menuList[$id])) {
            $this->menuList[$id]["current"] = 1;
            $this->activeParent($this->menuList[$id]["pid"]);
        }
    }
}

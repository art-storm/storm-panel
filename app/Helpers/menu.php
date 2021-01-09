<?php

if (!function_exists('menu')) {
    function menu(string $name)
    {
        return \App\Facades\Menu::render($name);
    }
}

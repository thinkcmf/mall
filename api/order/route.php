<?php

use think\facade\Route;

Route::get('order/index$', 'order/Index/index');
Route::get('order/version$', 'order/Index/version');
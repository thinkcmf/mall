<?php

use think\facade\Route;

Route::get('invoice/index$', 'invoice/Index/index');
Route::get('invoice/version$', 'invoice/Index/version');
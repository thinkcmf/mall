<?php

use think\facade\Route;

Route::get('shipment/index$', 'shipment/Index/index');
Route::get('shipment/version$', 'shipment/Index/version');
<?php

use think\facade\Route;

Route::resource('mall/goods', 'mall/Goods', ['only' => ['index', 'read']]);

Route::rule('mall/categories', 'mall/Category/index');
Route::rule('mall/brands', 'mall/Brand/index');


// ###### 购物车：
Route::post('mall/addToCart', 'mall/Cart/edit');
Route::rule('mall/cart/goods', 'mall/Cart/index');  // 购物车列表
Route::post('mall/cart/edit', 'mall/Cart/edit');    // 购物车商品添加、编辑数量
Route::post('mall/cart/set', 'mall/Cart/set');      // 购物车商品删除、选择、选择取消

Route::post('mall/cart/deleteGoods', 'mall/Cart/set?action=delete');
Route::post('mall/cart/selectGoods', 'mall/Cart/set?action=select');
Route::post('mall/cart/cancelSelectGoods', 'mall/Cart/set?action=unselect');

// restful风格：

Route::post  ('mall/cart', 'mall/Cart/edit'); // 购物车商品添加
Route::get   ('mall/cart', 'mall/Cart/index'); // 购物车商品列表
Route::put   ('mall/cart', 'mall/Cart/edit'); // 购物车商品数量编辑、选择、选择取消
Route::delete('mall/cart', 'mall/Cart/set?action=delete');  // 购物车商品删除

Route::post('mall/checkout', 'mall/Cart/checkout');
Route::post('mall/buynow', 'mall/Cart/buynow');
Route::get('mall/pay', 'mall/Payment/pay'); //统一支付
Route::get('mall/trail', 'mall/Logistics/trail'); //物流轨迹


Route::get('my/address', 'mall/my/address');
Route::get('my/addressArea', 'mall/my/addressArea');

Route::get   ('mall/order/count', 'mall/Order/count');
Route::get   ('mall/order/:id', 'mall/Order/read');
Route::get   ('mall/order', 'mall/Order/index');
Route::post  ('mall/order', 'mall/Order/submit');
Route::delete('mall/order/:id', 'mall/Order/cancel');
Route::put('mall/order/received', 'mall/Order/received');


Route::get   ('mall/config', 'mall/Config/index');



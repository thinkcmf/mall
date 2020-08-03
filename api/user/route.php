<?php

use think\facade\Route;

Route::rule('user', 'user/Profile/userInfo');
Route::post('upload/user', 'user/Upload/one?app=user');
Route::post('upload', 'user/Upload/one');

Route::post('login', 'user/Public/login');
Route::post('logout', 'user/Public/logout');
Route::post('register', 'user/Public/register');


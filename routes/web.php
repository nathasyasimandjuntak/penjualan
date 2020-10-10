<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/home_user', 'UsersController@index');
Route::get('/login', 'UsersController@login')->name('login');
Route::post('/loginPost', 'UsersController@loginPost');
Route::get('/register', 'UsersController@register');
Route::post('/registerPost', 'UsersController@registerPost');
Route::get('/logout', 'UsersController@logout');

Route::get('/', function(){ return redirect()->route('login'); });
Route::group(array('prefix'=>'admin'), function(){



  //////////////////////// ROUTE DASHPBOARD ////////////////////////
  Route::group(array('prefix'=>'dashboard'), function(){
    Route::get('/', 'DashboardController@index')->name('dashboard');
  });
//////////////////////// ROUTE PENGGUNA ////////////////////////
  Route::group(array('prefix'=>'pelanggan'), function(){
    Route::get('/', 'PelangganController@index')->name('pelanggan');
    Route::post('/datagrid', 'PelangganController@datagrid')->name('pelangganDatagrid');
    Route::get('/create', 'PelangganController@create')->name('pelangganCreate');
    Route::post('/show', 'PelangganController@show')->name('pelangganShow');
    Route::post('/store', 'PelangganController@store')->name('pelangganStore');
    Route::post('/destroy', 'PelangganController@destroy')->name('pelangganDestroy');
    Route::get('/{edit}/{id}', 'PelangganController@create')->name('pelangganEdit');
  });

  //////////////////////// ROUTE BARANG ////////////////////////
    Route::group(array('prefix'=>'barang'), function(){
      Route::get('/', 'BarangController@index')->name('barang');
      Route::post('/datagrid', 'BarangController@datagrid')->name('barangDatagrid');
      Route::get('/create', 'BarangController@create')->name('barangCreate');
      Route::post('/show', 'BarangController@show')->name('barangShow');
      Route::post('/store', 'BarangController@store')->name('barangStore');
      Route::post('/destroy', 'BarangController@destroy')->name('barangDestroy');
      Route::get('/{edit}/{id}', 'BarangController@create')->name('barangEdit');
    });

    //////////////////////// ROUTE TRANSAKSI ////////////////////////
      Route::group(array('prefix'=>'transaksi'), function(){
        Route::get('/', 'TransaksiController@index')->name('transaksi');
        Route::post('/datagrid', 'TransaksiController@datagrid')->name('transaksiDatagrid');
        Route::get('/create', 'TransaksiController@create')->name('transaksiCreate');
        Route::post('/show', 'TransaksiController@show')->name('transaksiShow');
        Route::post('/store', 'TransaksiController@store')->name('transaksiStore');
        Route::post('/destroy', 'TransaksiController@destroy')->name('transaksiDestroy');
        Route::get('/{edit}/{id}', 'TransaksiController@create')->name('transaksiEdit');
      });

      //////////////////////// ROUTE DETAIL ////////////////////////
        Route::group(array('prefix'=>'detail'), function(){
          Route::get('/', 'DetailController@index')->name('detail');
          Route::post('/datagrid', 'DetailController@datagrid')->name('detailDatagrid');
          Route::get('/create', 'DetailController@create')->name('detailCreate');
          Route::post('/show', 'DetailController@show')->name('detailShow');
          Route::post('/store', 'DetailController@store')->name('detailStore');
          Route::post('/destroy', 'DetailController@destroy')->name('detailDestroy');
          Route::get('/{edit}/{id}', 'DetailController@create')->name('detailEdit');
        });
});

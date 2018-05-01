<?php

// Pages Index
Route::get('/', 'CommitteeController@all');

// Subpage
Route::get('{committee}', 'CommitteeController@view');
Route::get('{committee}/members', 'CommitteeController@members');
Route::get('{committee}/documents', 'CommitteeController@documents');
Route::post('{committee}/documents/{folder?}', 'CommitteeController@figureout')
	->where(['folder' => '.*?[folder/create|file/upload|folder/delete|file/delete|file/approve]']);

Route::get('{committee}/documents/{folder?}/{file}', 'FileController@view')->where(['folder' => '.*\/*file']);
Route::get('{committee}/documents/{folder}', 'FolderController@view')->where(['folder' => '.*']);

Route::post('{committee}/feedback', 'CommitteeController@feedback');

//Webpage
Route::get('/pages/{category}', 'WebpageController@categories')
	->where(['category' => '.*']);
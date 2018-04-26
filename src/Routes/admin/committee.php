<?php

Route::resource('committees', CommitteeController::class);

Route::group(['as' => 'committees.'], function () {
    Route::get('committees/{committee}/folders/{folder}/child', 'FolderController@create')
        ->name('folders.create.child');

    Route::post('committees/file/store/{folder?}', 'FileController@store')
        ->name('file.store');

    Route::get('committees/{committee}/file/{file}/download', 'FileController@download')
        ->name('file.download');

    Route::get('committees/{committee}/file/{file}/edit', 'FileController@edit')
        ->name('file.edit');

    Route::put('committees/{committee}/file/{file}/update', 'FileController@update')
        ->name('file.update');

    Route::post('committees/{committee}/file/{file}/delete', 'FileController@delete')
        ->name('file.delete');

    //Committee folder routes
    Route::get('committees/{committee}/folders/{folder}/edit/parent', 'FolderController@editParent')
        ->name('folder.edit.parent');

    Route::resource('committees/{committee}/folders', FolderController::class);
    Route::resource('committees/{committee}/members', MemberController::class);
    Route::resource('committees/{committee}/webpages', WebpageController::class);
});

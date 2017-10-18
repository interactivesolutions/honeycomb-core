<?php

Route::get('/', function () {
    return hcview('HCCoreUI::welcome');
});

if( config('hc.multiLanguage') ) {
    Route::get('{lang}', function () {
        return hcview('HCCoreUI::welcome');
    });
}
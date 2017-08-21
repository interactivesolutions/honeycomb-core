<?php

Route::get('/', function(){
    return hcview('HCCoreUI::welcome');
});

Route::get('{lang}', function(){
    return hcview('HCCoreUI::welcome');
});

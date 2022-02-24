<?php

Route::post('verify-code', 'SmsController@postSendCode');

Route::get('info', 'SmsController@info');

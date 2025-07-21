<?php

use App\Http\Controllers\MailTestController;

Route::get('/test/email', [MailTestController::class, 'sendTest']);

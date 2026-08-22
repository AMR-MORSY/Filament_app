<?php

namespace App\Actions;

use Exception;

class SlotUnavailableException extends Exception
{
    protected $message='Sorry, this time slot was just taken. Please choose another available time.';
}
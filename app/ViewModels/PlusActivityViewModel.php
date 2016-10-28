<?php

namespace App\ViewModels;

class PlusActivityViewModel
{
    public $id; // When Re-Share, set this into id of activity that being reshared
    public $originalContent;
    public $access;

    public $verb; // post | share (Re-Share)
}
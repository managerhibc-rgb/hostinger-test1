<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class TaskLabel extends Pivot
{
    protected $table = 'task_label';

    public $timestamps = false;
}

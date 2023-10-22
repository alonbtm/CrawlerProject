<?php

namespace App\Models;

//use mongodb eloquent model
// use Illuminate\Database\Eloquent\Model;
use MongoDB\Laravel\Eloquent\Model;

class Page extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'pages';
    
    protected $fillable = ['url', 'content', 'depth', 'parent'];
}

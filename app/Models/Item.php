<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Item extends Model
{
    use HasFactory;

    //Fillable means what columns in the table are allowed to be inserted
    protected $fillable =['name','price','status'];


    //a relationship that indicates that a user can have nultiple items
    public function user(){

        return $this->belongsTo(User::class);
    }




}

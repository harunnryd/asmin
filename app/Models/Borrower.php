<?php namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;

class Borrower extends Model 
{
    use Uuid;
    public $incrementing = false;
    protected $fillable = [
        'first_name', 
        'last_name', 
        'nickname', 
        'address', 
        'date_of_birth', 
        'place_of_birth'
    ];

    public function loanRequests() {
        return $this->hasMany(LoanRequest::class);
    }

    public function repayments() {
        return $this->hasMany(Repayment::class);
    }
}
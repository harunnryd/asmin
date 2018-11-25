<?php namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;

class LoanRequest extends Model 
{
    use Uuid;
    public $incrementing = false;
    protected $fillable = [
        'borrower_id', 
        'request_at',
        'status', 
        'deadline_at', 
        'payday_at', 
        'duration', 
        'repayment_frequency', 
        'amount', 
        'interest_rate', 
        'arrangement_fee', 
        'description'
    ];

    public function borrower() {
        return $this->belongsTo(Borrower::class);
    }

    public function repayments() {
        return $this->hasMany(Repayment::class);
    }
}
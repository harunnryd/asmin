<?php namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;

class Repayment extends Model
{
    use Uuid;
    public $incrementing = false;
    protected $fillable = [
        'loan_request_id',
        'borrower_id',
        'status',
        'amount',
        'date_at',
        'deadline_at'
    ];

    public function borrower() {
        return $this->belongsTo(Borrower::class);
    }

    public function loanRequest() {
        return $this->belongsTo(LoanRequest::class);
    }
}
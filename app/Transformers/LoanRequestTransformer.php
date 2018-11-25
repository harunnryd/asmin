<?php namespace App\Transformers;

use App\Models\LoanRequest;
use League\Fractal\TransformerAbstract;

class LoanRequestTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'borrower',
        'repayments'
    ];

    public function transform(LoanRequest $loanRequest) {
        return [
            'id' => $loanRequest->id,
            'borrower_id' => $loanRequest->borrower_id, 
            'request_at' => $loanRequest->request_at, 
            'deadline_at' => $loanRequest->deadline_at, 
            'payday_at' => $loanRequest->payday_at, 
            'duration' => $loanRequest->duration, 
            'status' => $loanRequest->status,
            'repayment_frequency' => $loanRequest->repayment_frequency, 
            'amount' => $loanRequest->amount, 
            'interest_rate' => $loanRequest->interest_rate, 
            'arrangement_fee' => $loanRequest->arrangement_fee, 
            'description' => $loanRequest->description,
            'created_at' => $loanRequest->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $loanRequest->updated_at->format('Y-m-d H:i:s')
        ];
    }

    public function includeBorrower(LoanRequest $loanRequest) {
        $borrower = $loanRequest->borrower;
        return $this->item($borrower, new BorrowerTransformer);
    }

    public function includeRepayments(LoanRequest $loanRequest) {
        $repayments = $loanRequest->repayments;
        return $this->collection($repayments, new RepaymentTransformer);
    }
}
<?php namespace App\Transformers;

use App\Models\Repayment;
use League\Fractal\TransformerAbstract;

class RepaymentTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'loan_request',
        'borrower_id'
    ];

    public function transform(Repayment $repayment) {
        return [
            'id' => $repayment->id,
            'loan_request_id' => $repayment->loan_request_id, 
            'borrower_id' => $repayment->borrower_id, 
            'status' => $repayment->status, 
            'amount' => $repayment->amount, 
            'date_at' => $repayment->date_at, 
            'deadline_at' => $repayment->deadline_at,
            'created_at' => $repayment->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $repayment->updated_at->format('Y-m-d H:i:s')
        ];
    }

    public function includeLoanRequest(Repayment $repayment) {
        $loanRequest = $repayment->loanRequest;
        return $this->item($loanRequest, new LoanRequestTransformer);
    }

    public function includeBorrower(Repayment $repayment) {
        $borrower = $repayment->borrower;
        return $this->item($borrower, new BorrowerTransformer);
    }
}
<?php namespace App\Transformers;

use App\Models\Borrower;
use League\Fractal\TransformerAbstract;

class BorrowerTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'loan_requests',
        'repayments'
    ];

    public function transform(Borrower $borrower) {
        return [
            'id' => $borrower->id,
            'first_name' => $borrower->first_name, 
            'last_name' => $borrower->last_name, 
            'nickname' => $borrower->nickname, 
            'address' => $borrower->address, 
            'date_of_birth' => $borrower->date_of_birth, 
            'place_of_birth' => $borrower->place_of_birth,
            'created_at' => $borrower->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $borrower->updated_at->format('Y-m-d H:i:s')
        ];
    }

    public function includeLoanRequests(Borrower $borrower) {
        $loanRequests = $borrower->loanRequests;
        return $this->collection($loanRequests, new LoanRequestTransformer);
    }

    public function includeRepayments(Borrower $borrower) {
        $repayments = $borrower->repayments;
        return $this->collection($repayments, new RepaymentTransformer);
    }
}
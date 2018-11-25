<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Repayment;
use App\Models\LoanRequest;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid as Generator;
use App\Transformers\LoanRequestTransformer;

class LoanRequestController extends ApiController
{
    public function index() {
        $loanRequests = LoanRequest::take(10)->get();
        return $this->respondWithCollection($loanRequests, new LoanRequestTransformer);
    }

    public function show($id) {
        $loanRequest = LoanRequest::find($id);
        if (!$loanRequest) { return $this->errorNotFound(); }

        return $this->respondWithItem($loanRequest, new LoanRequestTransformer);
    }

    public function create(Request $request) {
        $this->validate($request, [
            'borrower_id' => 'required|exists:borrowers,id|max:36',
            'deadline_at' => 'required|date',
            'payday_at' => 'required|date',
            'duration' => 'required|in:3,6,12',
            'repayment_frequency' => 'required|in:1',
            'amount' => 'required',
            'interest_rate' => 'required',
            'arrangement_fee' => 'required',
            'description' => 'required'
        ]);

        $loanRequest = LoanRequest::create([
            'borrower_id' => $request->borrower_id,
            'request_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'deadline_at' => $request->deadline_at,
            'payday_at' => $request->payday_at,
            'duration' => $request->duration,
            'status' => 'unapproved',
            'repayment_frequency' => $request->repayment_frequency,
            'amount' => $request->amount,
            'interest_rate' => $request->interest_rate,
            'arrangement_fee' => $request->arrangement_fee,
            'description' => $request->description
        ]);

        return $this->respondWithItem($loanRequest, new LoanRequestTransformer);
    }

    public function approved(Request $request, $id) {
        $loanRequest = LoanRequest::find($id);
        if (!$loanRequest) { return $this->errorNotFound(); }
        if ($loanRequest->status == 'approved') { return $this->errorInternalError('Loan already been approved'); }

        $repayments = []; 
        $installment = ($loanRequest->amount * (100 + $loanRequest->duration) / 100) / $loanRequest->duration; 
        for ($i = 0; $i < $loanRequest->duration; $i++) {
            array_push($repayments, [
                'id' => Generator::uuid4()->toString(),
                'loan_request_id' => $loanRequest->id,
                'borrower_id' => $loanRequest->borrower_id,
                'status' => 'unpaid',
                'amount' => $installment,
                'date_at' => Carbon::now()->addMonth($i),
                'deadline_at' => Carbon::now()->addMonth($i)->addDay(7),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        }

        Repayment::insert($repayments);
        $loanRequest->update(['status' => 'approved']);

        return $this->respondWithItem($loanRequest, new LoanRequestTransformer);
    }

    public function edit(Request $request, $id) {
        $this->validate($request, [
            'borrower_id' => 'required|exists:borrowers,id|max:36',
            'deadline_at' => 'required|date',
            'payday_at' => 'required|date',
            'duration' => 'required|in:3,6,12',
            'repayment_frequency' => 'required|in:1',
            'amount' => 'required',
            'interest_rate' => 'required',
            'arrangement_fee' => 'required',
            'description' => 'required'
        ]);

        $loanRequest = LoanRequest::find($id);
        if (!$loanRequest) { return $this->errorNotFound(); }
        if ($loanRequest->status == 'approved') { return $this->errorInternalError('Ups, Loan cannot editable while approved'); }

        $loanRequest->update([
            'borrower_id' => $request->borrower_id,
            'request_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'deadline_at' => $request->deadline_at,
            'payday_at' => $request->payday_at,
            'duration' => $request->duration,
            'repayment_frequency' => $request->repayment_frequency,
            'amount' => $request->amount,
            'interest_rate' => $request->interest_rate,
            'arrangement_fee' => $request->arrangement_fee,
            'description' => $request->description
        ]);

        return $this->respondWithItem($loanRequest, new LoanRequestTransformer);
    }

    public function destroy($id) {
        $loanRequest = LoanRequest::find($id);
        if (!$loanRequest) { return $this->errorNotFound(); }
        if ($loanRequest->status == 'approved') { return $this->errorInternalError('Ups, Loan cannot deletable while approved'); }
        $loanRequest->delete();

        return $this->respondWithItem($loanRequest, new LoanRequestTransformer);
    }
}

<?php namespace App\Http\Controllers;

use App\Models\Borrower;
use Illuminate\Http\Request;
use App\Transformers\BorrowerTransformer;

class BorrowerController extends ApiController
{
    public function index() {
        $borrowers = Borrower::take(10)->get();
        return $this->respondWithCollection($borrowers, new BorrowerTransformer);
    }

    public function create(Request $request) {
        $this->validate($request, [
            'first_name' => 'required|max:30',
            'last_name' => 'required|max:30',
            'nickname' => 'required|max:30',
            'address' => 'required|max:255',
            'date_of_birth' => 'required|date',
            'place_of_birth' => 'required'
        ]);

        $borrower = Borrower::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'nickname' => $request->nickname,
            'address' => $request->address,
            'date_of_birth' => $request->date_of_birth,
            'place_of_birth' => $request->place_of_birth
        ]);

        return $this->respondWithItem($borrower, new BorrowerTransformer);
    }

    public function show($id) {
        $borrower = Borrower::find($id);
        if (!$borrower) { return $this->errorNotFound(); }
        return $this->respondWithItem($borrower, new BorrowerTransformer);
    }

    public function edit(Request $request, $id) {
        $this->validate($request, [
            'first_name' => 'required',
            'last_name' => 'required',
            'nickname' => 'required',
            'address' => 'required',
            'date_of_birth' => 'required|date',
            'place_of_birth' => 'required'
        ]);

        $borrower = Borrower::find($id);
        if (!$borrower) { return $this->errorNotFound(); }
        $borrower->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'nickname' => $request->nickname,
            'address' => $request->address,
            'date_of_birth' => $request->date_of_birth,
            'place_of_birth' => $request->place_of_birth
        ]);

        return $this->respondWithItem($borrower, new BorrowerTransformer);
    }

    public function destroy($id) {
        $borrower = Borrower::find($id);
        if (!$borrower) { return $this->errorNotFound(); }
        if (!$borrower->repayments->where('status', 'unpaid')->isEmpty()) { return $this->errorInternalError("Borrower cannot deletable while having installments."); }
        $borrower->delete();

        return $this->respondWithItem($borrower, new BorrowerTransformer);
    }

    public function repayment($id, $loanRequestId, $repaymentId) {
        $borrower = Borrower::find($id);
        if (!$borrower) { return $this->errorNotFound(); }
        $loanRequest = $borrower->loanRequests->find($loanRequestId);
        if (!$borrower) { return $this->errorNotFound(); }
        $repayment = $loanRequest->repayments->find($repaymentId);
        if ($repayment->status == 'paid') { return $this->errorInternalError("Installments already been paid."); }
        
        $repayment->update(['status' => 'paid']);
        
        return $this->respondWithItem($borrower, new BorrowerTransformer);
    }
}
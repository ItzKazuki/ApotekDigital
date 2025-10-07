<?php

namespace App\Http\Controllers\Api\Member;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->query('limit', 10); // default 10 kalau gak ada query

        $query = Transaction::with('member')
            ->where('member_id', $request->user()->id)
            ->paginate($limit);

        return TransactionResource::collection($query);
    }


    public function show(Request $request, $transaction_id)
    {
        $transaction = Transaction::with('transactionDetails')->where('member_id', $request->user()->id)->where('id', $transaction_id)->firstOrFail();

        return new TransactionResource($transaction);
    }
}

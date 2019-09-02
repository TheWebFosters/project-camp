<?php

namespace App\Http\Controllers\Admin;

use App\Customer;
use App\Http\Controllers\Controller;
use App\System;
use App\Transaction;
use App\TransactionPaymentLine;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class CustomerAccountLedgerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //customer and business info
        $customer_id = $request->get('customer_id');
        $customer = Customer::with('currency')->find($customer_id);
        $keys = ['tax_number', 'email', 'mobile', 'city', 'state', 'zip_code', 'country'];

        $business_info = System::getSystemSettings($keys);
        $business_info['name'] = config('app.name');

        $start = $request->get('start_date');
        $end = $request->get('end_date');
        
        //invoice
        $transactions = Transaction::OfTransaction('invoice')
                        ->where('transactions.customer_id', $customer_id)
                        ->where('transactions.status', 'final')
                        ->whereBetween('transactions.transaction_date', [$start, $end])
                        ->get()
                        ->toArray();


        //payments
        $transaction_payments = TransactionPaymentLine::whereBetween('paid_on', [$start, $end])
                ->join('transactions', 'transaction_payment_lines.transaction_id', '=', 'transactions.id')
                ->where('transactions.type', 'invoice')
                ->where('transactions.status', 'final')
                ->where('transactions.customer_id', $customer_id)
                ->select('paid_on as transaction_date', 'transactions.ref_no', 'amount', 'transactions.total as total', 'transaction_payment_lines.transaction_id', 'transactions.payment_status')
                ->groupBy('transaction_payment_lines.id')
                ->get()
                ->toArray();
        
        $payments = [];
        $line_amount = [];
        foreach ($transaction_payments as $key => $payment) {
            if (!array_key_exists($payment['transaction_id'], $line_amount)) {
                $paid_total = TransactionPaymentLine::where('paid_on', '<', $start)
                                        ->where('transaction_id', $payment['transaction_id'])
                                        ->sum('amount');

                $line_amount[$payment['transaction_id']] = $paid_total;
            }

            $line_amount[$payment['transaction_id']] += $payment['amount'];

            $payment['balance'] = $payment['total'] - $line_amount[$payment['transaction_id']];
            $payment['type'] = 'payment';
            $payments[] = $payment;
        }

        if (!empty($payments)) {
            $transactions = array_merge($transactions, $payments);
        }

        $sorted_transactions = array_values(Arr::sort($transactions, function ($value) {
            return $value['transaction_date'];
        }));

        $data = ['customer' => $customer, 'business_info' => $business_info, 'transactions' => $sorted_transactions];
        return $this->respond($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

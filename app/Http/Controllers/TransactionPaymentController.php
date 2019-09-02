<?php

namespace App\Http\Controllers;

use App\Currency;
use App\Http\Util\CommonUtil;
use App\System;
use App\Transaction;
use App\TransactionPaymentLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionPaymentController extends Controller
{
    /**
     * All Utils instance.
     */
    protected $commonUtil;

    /**
     * Constructor.
     *
     * @param CommonUtil
     */
    public function __construct(CommonUtil $commonUtil)
    {
        $this->CommonUtil = $commonUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $transaction_id = request()->get('transaction_id');
        $transaction_type = request()->get('type');
        $transaction = Transaction::OfTransaction($transaction_type)
                        ->with('customer.currency')
                        ->find($transaction_id);

        $transaction_total = $transaction->total;

        if ($transaction_type == 'invoice') {
            $customer_currency = $transaction->customer->currency['id'];
            $business_currency = System::getValue('currency_id');

            $show_conversion_rate_field = ($customer_currency == $business_currency) ? 'false' : 'true';
        } elseif ($transaction_type == 'expense') {
            $show_conversion_rate_field = 'false';
        }

        $total_paid = $this->CommonUtil->calculateTotalPaid($transaction_id);

        if (!empty($total_paid)) {
            $transaction_total = $transaction_total - $total_paid;
        }

        $data = ['transaction_total' => $transaction_total, 'conversion_rate' => $show_conversion_rate_field ];

        return $this->respond($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $input = $request->only('transaction_id', 'amount', 'paid_on', 'payment_details', 'conversion_rate');
            $input['created_by'] = $request->user()->id;

            if (empty($input['conversion_rate'])) {
                $input['conversion_rate'] = 1;
            }
            
            $payment_status = $this->CommonUtil->calculatePaymentStatus($input['transaction_id'], $input['amount']);

            DB::beginTransaction();

            TransactionPaymentLine::create($input);
            Transaction::where('id', $input['transaction_id'])
                    ->update(['payment_status' => $payment_status]);

            DB::commit();

            $output = $this->respondSuccess(__('messages.paid_successfully'));
        } catch (Exception $e) {
            DB::rollBack();
            $output = $this->respondWentWrong($e);
        }

        return $output;
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $transaction_type = request()->get('type');
        $transaction = Transaction::OfTransaction($transaction_type)
                                ->with('payments', 'customer', 'customer.currency', 'project', 'expenseFor')
                                ->find($id);

        if ($transaction_type == 'invoice') {
            $currency['customer'] = $transaction->customer->currency;
        }
        $currency_id = System::getValue('currency_id');
        $currency['business'] = Currency::find($currency_id);
        
        $data = ['transaction' => $transaction, 'currency' => $currency];

        return $this->respond($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    }
}

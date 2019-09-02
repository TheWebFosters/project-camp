<?php

namespace App\Http\Controllers;

use App\Category;
use App\Components\User\Models\User;
use App\Http\Util\CommonUtil;
use App\Project;
use App\System;
use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpensesController extends Controller
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
        if (!request()->user()->can('expense.create')) {
            abort(403, 'Unauthorized action.');
        }
        
        $rowsPerPage = (request()->get('rowsPerPage') > 0) ? request()->get('rowsPerPage') : 0;
        $sort_by = request()->get('sort_by');
        $descending = request()->get('descending');

        if ('false' == $descending) {
            $orderby = 'asc';
        } elseif ('true' == $descending) {
            $orderby = 'desc';
        } elseif ('' == $descending) {
            $orderby = 'desc';
            $sort_by = 'id';
        }

        $transactions = Transaction::OfTransaction('expense')
                        ->leftJoin('categories as C', 'transactions.category_id', '=', 'C.id')
                        ->leftJoin('users', 'transactions.expense_for', '=', 'users.id')
                        ->leftJoin('transaction_payment_lines as TPL', 'transactions.id', '=', 'TPL.transaction_id')
                        ->select('ref_no', 'transaction_date', 'total', 'transactions.id as id', 'payment_status', 'C.name as category', 'notes', 'status', 'due_date', 'users.name as expense_for', DB::raw('transactions.total - COALESCE(SUM(TPL.amount), 0) as payment_due'))
                        ->groupBy('transactions.id');

        if (!empty(request()->get('expense_for'))) {
            $transactions->where('transactions.expense_for', request()->get('expense_for'));
        }

        if (!empty(request()->get('category_id'))) {
            $transactions->where('transactions.category_id', request()->get('category_id'));
        }
        
        if (!empty(request()->get('payment_status'))) {
            $transactions->where('transactions.payment_status', request()->get('payment_status'));
        }

        $transactions = $transactions->orderBy($sort_by, $orderby)
                                    ->paginate($rowsPerPage);

        return $this->respond($transactions);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!request()->user()->can('expense.create')) {
            abort(403, 'Unauthorized action.');
        }

        $employees = User::getUsersForDropDown();
        $categories = Category::forDropdown('expenses');
        $projects = Project::getProjectsList();

        $data = [
                'employees' => $employees,
                'projects' => $projects,
                'categories' => $categories,
            ];

        return $this->respond($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!request()->user()->can('expense.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only('category_id', 'transaction_date', 'due_date', 'ref_no', 'total', 'expense_for', 'project_id', 'notes');
            $input['created_by'] = $request->user()->id;
            $input['payment_status'] = 'due';
            $input['type'] = 'expense';
            $input['status'] = 'final';

            //generate ref_no. if ref_no is empty
            if (empty($input['ref_no'])) {
                $input['ref_no'] = $this->CommonUtil->generateReferenceNo('expense');
            }
            $transaction = Transaction::create($input);

            $output = $this->respondSuccess(__('messages.saved_successfully'));
        } catch (Exception $e) {
            $output = $this->respondWentWrong($e);
        }
        return $output;
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
        if (!request()->user()->can('expense.edit')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $transaction = Transaction::find($id);
            $employees = User::getUsersForDropDown();
            $categories = Category::forDropdown('expenses');
            $projects = Project::getProjectsList();

            $data = [
                    'employees' => $employees,
                    'projects' => $projects,
                    'categories' => $categories,
                    'transaction' => $transaction
                ];

            $output = $data;
        } catch (Exception $e) {
            $output = $this->respondWentWrong($e);
        }

        return $output;
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
        if (!request()->user()->can('expense.edit')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only('category_id', 'transaction_date', 'due_date', 'ref_no', 'total', 'expense_for', 'project_id', 'notes');
            $input['created_by'] = $request->user()->id;

            //generate ref_no. if ref_no is empty
            if (empty($input['ref_no'])) {
                $input['ref_no'] = $this->CommonUtil->generateReferenceNo('expense');
            }
            
            $transaction = Transaction::where('id', $id)
                            ->update($input);

            $output = $this->respondSuccess(__('messages.updated_successfully'));
        } catch (Exception $e) {
            $output = $this->respondWentWrong($e);
        }
        return $output;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!request()->user()->can('expense.delete')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $transaction = Transaction::find($id);
            $transaction->delete();

            $output = $this->respondSuccess(__('messages.deleted_successfully'));
        } catch (Exception $e) {
            $output = $this->respondWentWrong($e);
        }
        return $output;
    }

    /**
     * Get Filters for expenses
     *
     * @return \Illuminate\Http\Response
     */
    public function getFilters()
    {
        $employees = User::getUsersForDropDown(true);
        $categories = Category::forDropdown('expenses', null, true);
        $payment_statuses = Transaction::paymentStatuses(true);
        $business_currency = System::getBusinessCurrency('currency_id');
        $data = [
                'employees' => $employees,
                'categories' => $categories,
                'payment_statuses' => $payment_statuses,
                'business_currency' => $business_currency
            ];

        return $this->respond($data);
    }

    /**
     * Get statistics for expenses
     *
     * @return \Illuminate\Http\Response
     */
    public function getStatistics()
    {
        if (!request()->user()->can('expense.create')) {
            abort(403, 'Unauthorized action.');
        }

        $payment_stats = Transaction::OfTransaction('expense')
                            ->select(
                                'payment_status',
                                DB::raw('count(id) as payment_status_count')
                                )
                            ->groupBy('payment_status')
                            ->get();

        //transaction Amount calculation
        $paid_amount = Transaction::OfTransaction('expense')
                            ->leftjoin('transaction_payment_lines as tpl', 'transactions.id', '=', 'tpl.transaction_id')
                            ->select(DB::raw("SUM(amount * conversion_rate) as paid_amount"))
                            ->first();

        $data = ['payment_stats' => $payment_stats, 'paid_amount' => $paid_amount];

        return $this->respond($data);
    }
}

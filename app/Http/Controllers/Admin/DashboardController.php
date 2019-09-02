<?php

namespace App\Http\Controllers\Admin;

use App\Currency;
use App\Http\Controllers\Controller;
use App\Http\Util\CommonUtil;
use App\Project;
use App\System;
use App\Ticket;
use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
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
        $user = request()->user();
        $project_ids = $this->CommonUtil->getAssignedProjectForEmployee($user->id);
        $customer_id = request()->get('customer_id');

        //projects stats
        $projects = new Project();
        if (!$user->hasRole('superadmin')) {
            $projects = $projects->orWhere('projects.lead_id', $user->id)
                    ->orWhereIn('projects.id', $project_ids);
        }
        if (!empty($customer_id)) {
            $projects = $projects->where('customer_id', $customer_id);
        }
        $project_counts = $projects->select(
            DB::raw("COALESCE(SUM(IF(status != 'completed', 1, 0)), 0) as incompleted"),
            DB::raw('count(*) as total')
            )
            ->first();

        //Task
        $task_counts = Project::join('project_tasks as PT', 'projects.id', '=', 'PT.project_id');
        if (!$user->hasRole('superadmin')) {
            $task_counts = $task_counts->join('project_task_members as ptm', 'PT.id', 'ptm.project_task_id')
                ->where('ptm.user_id', $user->id);
        }
        if (!empty($customer_id)) {
            $task_counts->where('customer_id', $customer_id);
        }
        $task_counts = $task_counts->select(
            DB::raw('COALESCE(SUM(IF(is_completed = 1, 0, 1)), 0) as incompleted'),
            DB::raw('count(*) as total')
            )->first();

        //TODO: Needs improvement
        //invoices stats
        $invoice_counts = Transaction::OfTransaction('invoice')
                        ->join('customers as C', 'transactions.customer_id', '=', 'C.id')
                        ->join('currencies as CUR', 'C.currency_id', '=', 'CUR.id')
                        ->leftJoin('transaction_payment_lines AS TPL', 'transactions.id', '=', 'TPL.transaction_id')
                        ->where('status', 'final');

        if (!empty($customer_id)) {
            $invoice_counts->where('customer_id', $customer_id);
        }
        
        $invoice_counts = $invoice_counts->select(
            DB::raw('COALESCE(SUM(TPL.amount), 0) as paid_amount'),
            DB::raw('COALESCE(SUM(TPL.amount * TPL.conversion_rate), 0) as business_currency_amount'),
            'CUR.iso_name as currency_name',
            'CUR.symbol as currency_symbol',
            'CUR.id as currency_id'
            )
            ->groupBy('CUR.id')
            ->get()
            ->toArray();
        
        $invoice_totals = [
            'business_currency_amount' => 0,
        ];

        $currencies = [];
        $currency_symbols = [];

        foreach ($invoice_counts as $key => $counts) {
            $customers = Currency::findOrFail($counts['currency_id'])->customers;
            
            $customer_ids = [];
            foreach ($customers as $customer) {
                $customer_ids[] = $customer->id;
            }
            
            $transaction = new Transaction();
            $transaction = $transaction->where('type', 'invoice')
                                ->where('status', 'final');

            if (!empty($customer_id)) {
                $transaction->where('customer_id', $customer_id);
            } else {
                $transaction->whereIn('customer_id', $customer_ids);
            }
            
            $transaction = $transaction->select(DB::raw("SUM(transactions.total) as total_amount"))
                        ->first();

            $invoice_counts[$key]['total_amount'] = $transaction->total_amount;

            $invoice_totals['business_currency_amount'] += $counts['business_currency_amount'];

            $currencies[$counts['currency_id']] = [
                'name' => $counts['currency_name'] . ' - ' . $counts['currency_symbol'],
                'id' => $counts['currency_id']
            ];
            $currency_symbols[$counts['currency_id']] = $counts['currency_symbol'];
        }

        $business_currency = System::getBusinessCurrency('currency_id');
        $business_curr_data[] = ['currency_symbol' => $business_currency->symbol,
                                  'currency_id' => $business_currency->id
                                ];

        if (!array_key_exists($business_currency->id, $currencies)) {
            $business_curr[$business_currency->id] = [
                                        'name' => $business_currency->iso_name . ' - ' . $business_currency->symbol,
                                        'id' => $business_currency->id
                                        ];
            $currencies += $business_curr;
            $invoice_counts = array_merge($invoice_counts, $business_curr_data);
        }

        //payment Count
        $payment_status_count = Transaction::OfTransaction('invoice')
                                ->where('status', 'final');
        if (!empty($customer_id)) {
            $payment_status_count->where('customer_id', $customer_id);
        }
        $payment_status_count = $payment_status_count->select('payment_status', DB::raw('count(id) as count'))
            ->groupBy('payment_status')
            ->get();


        //Ticket Pie Chart
        $tickets = Ticket::select('status', DB::raw("COUNT(id) as status_count"));

        if ($user->hasRole('employee') && !$user->hasRole('superadmin')) {
            $tickets->where('tickets.assigned_to', $user->id);
        }
        $tickets = $tickets->groupBy('status')
                            ->get();

        $ticket_pie_chart_label = [];
        $ticket_pie_chart_dataset = [];
        foreach ($tickets as $key => $ticket) {
            $ticket_pie_chart_label[] = __('messages.'.$ticket->status);
            $ticket_pie_chart_dataset[] = $ticket->status_count;
        }

        //TRANSACTION:invoice paid vs expense pie chart
        $invoice = Transaction::OfTransaction('invoice')
                            ->where('status', 'final')
                            ->leftjoin('transaction_payment_lines as tpl', 'transactions.id', '=', 'tpl.transaction_id')
                            ->select(DB::raw("SUM(amount * conversion_rate) as paid_amount"))
                            ->first();

        $expense = Transaction::OfTransaction('expense')
                            ->select(DB::raw("SUM(total) as total"))
                            ->first();

        $invoice_paid_amount = (number_format($invoice->paid_amount, 2));

        $total_expense = (number_format($expense->total, 2));

        $transaction_pie_chart_label = [__('messages.invoice_paid'), __('messages.expense')];

        $transaction_pie_chart_datasets = [$invoice_paid_amount, $total_expense];

        $dashboard_data = [
                'project_counts' => $project_counts,
                'task_counts' => $task_counts,
                'invoice_counts' => $invoice_counts,
                'invoice_totals' => $invoice_totals,
                'currencies' => $currencies,
                'currency_symbols' => $currency_symbols,
                'business_currency' => $business_currency,
                'payment_status_count' => $payment_status_count,
                'sticky_notes' => $user->sticky_notes,
                'ticket_pie_chart_label' => $ticket_pie_chart_label,
                'ticket_pie_chart_dataset' => $ticket_pie_chart_dataset,
                'transaction_pie_chart_label' => $transaction_pie_chart_label,
                'transaction_pie_chart_datasets' => $transaction_pie_chart_datasets
            ];

        return $dashboard_data;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
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

    public function saveStickNotes()
    {
        $user = request()->user();
        $user->sticky_notes = request()->get('sticky_notes');
        $user->save();
    }
}

<?php

namespace App\Http\Controllers\Client;

use App\Components\User\Models\User;
use App\Http\Controllers\Controller;
use App\Project;
use App\Ticket;
use App\Transaction;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            $user_id = request()->user()->id;
            $user = User::with(['customer', 'customer.currency'])
                    ->findOrFail($user_id);

            //Projects incompleted
            $project_counts = Project::where('customer_id', $user->customer->id)
                    ->select(
                        DB::raw("COALESCE(SUM(IF(status != 'completed', 1, 0)), 0) as incompleted"),
                        DB::raw('count(*) as total')
                    )
                    ->first();

            //Task incompleted
            $task_counts = Project::join('project_tasks as PT', 'projects.id', '=', 'PT.project_id')
                    ->where('customer_id', $user->customer->id)
                    ->select(
                        DB::raw('COALESCE(SUM(IF(is_completed = 1, 0, 1)), 0) as incompleted'),
                        DB::raw('count(*) as total')
                    )
                    ->first();

            //Invoices stats
            $invoice_counts = Transaction::OfTransaction('invoice')
                ->where('customer_id', $user->customer->id)
                ->where('status', 'final')
                ->select(
                    DB::raw("COALESCE(SUM(IF(payment_status = 'paid', 1, 0)), 0) as paid"),
                    DB::raw("COALESCE(SUM(IF(payment_status != 'paid', 1, 0)), 0) as not_paid"),
                    DB::raw("COALESCE(SUM(IF(payment_status = 'due', 1, 0)), 0) as due"),
                    DB::raw("COALESCE(SUM(IF(payment_status = 'partial', 1, 0)), 0) as partial"),
                    DB::raw('count(*) as total_counts'),
                    DB::raw('COALESCE(SUM(total), 0) as total_amount')
                )
                ->first();

            //Invoice paid amount
            $invoice_paid_amount = Transaction::OfTransaction('invoice')
                ->join(
                    'transaction_payment_lines AS TPL',
                    'transactions.id',
                    '=',
                    'TPL.transaction_id'
                )
                ->where('customer_id', $user->customer->id)
                ->where('status', 'final')
                ->select(DB::raw('COALESCE(SUM(amount), 0) as paid_amount'))
                ->first();

            //Ticket Pie Chart
            $tickets = Ticket::where('customer_id', $user->customer_id)
                            ->select('status', DB::raw("COUNT(id) as status_count"))
                            ->groupBy('status')
                            ->get();

            $ticket_pie_chart_label = [];
            $ticket_pie_chart_dataset = [];
            foreach ($tickets as $key => $ticket) {
                $ticket_pie_chart_label[] = __('messages.'.$ticket->status);
                $ticket_pie_chart_dataset[] = $ticket->status_count;
            }
            $data = [
                    'project' => $project_counts,
                    'task' => $task_counts,
                    'invoice' => $invoice_counts,
                    'user_name' => $user->name,
                    'currency_symbol' => $user->customer->currency->symbol,
                    'ticket_pie_chart_label' => $ticket_pie_chart_label,
                    'ticket_pie_chart_dataset' => $ticket_pie_chart_dataset
                ];
            $data['invoice']['paid_amount'] = $invoice_paid_amount->paid_amount;

            $output = $this->respond($data);
        } catch (Exception $e) {
            $output = $this->respondWentWrong($e);
        }

        return $output;
    }
}

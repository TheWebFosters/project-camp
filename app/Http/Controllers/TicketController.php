<?php

namespace App\Http\Controllers;

use App\Category;
use App\Components\User\Models\User;
use App\Customer;
use App\Http\Util\CommonUtil;
use App\Notifications\TicketCreated;
use App\Ticket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Notification;

class TicketController extends Controller
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
        $rowsPerPage = (request()->get('rowsPerPage') > 0) ? request()->get('rowsPerPage') : 0;
        $sort_by = request()->get('sort_by');
        $descending = request()->get('descending');

        if ('false' == $descending) {
            $orderby = 'asc';
        } elseif ('true' == $descending) {
            $orderby = 'desc';
        } elseif ('' == $descending) {
            $orderby = 'desc';
            $sort_by = 'tickets.id';
        }

        $user = request()->user();
        $assigned_to = request()->get('assigned_to');
        $customer_id = request()->get('customer_id');

        if ($user->hasRole('employee') && !$user->hasRole('superadmin')) {
            $assigned_to = $user->id;
        }

        if ($user->hasRole('contact')) {
            $customer_id = $user->customer_id;
        }

        $tickets = Ticket::join('categories as Category', 'tickets.category_id', '=', 'Category.id')
            ->leftJoin('users as U', 'tickets.assigned_to', '=', 'U.id')
            ->join('customers as C', 'tickets.customer_id', '=', 'C.id')
            ->select('tickets.id as id', 'reference_no', 'C.company as customer', 'U.name as assigned_to', 'Category.name as ticket_type', 'title', 'priority', 'status', 'tickets.updated_at as last_updated');

        if (!empty(request()->get('category_id'))) {
            $tickets->where('tickets.category_id', request()->get('category_id'));
        }

        if (!empty(request()->get('priority'))) {
            $tickets->where('tickets.priority', request()->get('priority'));
        }

        if (!empty(request()->get('status'))) {
            $tickets->where('tickets.status', request()->get('status'));
        }

        if (!empty($assigned_to)) {
            $tickets->where('tickets.assigned_to', $assigned_to);
        }

        if (!empty($customer_id)) {
            $tickets->where('tickets.customer_id', $customer_id);
        }
        
        $tickets = $tickets->orderBy($sort_by, $orderby)
            ->paginate($rowsPerPage);

        return $this->respond($tickets);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!request()->user()->can('tickets.create')) {
            abort(403, 'Unauthorized action.');
        }

        $priority = $this->CommonUtil->getPriorityList();
        $statuses = Ticket::StatusForDropDown();
        $ticket_types = Category::forDropdown('tickets');
        $customers = Customer::getCustomersForDropDown();
        $employees = User::getUsersForDropDown();

        $data = [
                    'priority' => $priority,
                    'statuses' => $statuses,
                    'ticket_types' => $ticket_types,
                    'customers' => $customers,
                    'employees' => $employees
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
        if (!request()->user()->can('tickets.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();

            $user = $request->user();

            $input = $request->only('title', 'category_id', 'priority', 'description', 'status', 'assigned_to', 'customer_id');
            $input['created_by'] = $user->id;
            $input['updated_by'] = $user->id;

            //If contact then assign the contacts customer id, default status new
            if ($user->hasRole('contact')) {
                $input['customer_id'] = $user->customer_id;
                $input['status'] = 'new';
            }
            
            $input['reference_no'] = $this->CommonUtil->generateReferenceNo('ticket');

            $ticket = Ticket::create($input);

            //QUERY:superadmin & contacts & assigned member.
            if ($user->hasRole('contact')) {
                //ticket created by customer
                $input['assigned_to'] = null;
                $this->_sendNotificationToUser($ticket, $input['assigned_to'], true);
            } else {
                // ticket created by superadmin
                $this->_sendNotificationToContacts($input['customer_id'], $ticket);

                if (!empty($input['assigned_to'])) {
                    $this->_sendNotificationToUser($ticket, $input['assigned_to']);
                }
            }

            DB::commit();

            $output = $this->respondSuccess(__('messages.saved_successfully'));
        } catch (Exception $e) {
            DB::rollBack();
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
        $ticket = Ticket::with('ticketType', 'lastUpdateBy')
                    ->findOrFail($id);

        return $this->respond($ticket);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!request()->user()->can('tickets.edit')) {
            abort(403, 'Unauthorized action.');
        }

        $ticket = Ticket::findOrFail($id);
        $priority = $this->CommonUtil->getPriorityList();
        $statuses = Ticket::StatusForDropDown();
        $ticket_types = Category::forDropdown('tickets');
        $customers = Customer::getCustomersForDropDown();
        $employees = User::getUsersForDropDown();

        $data = [
                    'priority' => $priority,
                    'statuses' => $statuses,
                    'ticket_types' => $ticket_types,
                    'customers' => $customers,
                    'employees' => $employees,
                    'ticket' => $ticket
                ];

        return $this->respond($data);
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
        if (!request()->user()->can('tickets.edit')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();

            $input = $request->only('title', 'category_id', 'priority', 'description', 'status', 'assigned_to', 'customer_id');
            $input['updated_by'] = $request->user()->id;

            $ticket = Ticket::find($id);
            Ticket::where('id', $id)
                ->update($input);

            //QUERY:contacts & assigned member.
            if ($ticket->customer_id !== $input['customer_id']) {
                $this->_sendNotificationToContacts($input['customer_id'], $ticket);
            }

            if ($ticket->assigned_to !== $input['assigned_to']) {
                $this->_sendNotificationToUser($ticket, $input['assigned_to']);
            }

            DB::commit();

            $output = $this->respondSuccess(__('messages.updated_successfully'));
        } catch (Exception $e) {
            DB::rollBack();
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
        try {
            Ticket::destroy($id);

            $output = $this->respondSuccess(__('messages.deleted_successfully'));
        } catch (\Exception $e) {
            $output = $this->respondWentWrong($e);
        }

        return $output;
    }

    /**
     * Get Filters for ticket
     *
     * @return \Illuminate\Http\Response
     */
    public function getFilters()
    {
        $employees = User::getUsersForDropDown(true);
        $ticket_types = Category::forDropdown('tickets', null, true);
        $customers = Customer::getCustomersForDropDown(true);
        $priorities = $this->CommonUtil->getPriorityList(true);
        $statuses = Ticket::StatusForDropDown(true);

        $data = [
                'employees' => $employees,
                'ticket_types' => $ticket_types,
                'customers' => $customers,
                'priorities' => $priorities,
                'statuses' => $statuses
            ];

        return $this->respond($data);
    }

    /**
     * update status of
     * ticket
     * @return \Illuminate\Http\Response
     */
    public function updateStatus()
    {
        try {
            $input['closed_at'] = Carbon::now()->toDateTimeString();
            $input['status'] = request()->get('status');
            $input['updated_by'] = request()->user()->id;

            $ticket_id = request()->get('ticket_id');
            Ticket::where('id', $ticket_id)
                    ->update($input);

            $output = $this->respondSuccess(__('messages.updated_successfully'));
        } catch (Exception $e) {
            $output = $this->respondWentWrong($e);
        }
        return $output;
    }

    /**
     * statistics for tickets
     *
     * @return \Illuminate\Http\Response
     */
    public function getStatistics()
    {
        $user = request()->user();

        if ($user->hasRole('employee') && !$user->hasRole('superadmin')) {
            $assigned_to = $user->id;
        }

        if ($user->hasRole('contact')) {
            $customer_id = $user->customer_id;
        }

        $tickets_statistics = Ticket::select('status', DB::raw("COUNT(id) as status_count"));

        if (!empty($assigned_to)) {
            $tickets_statistics->where('tickets.assigned_to', $assigned_to);
        }

        if (!empty($customer_id)) {
            $tickets_statistics->where('tickets.customer_id', $customer_id);
        }

        $tickets_statistics = $tickets_statistics->groupBy('status')
                                            ->get();
        
        return $this->respond($tickets_statistics);
    }

    /**
     * send database notification.
     *
     * @return \Illuminate\Http\Response
     */
    protected function _sendNotificationToUser($ticket, $user_id, $is_admin = false)
    {
        if ($is_admin) {
            $notification_to_be_sent = $this->CommonUtil->getSuperadmin();
        } else {
            $notification_to_be_sent = User::find($user_id);
        }

        $notification_to_be_sent->notify(new TicketCreated($ticket));
    }

    /**
     * send database notification.
     *
     * @return \Illuminate\Http\Response
     */
    protected function _sendNotificationToContacts($customer_id, $ticket)
    {
        $contacts = Customer::find($customer_id)
                        ->contacts;

        Notification::send($contacts, new TicketCreated($ticket));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Customer;
use App\Http\Controllers\Controller;
use App\Http\Util\CommonUtil;
use App\Notifications\LeadAdded;
use App\Source;
use App\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeadController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $commonUtil;

    /**
     * Constructor
     *
     * @param CommonUtil
     * @return void
     */
    public function __construct(CommonUtil $commonUtil)
    {
        $this->CommonUtil = $commonUtil;
        $this->customer_status = Customer::$STATUS_ID_FOR_CUSTOMER;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!request()->user()->can('customer.view')) {
            abort(403, 'Unauthorized action.');
        }

        $rowsPerPage = ($request->get('rowsPerPage') > 0) ? $request->get('rowsPerPage') : 0;
        $sort_by = $request->get('sort_by');
        $descending = $request->get('descending');

        if ($descending == 'false') {
            $orderby = 'asc';
        } elseif ($descending == 'true') {
            $orderby = 'desc';
        } elseif ($descending == '') {
            $orderby = 'desc';
            $sort_by = 'id';
        }

        $query = Customer::where('status_id', '!=', $this->customer_status)
                    ->leftJoin('sources as S', 'customers.source_id', '=', 'S.id')
                    ->leftJoin('users as U', 'customers.assigned_to', '=', 'U.id')
                    ->join('statuses', 'customers.status_id', '=', 'statuses.id')
                    ->select('customers.id as id', 'company', 'statuses.name as status', 'customers.mobile as mobile', 'contacted_date', 'S.name as source', 'U.name as assigned_to', 'status_id');
                    
        if (!empty($request->get('term'))) {
            $term = $request->get('term');
            $query->where('customers.company', 'like', "%$term%")
                ->orWhere('customers.mobile', 'like', "%$term%");
        }

        if (!empty($request->input('status_id'))) {
            $query->where('status_id', $request->input('status_id'));
        }

        if (!empty($request->input('source_id'))) {
            $query->where('source_id', $request->input('source_id'));
        }

        $lead = $query->where('status_id', '!=', $this->customer_status)
                        ->orderBy($sort_by, $orderby)
                        ->paginate($rowsPerPage);

        $statuses = Status::getStatus(true);
        $sources = Source::getSource(true);

        $data = ['lead' => $lead, 'statuses' => $statuses, 'sources' => $sources];

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
        if (!request()->user()->can('customer.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();

            $lead_data = $request->only(
                'company',
                'tax_number',
                'mobile',
                'currency_id',
                'alternate_contact_no',
                'email',
                'website',
                'city',
                'state',
                'country',
                'zip_code',
                'billing_address',
                'shipping_address',
                'status_id',
                'source_id',
                'assigned_to',
                'description',
                'contacted_date'
            );

            $lead_data['created_by'] = $request->user()->id;
            $lead = Customer::create($lead_data);

            $contact = $request->only('name', 'password');
            $contact['email'] = $request->input('contact_email');
            $contact['customer_id'] = $lead->id;
            $user = $this->CommonUtil->createContact($contact);

            if (!empty($request->input('send_email'))) {
                $this->_sendEmailToLead($lead);
            }
            DB::commit();

            $output = $this->respondSuccess(__('messages.saved_successfully'));
        } catch (\Exception $e) {
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
        if (!request()->user()->can('customer.edit')) {
            abort(403, 'Unauthorized action.');
        }

        if (!empty($id)) {
            $lead = Customer::findOrFail($id);
            
            return $this->respond($lead);
        }
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
        if (!request()->user()->can('customer.edit')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();

            $lead_data = $request->only(
                'company',
                'tax_number',
                'mobile',
                'currency_id',
                'alternate_contact_no',
                'email',
                'website',
                'city',
                'state',
                'country',
                'zip_code',
                'billing_address',
                'shipping_address',
                'status_id',
                'source_id',
                'assigned_to',
                'contacted_date',
                'description'
            );

            $lead = Customer::where('id', $id)
                        ->update($lead_data);

            //send email to lead if send_email is enabled
            if (!empty($request->input('send_email'))) {
                $lead = Customer::findOrFail($id);
                $this->_sendEmailToLead($lead);
            }
            DB::commit();

            $output = $this->respondSuccess(__('messages.updated_successfully'));
        } catch (\Exception $e) {
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
        if (!request()->user()->can('customer.delete')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            Customer::destroy($id);

            $output = $this->respondSuccess(__('messages.deleted_successfully'));
        } catch (\Exception $e) {
            $output = $this->respondWentWrong($e);
        }

        return $output;
    }

    /**
     * send email to lead
     * when added in application
     *
     * @return \Illuminate\Http\Response
     */
    protected function _sendEmailToLead($lead)
    {
        $lead->notify(new LeadAdded($lead));
    }

    /**
     * Update Status of a lead
     *
     * @return \Illuminate\Http\Response
     */
    public function updateStatus()
    {
        if (!request()->user()->can('customer.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $lead_id = request()->input('id');
            $status_id = request()->only('status_id');
            Customer::where('id', $lead_id)
                    ->update($status_id);
                    
            $output = $this->respondSuccess(__('messages.updated_successfully'));
        } catch (Exception $e) {
            $output = $this->respondWentWrong($e);
        }
        return $output;
    }

    /**
     * statistics for lead
     *
     * @return \Illuminate\Http\Response
     */
    public function getStatistics()
    {
        if (!request()->user()->can('customer.view')) {
            abort(403, 'Unauthorized action.');
        }

        $statuses = Customer::where('status_id', '!=', $this->customer_status)
                        ->join('statuses', 'customers.status_id', '=', 'statuses.id')
                        ->select(
                            'statuses.name as name',
                            DB::raw("COUNT(statuses.id) as status_count")
                        )
                        ->groupBy('status_id')
                        ->get();

        $sources = Customer::where('status_id', '!=', $this->customer_status)
                        ->leftJoin('sources as S', 'customers.source_id', '=', 'S.id')
                        ->select(
                            'S.name as name',
                            DB::raw("COUNT(S.id) as source_count")
                        )
                        ->groupBy('source_id')
                        ->get();
        
        $lead_stats = ['statuses' => $statuses, 'sources' => $sources];

        
        return $this->respond($lead_stats);
    }
}

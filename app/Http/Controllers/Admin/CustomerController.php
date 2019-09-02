<?php

namespace App\Http\Controllers\Admin;

use App\Components\User\Models\User;
use App\Customer;
use App\Http\Controllers\Controller;
use App\Http\Util\CommonUtil;
use App\Notifications\CustomerAdded;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
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

        $query = Customer::where('status_id', $this->customer_status)
            ->select(
                'company',
                'tax_number',
                'mobile',
                'website',
                'id'
            );

        if (!empty($request->get('term'))) {
            $term = $request->get('term');
            $query->where('company', 'like', "%$term%")
                ->orWhere('mobile', 'like', "%$term%")
                ->orWhere('website', 'like', "%$term%")
                ->orWhere('tax_number', 'like', "%$term%");
        }

        $customers = $query->orderBy($sort_by, $orderby)
                        ->paginate($rowsPerPage);

        return $customers;
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

            $customer_data = $request->only(
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
                'shipping_address'
            );
            
            //Default: status id for customer
            $customer_data['status_id'] = $this->customer_status;
            $customer_data['created_by'] = $request->user()->id;
            $customer = Customer::create($customer_data);

            $contact = $request->only('name', 'password');
            $contact['email'] = $request->input('contact_email');
            $contact['customer_id'] = $customer->id;
            $user = $this->CommonUtil->createContact($contact);

            //Assign roles to contact related to projects.
            $projects = $user->customer->projects;
            foreach ($projects as $project) {
                $user->assignRole($this->CommonUtil->customerProjectRole($project->id));
            }

            // send email to customer if send_email is enabled
            if (!empty($request->input('send_email'))) {
                $this->_sendEmailToCustomer($customer);
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
        if (!request()->user()->can('customer.view')) {
            abort(403, 'Unauthorized action.');
        }

        if (!empty($id)) {
            $customer = Customer::with('currency')->find($id);

            return $this->respond($customer);
        }
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
            $customer = Customer::find($id);
            
            return $this->respond($customer);
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
            $customer_data = $request->only(
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
                'shipping_address'
            );
            
            $customer = Customer::findOrFail($id);
            $customer->update($customer_data);

            $output = $this->respondSuccess(__('messages.updated_successfully'));
        } catch (\Exception $e) {
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
     * Get list of contacts for a customer
     *
     * @param  int  $customer_id
     * @return \Illuminate\Http\Response
     */
    public function getContacts($customer_id)
    {
        $contacts = User::where('customer_id', $customer_id)
                        ->select('name', 'id')
                        ->get()
                        ->toArray();

        return $this->respond($contacts);
    }

    /**
     * Get customer
     *
     * @param  int  $customer_id
     * @return \Illuminate\Http\Response
     */
    public function getCustomer($customer_id)
    {
        $customer = Customer::findOrFail($customer_id);

        return $this->respond($customer);
    }

    /**
     * send email to customer
     * when added in application
     *
     * @return \Illuminate\Http\Response
     */
    protected function _sendEmailToCustomer($customer)
    {
        $customer->notify(new CustomerAdded($customer));
    }
}

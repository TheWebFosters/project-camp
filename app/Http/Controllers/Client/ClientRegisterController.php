<?php

namespace App\Http\Controllers\Client;

use App\Currency;
use App\Customer;
use App\Http\Controllers\Controller;
use App\Http\Util\CommonUtil;
use App\Notifications\CustomerAdded;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientRegisterController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(CommonUtil $commonUtil)
    {
        $this->middleware('guest');
        
        $this->customer_status = Customer::$STATUS_ID_FOR_CUSTOMER;
        $this->CommonUtil = $commonUtil;
    }
    
    /**
     * Display a registration form to the client.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $currencies = Currency::select('id', DB::raw("concat(iso_name, ' (', symbol, ') ') as currency"))
                ->get()
                ->toArray();

        return view('client_register.register_form')->with(compact('currencies'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'company' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'mobile' => 'required|string|max:255',
            'currency_id' => 'required|integer',
            'email' => 'required|email|unique:customers,email',
            'contact_name' => 'required|string|max:255',
            'contact_email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'contact_name' => 'required|string|max:255',
        ]);

        try {
            //Create Customer
            $customer_data = $request->only(['company', 'tax_number', 'mobile',
                    'currency_id', 'alternate_contact_no', 'email', 'website',
                    'city', 'state', 'country', 'zip_code', 'billing_address',
                    'shipping_address']);

            $customer_data['status_id'] = $this->customer_status;
            $customer_data['created_by'] = 0;

            DB::beginTransaction();

            $customer = Customer::create($customer_data);

            //Create Contact
            $contact_data = [
                'name' => $request->input('contact_name'),
                'email' => $request->input('contact_email'),
                'customer_id' => $customer->id,
                'password' => $request->input('password')
            ];
            $contact = $this->CommonUtil->createContact($contact_data);

            $customer->created_by = $contact->id;
            $customer->save();

            DB::commit();

            //$customer->notify(new CustomerAdded($customer));

            $output = [
                'success' => true,
                'msg' => __('messages.registered_successfully')
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->route('login')->with('status', $output);
    }
}

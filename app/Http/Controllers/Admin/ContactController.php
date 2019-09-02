<?php

namespace App\Http\Controllers\Admin;

use App\Components\User\Models\User;
use App\Customer;
use App\Http\Controllers\Controller;
use App\Http\Util\CommonUtil;
use App\Notifications\CustomersContactAdded;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContactController extends Controller
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
        if (!request()->user()->can('contact.view')) {
            abort(403, 'Unauthorized action.');
        }

        $rowsPerPage = ($request->get('rowsPerPage') > 0) ? $request->get('rowsPerPage') : 0;
        $sort_by = $request->get('sort_by');
        $descending = $request->get('descending');
        $customer_id = $request->get('customer_id');

        if ($descending == 'false') {
            $orderby = 'asc';
        } elseif ($descending == 'true') {
            $orderby = 'desc';
        } elseif ($descending == '') {
            $orderby = 'desc';
            $sort_by = 'id';
        }

        $users = User::where('customer_id', $customer_id)
                    ->select('name', 'email', 'id')
                    ->orderBy($sort_by, $orderby)
                    ->paginate($rowsPerPage);

        return $users;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!request()->user()->can('contact.create')) {
            abort(403, 'Unauthorized action.');
        }

        $gender_types = User::getGenders();
        return $this->respond(['gender_types' => $gender_types]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!request()->user()->can('contact.create')) {
            abort(403, 'Unauthorized action.');
        }

        $validate = validator($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
        ]);

        if ($validate->fails()) {
            return $this->respondWithError($validate->errors()->first());
        }
        
        try {
            DB::beginTransaction();

            $customer_contact = $request->only(
                'name',
                'email',
                'password',
                'mobile',
                'alternate_num',
                'address',
                'skype',
                'linkedin',
                'facebook',
                'twitter',
                'gender',
                'note',
                'customer_id'
            );

            $customer_contact['created_by'] = $request->user()->id;
            $user = $this->CommonUtil->createContact($customer_contact);

            //Assign roles to contact related to projects.
            $customer = Customer::find($customer_contact['customer_id']);
            if ($customer->status_id == $this->customer_status) {
                $projects = $user->customer->projects;
                foreach ($projects as $project) {
                    $user->assignRole($this->CommonUtil->customerProjectRole($project->id));
                }
            }
            
            //send email to contact is send_email is enabled
            if (!empty($request->input('send_email'))) {
                $customer_contact['customer'] = $customer->company;
                $this->_sendEmailToCustomersContact($user, $customer_contact);
            }

            $output = $this->respondSuccess(__('messages.saved_successfully'));
            DB::commit();
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
        if (!request()->user()->can('contact.view')) {
            abort(403, 'Unauthorized action.');
        }

        $customer_id = request()->get('customer_id');

        if (!empty($id)) {
            $user = User::where('customer_id', $customer_id)
                        ->find($id);

            return $this->respond($user);
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
        if (!request()->user()->can('contact.edit')) {
            abort(403, 'Unauthorized action.');
        }

        if (!empty($id)) {
            $customer_id = request()->get('customer_id');
            $gender_types = User::getGenders();
            $user = User::where('customer_id', $customer_id)
                        ->find($id);

            return $this->respond(['gender_types' => $gender_types, 'user' => $user]);
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
        if (!request()->user()->can('contact.edit')) {
            abort(403, 'Unauthorized action.');
        }

        $validate = validator($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$id,
        ]);

        if ($validate->fails()) {
            return $this->respondWithError($validate->errors()->first());
        }
        
        try {
            DB::beginTransaction();

            $contact = $request->only(
                'name',
                'email',
                'password',
                'mobile',
                'alternate_num',
                'address',
                'skype',
                'linkedin',
                'facebook',
                'twitter',
                'gender',
                'note'
            );

            $customer_id = $request->get('customer_id');
            $customer = Customer::find($customer_id);

            $user = User::where('customer_id', $customer_id)
                        ->findOrFail($id);
            $user->update($contact);

            //send email to contact if password & send_email enabled
            if (!empty($request->input('send_email')) && !empty($contact['password'])) {
                $contact['customer'] = $customer->company;
                
                $this->_sendEmailToCustomersContact($user, $contact);
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
    public function destroy($id, Request $request)
    {
        if (!request()->user()->can('contact.delete')) {
            abort(403, 'Unauthorized action.');
        }
        
        try {
            $customer_id = $request->get('customer_id');
            $user = User::where('customer_id', $customer_id)
                        ->findOrFail($id);
            $user->delete();

            $output = $this->respondSuccess(__('messages.deleted_successfully'));
        } catch (\Exception $e) {
            $output = $this->respondWentWrong($e);
        }

        return $output;
    }

    /**
     * send email to customer's contact
     * when added in application
     *
     * @return \Illuminate\Http\Response
     */
    protected function _sendEmailToCustomersContact($user, $data)
    {
        $user->notify(new CustomersContactAdded($data));
    }
}

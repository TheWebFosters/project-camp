<?php

namespace App\Http\Controllers\Admin;

use App\Customer;
use App\Http\Controllers\Controller;
use App\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerNoteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!request()->user()->can('customerNote.view')) {
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

        $users = Note::where('notable_id', $customer_id)
                    ->where('notable_type', 'App\Customer')
                    ->with('user')
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
        if (!request()->user()->can('customerNote.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();

            $data = $request->only('heading', 'description');
            $data['created_by'] = $request->user()->id;

            $customer_id = $request->get('customer_id');
            $customer = Customer::findOrFail($customer_id);

            $note = $customer->notes()->create($data);

            //Add medias for customer
            if (!empty($request->medias)) {
                $this->addMedias($request->medias, $note, 'customer_note');
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
    public function show($id, Request $request)
    {
        if (!request()->user()->can('customerNote.view')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $customer_id = $request->get('customer_id');
            $customer_note = Note::where('notable_id', $customer_id)
                                ->where('notable_type', 'App\Customer')
                                ->with(['user', 'media'])
                                ->findOrFail($id);

            $output = $this->respond($customer_note);
        } catch (\Exception $e) {
            $output = $this->respondWentWrong($e);
        }

        return $output;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!request()->user()->can('customerNote.edit')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $customer_id = request()->get('customer_id');
            $customer_note = Note::where('notable_id', $customer_id)
                                ->where('notable_type', 'App\Customer')
                                ->findOrFail($id);

            $output = $this->respond($customer_note);
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
        if (!request()->user()->can('customerNote.edit')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();

            $data = $request->only('heading', 'description');

            $customer_id = $request->get('customer_id');
            $note = Note::where('notable_id', $customer_id)
                        ->where('notable_type', 'App\Customer')
                        ->findOrFail($id);
                        
            $note->heading = $data['heading'];
            $note->description = $data['description'];
            $note->save();

            //Add medias for customer
            if (!empty($request->medias)) {
                $this->addMedias($request->medias, $note, 'customer_note');
            }

            DB::commit();

            $output = $this->respondSuccess();
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
        if (!request()->user()->can('customerNote.delete')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $customer_id = $request->get('customer_id');
            $note = Note::where('notable_id', $customer_id)
                        ->where('notable_type', 'App\Customer')
                        ->findOrFail($id);
            $note->delete();

            $output = $this->respondSuccess();
        } catch (\Exception $e) {
            $output = $this->respondWentWrong($e);
        }

        return $output;
    }
}

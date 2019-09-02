<?php

namespace App\Http\Controllers\Admin;

use App\Customer;
use App\Http\Controllers\Controller;
use App\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeadNoteController extends Controller
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
        $lead_id = $request->get('lead_id');

        if ($descending == 'false') {
            $orderby = 'asc';
        } elseif ($descending == 'true') {
            $orderby = 'desc';
        } elseif ($descending == '') {
            $orderby = 'desc';
            $sort_by = 'id';
        }

        $notes = Note::where('notable_id', $lead_id)
                    ->where('notable_type', 'App\Customer')
                    ->with('user')
                    ->orderBy($sort_by, $orderby)
                    ->paginate($rowsPerPage);

        return $this->respond($notes);
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

            $lead_id = $request->get('lead_id');
            $lead = Customer::findOrFail($lead_id);

            $data = $request->only('heading', 'description');
            $data['created_by'] = $request->user()->id;
            $note = $lead->notes()->create($data);

            //follow up date for lead
            if (!empty($request->input('contacted_date'))) {
                $lead->update(['contacted_date' => $request->input('contacted_date')]);
            }

            //Add medias for lead
            if (!empty($request->medias)) {
                $this->addMedias($request->medias, $note, 'lead_note');
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
        if (!request()->user()->can('customerNote.view')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $lead_id = request()->get('lead_id');
            $lead_note = Note::where('notable_id', $lead_id)
                                ->where('notable_type', 'App\Customer')
                                ->with(['user', 'media'])
                                ->findOrFail($id);

            $output = $this->respond($lead_note);
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
            $lead_id = request()->get('lead_id');
            $lead_note = Note::where('notable_id', $lead_id)
                                ->where('notable_type', 'App\Customer')
                                ->findOrFail($id);

            $contacted_date = Customer::where('id', $lead_id)
                                ->value('contacted_date');
            $lead_data = [
                        'lead_note' => $lead_note,
                        'contacted_date' => $contacted_date
                    ];
            $output = $this->respond($lead_data);
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

            $lead_id = $request->get('lead_id');
            if (!empty($request->input('contacted_date'))) {
                $lead = Customer::findOrFail($lead_id);
                $lead->update(['contacted_date' => $request->input('contacted_date')]);
            }

            $note = Note::where('notable_id', $lead_id)
                        ->where('notable_type', 'App\Customer')
                        ->findOrFail($id);

            $note->heading = $data['heading'];
            $note->description = $data['description'];
            $note->save();

            //Add medias for lead
            if (!empty($request->medias)) {
                $this->addMedias($request->medias, $note, 'lead_note');
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
    public function destroy($id)
    {
        if (!request()->user()->can('customerNote.delete')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $lead_id = request()->get('lead_id');
            $note = Note::where('notable_id', $lead_id)
                        ->where('notable_type', 'App\Customer')
                        ->findOrFail($id);

            $note->delete();

            $output = $this->respondSuccess(__('messages.deleted_successfully'));
        } catch (\Exception $e) {
            $output = $this->respondWentWrong($e);
        }

        return $output;
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Components\User\Models\User;
use App\Http\Controllers\Controller;
use App\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeNoteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!request()->user()->can('employeeNote.view')) {
            abort(403, 'Unauthorized action.');
        }

        $rowsPerPage = (request()->get('rowsPerPage') > 0) ? request()->get('rowsPerPage') : 0;
        $sort_by = request()->get('sort_by');
        $descending = request()->get('descending');
        $user_id = request()->get('user_id');

        if ($descending == 'false') {
            $orderby = 'asc';
        } elseif ($descending == 'true') {
            $orderby = 'desc';
        } elseif ($descending == '') {
            $orderby = 'desc';
            $sort_by = 'id';
        }

        $employee_note = Note::where('notable_id', $user_id)
                            ->where('notable_type', 'App\Components\User\Models\User')
                            ->with('user')
                            ->orderBy($sort_by, $orderby)
                            ->paginate($rowsPerPage);

        return $employee_note;
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
        if (!request()->user()->can('employeeNote.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();

            $data = $request->only('heading', 'description');
            $data['created_by'] = $request->user()->id;

            $user_id = $request->get('user_id');
            $user = User::findOrFail($user_id);

            $note = $user->notes()->create($data);

            //Add medias for employee
            if (!empty($request->medias)) {
                $this->addMedias($request->medias, $note, 'employee_note');
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
        if (!request()->user()->can('employeeNote.view')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $user_id = request()->get('user_id');
            $employee_note = Note::where('notable_id', $user_id)
                                ->where('notable_type', 'App\Components\User\Models\User')
                                ->with(['user', 'media'])
                                ->findOrFail($id);
                                
            $output = $this->respond($employee_note);
        } catch (Exception $e) {
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
        if (!request()->user()->can('employeeNote.edit')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $user_id = request()->get('user_id');
            $employee_note = Note::where('notable_id', $user_id)
                                ->where('notable_type', 'App\Components\User\Models\User')
                                ->findOrFail($id);

            $output = $this->respond($employee_note);
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
        if (!request()->user()->can('employeeNote.edit')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();

            $user_id = $request->get('user_id');
            $employee_note = Note::where('notable_id', $user_id)
                                ->where('notable_type', 'App\Components\User\Models\User')
                                ->findOrFail($id);

            $data = $request->only('heading', 'description');
            $employee_note->heading = $data['heading'];
            $employee_note->description = $data['description'];
            $employee_note->save();
            
            //Add medias for employee
            if (!empty($request->medias)) {
                $this->addMedias($request->medias, $employee_note, 'employee_note');
            }

            DB::commit();

            $output = $this->respondSuccess();
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
        if (!request()->user()->can('employeeNote.delete')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $user_id = request()->get('user_id');
            $note = Note::where('notable_id', $user_id)
                        ->where('notable_type', 'App\Components\User\Models\User')
                        ->findOrFail($id);
            $note->delete();

            $output = $this->respondSuccess();
        } catch (Exception $e) {
            $output = $this->respondWentWrong($e);
        }
        return $output;
    }
}

<?php

namespace App\Http\Controllers;

use App\Note;
use App\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectDocumentsAndNotesController extends Controller
{
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
        $project_id = request()->get('project_id');

        if ($descending == 'false') {
            $orderby = 'asc';
        } elseif ($descending == 'true') {
            $orderby = 'desc';
        } elseif ($descending == '') {
            $orderby = 'desc';
            $sort_by = 'id';
        }

        $project_note = Note::where('notable_id', $project_id)
                            ->where('notable_type', 'App\Project')
                            ->with('user')
                            ->orderBy($sort_by, $orderby)
                            ->paginate($rowsPerPage);

        return $this->respond($project_note);
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
        try {
            DB::beginTransaction();

            $data = $request->only('heading', 'description');
            $data['created_by'] = $request->user()->id;

            $project_id = $request->get('project_id');
            $project = Project::findOrFail($project_id);
            $note = $project->notes()->create($data);

            //Add medias for project
            if (!empty($request->medias)) {
                $this->addMedias($request->medias, $note, 'project_note');
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
        try {
            $project_id = request()->get('project_id');
            $project_note = Note::where('notable_id', $project_id)
                                ->with(['user', 'media'])
                                ->findOrFail($id);

            $output = $this->respond($project_note);
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
        try {
            $project_id = request()->get('project_id');
            $project_note = Note::where('notable_id', $project_id)
                                ->findOrFail($id);

            $output = $this->respond($project_note);
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
        try {
            DB::beginTransaction();

            $data = $request->only('heading', 'description');

            $project_id = $request->get('project_id');
            $project_note = Note::where('notable_id', $project_id)
                                ->findOrFail($id);

            $project_note->heading = $data['heading'];
            $project_note->description = $data['description'];
            $project_note->save();

            //Add medias for project
            if (!empty($request->medias)) {
                $this->addMedias($request->medias, $project_note, 'project_note');
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
        try {
            $project_id = request()->get('project_id');
            $note = Note::where('notable_id', $project_id)
                        ->findOrFail($id);
            $note->delete();

            $output = $this->respondSuccess();
        } catch (Exception $e) {
            $output = $this->respondWentWrong($e);
        }
        return $output;
    }
}

<?php

namespace App\Http\Controllers;

use App\Note;
use App\ProjectTask;
use Illuminate\Http\Request;

class ProjectCommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $project_task_id = request()->get('project_task_id');

        $task_note = Note::where('notable_id', $project_task_id)
                        ->where('notable_type', 'App\ProjectTask')
                        ->with(['user', 'media'])
                        ->latest()
                        ->get();

        return $this->respond($task_note);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
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
            $data = $request->only('description');
            $data['created_by'] = $request->user()->id;
            
            $task_id = $request->input('project_task_id');
            $project_task = ProjectTask::find($task_id);
            $note = $project_task->notes()->create($data);

            //Add medias for the project task
            if (!empty($request->medias)) {
                $medias = $request->medias;
                $this->addMedias($medias, $note, 'project_task');
            }

            $output = $this->respondSuccess(__('messages.saved_successfully'));
        } catch (\Exception $e) {
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
        //
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
        //
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
            $note = Note::findOrFail($id);

            $note->delete();
            
            $output = $this->respondSuccess(__('messages.deleted_successfully'));
        } catch (Exception $e) {
            $output = $this->respondWentWrong($e);
        }

        return $output;
    }
}

<?php

namespace App\Http\Controllers;

use App\ProjectMilestone;
use Illuminate\Http\Request;

class ProjectMilestonesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $project_id = request()->get('project_id');

        if (!request()->user()->can('project.'.$project_id.'.milestone.view')) {
            abort(403, 'Unauthorized action.');
        }

        $milestones = ProjectMilestone::where('project_id', $project_id)
                            ->latest()
                            ->get();
        return $milestones;
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
        $project_id = $request->input('project_id');

        if (!request()->user()->can('project.'.$project_id.'.milestone.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(
                'project_id',
                'name',
                'due_date',
                'description'
                );
            $input['created_by'] = $request->user()->id;

            $project_milestone = ProjectMilestone::create($input);

            $output = $this->respondSuccess(__('messages.saved_successfully'));
        } catch (Exception $e) {
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
        $project_id = request()->input('project_id');

        if (!request()->user()->can('project.'.$project_id.'.milestone.view')) {
            abort(403, 'Unauthorized action.');
        }

        if (!empty($id)) {
            $milestone = ProjectMilestone::find($id);

            return $this->respond($milestone);
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
        $project_id = request()->input('project_id');

        if (!request()->user()->can('project.'.$project_id.'.milestone.edit')) {
            abort(403, 'Unauthorized action.');
        }

        if (!empty($id)) {
            $milestone = ProjectMilestone::find($id);

            return $this->respond($milestone);
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
        $project_id = request()->input('project_id');

        if (!request()->user()->can('project.'.$project_id.'.milestone.edit')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(
                'name',
                'due_date',
                'description'
                );

            $project_milestone = ProjectMilestone::findOrFail($id);
            $project_milestone->name = $input['name'];
            $project_milestone->due_date = $input['due_date'];
            $project_milestone->description = $input['description'];
            $project_milestone->update();

            $output = $this->respondSuccess(__('messages.updated_successfully'));
        } catch (Exception $e) {
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
        $project_id = request()->input('project_id');
        
        if (!request()->user()->can('project.'.$project_id.'.milestone.delete')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            ProjectMilestone::destroy($id);

            $output = $this->respondSuccess(__('messages.deleted_successfully'));
        } catch (\Exception $e) {
            $output = $this->respondWentWrong($e);
        }

        return $output;
    }
}

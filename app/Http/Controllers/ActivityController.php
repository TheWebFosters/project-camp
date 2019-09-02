<?php

namespace App\Http\Controllers;

use App\Project;
use App\ProjectMilestone;
use App\ProjectTask;
use App\Transaction;
use Illuminate\Http\Request;

use Spatie\Activitylog\Models\Activity;

class ActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function project()
    {
        $project_id = request()->get('project_id');

        if (!request()->user()->can('project.'.$project_id.'.activities')) {
            abort(403, 'Unauthorized action.');
        }
        
        $project = Project::findOrFail($project_id);
        $activities = Activity::forSubject($project)
                    ->orWhere(function ($query) use ($project) {
                        $query->where('subject_type', (new ProjectTask())->getMorphClass())
                        ->whereIn('subject_id', $project->tasks()->pluck('id'));
                    })
                    ->orWhere(function ($query) use ($project) {
                        $query->where('subject_type', (new ProjectMilestone())->getMorphClass())
                        ->whereIn('subject_id', $project->milestones()->pluck('id'));
                    })
                    ->orWhere(function ($query) use ($project) {
                        $query->where('subject_type', (new Transaction())->getMorphClass())
                        ->whereIn('subject_id', $project->transactions()->pluck('id'));
                    })
            ->with(['causer', 'subject'])
            ->latest()
            ->simplePaginate(10);

        return $this->respond($activities);
    }
}

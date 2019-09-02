<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\Http\Controllers\Controller;
use App\KnowledgeBase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KnowledgeBaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!request()->user()->can('knowledge_base.view')) {
            abort(403, 'Unauthorized action.');
        }

        $user = $request->user();
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

        $query = KnowledgeBase::join('users', 'knowledge_bases.created_by', '=', 'users.id')
                        ->select('title', 'is_active', 'knowledge_bases.created_at', 'knowledge_bases.id', 'users.name as knowledge_base_creator');

        //add check for employee
        if (!$user->hasRole('superadmin')) {
            $query = $query->where('is_active', 1)
                        ->where('show_to_employee', 1)
                        ->orWhere('knowledge_bases.created_by', $user->id);
        }

        $knowledge_base = $query->orderBy($sort_by, $orderby)
                        ->paginate($rowsPerPage);

        return $this->respond($knowledge_base);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!request()->user()->can('knowledge_base.create')) {
            abort(403, 'Unauthorized action.');
        }

        $type = 'knowledge_base';
        $categories = Category::forDropdown($type);

        return $this->respond($categories);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!request()->user()->can('knowledge_base.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();

            $input = $request->only('title', 'description', 'show_to_employee', 'is_active');

            $input['show_to_employee'] = !empty($input['show_to_employee']) ? $input['show_to_employee'] : 0;
            $input['is_active'] = !empty($input['is_active']) ? $input['is_active'] : 0;

            $input['created_by'] = $request->user()->id;

            $knowledge_base = KnowledgeBase::create($input);

            //Add medias for KB
            if (!empty($request->medias)) {
                $this->addMedias($request->medias, $knowledge_base, 'knowledge_base');
            }

            //Add category
            $category = $request->input('category_id');
            $knowledge_base->categories()->sync($category);
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
        if (!request()->user()->can('knowledge_base.view')) {
            abort(403, 'Unauthorized action.');
        }

        $knowledge_base = KnowledgeBase::with('media', 'user')->find($id);

        return $this->respond($knowledge_base);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!request()->user()->can('knowledge_base.edit')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $knowledge_base = KnowledgeBase::with('categories')->find($id);

            $knowledge_base_category_id = [];
            foreach ($knowledge_base->categories as $category) {
                $knowledge_base_category_id[] = $category->id;
            }

            $type = 'knowledge_base';
            $categories = Category::forDropdown($type);
            
            $output = [
                    'categories' => $categories,
                    'knowledge_base' => $knowledge_base,
                    'knowledge_base_category_id' => $knowledge_base_category_id,
                ];
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
        if (!request()->user()->can('knowledge_base.edit')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();

            $input = $request->only('title', 'description', 'show_to_employee', 'is_active');

            $input['show_to_employee'] = !empty($input['show_to_employee']) ? $input['show_to_employee'] : 0;
            $input['is_active'] = !empty($input['is_active']) ? $input['is_active'] : 0;

            $knowledge_base = KnowledgeBase::find($id);
            $knowledge_base->update($input);

            //Add medias for KB
            if (!empty($request->medias)) {
                $this->addMedias($request->medias, $knowledge_base, 'knowledge_base');
            }

            //update category
            $category = $request->input('category_id');
            $knowledge_base->categories()->sync($category);
            DB::commit();

            $output = $this->respondSuccess(__('messages.updated_successfully'));
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
        if (!request()->user()->can('knowledge_base.delete')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $knowledge_base = KnowledgeBase::find($id);
            $knowledge_base->delete();
            $knowledge_base->categories()->delete();

            $output = $this->respondSuccess(__('messages.deleted_successfully'));
        } catch (\Exception $e) {
            $output = $this->respondWentWrong($e);
        }

        return $output;
    }
}

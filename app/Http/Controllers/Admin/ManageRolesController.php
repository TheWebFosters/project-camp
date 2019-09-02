<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class ManageRolesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
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

        $roles = Role::where('type', 'employee')
                    ->select('name', 'created_at', 'id');

        if (!empty($request->get('name'))) {
            $term = $request->get('name');
            $roles->where('name', 'like', "%$term%");
        }

        $roles = $roles->orderBy($sort_by, $orderby)
                    ->paginate($rowsPerPage);

        return $this->respond($roles);
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

            $role_name = $request->input('name');
            $permissions = $request->input('permissions');
            
            $count = Role::where('name', $role_name)
                        ->where('type', 'employee')
                        ->count();

            if ($count == 0) {
                $role = Role::create([
                            'name' => $role_name,
                            'type' => 'employee',
                        ]);

                if (!empty($permissions)) {
                    $role->syncPermissions($permissions);
                }

                DB::commit();
                
                $output = $this->respondSuccess(__('messages.saved_successfully'));
            } else {
                $output = $this->respondWithError(__('messages.role_already_existed'));
            }
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
        $role = Role::where('type', 'employee')
                ->find($id);

        $role_permissions = [];
        foreach ($role->permissions as $role_perm) {
            $role_permissions[] = $role_perm->name;
        }

        $data = [
                'role' => $role->name,
                'permissions' => $role_permissions
            ];

        return $this->respond($data);
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

            $role_name = $request->input('name');
            $permissions = $request->input('permissions');
            
            $count = Role::where('name', $role_name)
                        ->where('type', 'employee')
                        ->where('id', '!=', $id)
                        ->count();

            if ($count == 0) {
                $role = Role::find($id);

                $role->name = $role_name;
                $role->save();
                
                if (!empty($permissions)) {
                    $role->syncPermissions($permissions);
                }

                DB::commit();
                
                $output = $this->respondSuccess(__('messages.updated_successfully'));
            } else {
                $output = $this->respondWithError(__('messages.role_already_existed'));
            }
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
            $role = Role::where('type', 'employee')
                        ->find($id);
                        
            $role->delete();

            $output = $this->respondSuccess(__('messages.deleted_successfully'));
        } catch (Exception $e) {
            $output = $this->respondWentWrong($e);
        }
        return $output;
    }
}

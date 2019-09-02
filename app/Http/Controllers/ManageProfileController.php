<?php

namespace App\Http\Controllers;

use App\Components\User\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ManageProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $user_id = request()->user()->id;
            $user = User::with('media')->findOrFail($user_id);

            $output = $this->respond($user);
        } catch (Exception $e) {
            $output = $this->respondWentWrong($e);
        }

        return $output;
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
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!request()->user()->can('profile.edit')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $user_id = request()->user()->id;
            $user = User::findOrFail($user_id);
            $gender_types = User::getGenders();

            $output = $this->respond(['user' => $user, 'gender_types' => $gender_types]);
        } catch (Exception $e) {
            $output = $this->respondWentWrong($e);
        }

        return $output;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!request()->user()->can('profile.edit')) {
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
            if ($this->isDemo()) {
                return $this->respondDemo();
            }

            $data = $request->only(
                'name',
                'email',
                'password',
                'mobile',
                'alternate_num',
                'skype',
                'linkedin',
                'facebook',
                'twitter',
                'gender',
                'home_address',
                'current_address',
                'guardian_name',
                'gender',
                'birth_date'
            );

            //password is present but has null/empty
            //unset it
            if (empty($data['password'])) {
                unset($data['password']);
            }

            DB::beginTransaction();

            $user_id = request()->user()->id;
            $user = User::findOrFail($user_id);
            $user->update($data);

            //Add medias for employee
            if (!empty($request->media)) {
                $profile_img[] = $request->media;
                $this->addMedias($profile_img, $user, 'avatar');
            }

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
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    }
}

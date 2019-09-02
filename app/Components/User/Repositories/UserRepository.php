<?php

namespace App\Components\User\Repositories;

use App\Components\Core\BaseRepository;
use App\Components\User\Models\User;

class UserRepository extends BaseRepository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * list all users
     *
     * @param array $params
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model[]|mixed[]
     */
    public function listUsers($params)
    {
        return $this->get($params);
    }

    /**
     * delete a user by id
     *
     * @param int $id
     * @return bool
     * @throws \Exception
     */
    public function delete(int $id)
    {
        $ids = explode(',', $id);

        foreach ($ids as $id) {
            /** @var User $User */
            $User = $this->model->find($id);
            
            if (!$User) {
                return false;
            };
            
            $User->syncRoles([]);
            $User->syncPermissions([]);
            $User->delete();
        }

        return true;
    }
}

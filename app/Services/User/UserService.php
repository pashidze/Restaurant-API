<?php

namespace App\Services\User;

use App\Models\Role;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function create($data)
    {
        try {

            //Подтверждение пароля
            if($data['password'] !== $data['password_confirmation']) {
                throw new Exception('пароли не совпадают!', 500);
            }

            //Подтверждение пин-кода
            if($data['pin_code'] !== $data['pin_code_confirmation']) {
                throw new Exception('Пин-коды не совпадают!', 500);
            }

            DB::beginTransaction();

            //Хеширование пароля и пин-кода
            $data['password'] = Hash::make($data['password']);
            $data['pin_code'] = Hash::make($data['pin_code']);

            $user = User::create($data);

            DB::commit();

            //Объект для JSON
            return $user;
        } catch (Exception $exception) {
            DB::rollBack();

            throw new Exception($exception->getMessage(), 500);
        }
    }

    public function update($user, $data)
    {
        try {
            DB::beginTransaction();

            $user->update($data);

            DB::commit();

            return $user;
        } catch (Exception $exception) {
            DB::rollBack();

            throw new Exception($exception->getMessage(), 500);
        }
    }

    public function delete($user)
    {
        try {
            DB::beginTransaction();

            $user->delete();

            DB::commit();

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();

            throw new Exception($exception->getMessage(), 500);
        }
    }

    public function filter($data)
    {
        try {
            $query = User::query()->with('role');

            //поиск id роли по её наименованию
            if(isset($data['role']))
            {
                $roleQuery = Role::query()->where('name', 'like', '%' . $data['role'] . '%');
                $rolesIds = $roleQuery->pluck('id')->toArray();
                $query->whereIn('role_id', $rolesIds);
            }

            //То же самое, но надо разобраться
            /*$query = User::whereHas('role', function ($roleQuery) use ($data) {
                $roleQuery->where('name', 'like', '%' . $data['role'] . '%');
            })->with('role');*/

            isset($data['name']) ? $query->where('users.name', 'like', '%' . $data['name'] . '%') : null;
            isset($data['email']) ? $query->where('users.email', 'like', '%' . $data['email'] . '%') : null;

            $page = $data['page'] ?? 1;
            $perPage = $data['per_page'] ?? 10;

            $orderBy = $data['sort_by'] ?? 'users.id';
            isset($data['sort_by']) && $data['sort_by'] === 'role' ? $orderBy = 'users.role_id' : null;
            $orderDir = $data['sort_dir'] ?? 'asc';

            $query->orderBy($orderBy, $orderDir);

            return $query->paginate($perPage, ['*'], 'page', $page);
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage(), 500);
        }

    }
}

<?php

namespace App\Services\User;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function login($user, $data)
    {
        try {
            //проверка пароля
            if(isset($data['password'])) {
                //если официант с паролем, то ошибка
                if($user->role_id === 3) throw new Exception('Официант может входить только по пин коду', 401);

                //если пароль верный, то создание и вывод токена, иначе ошибка
                if(Hash::check($data['password'], $user->password)) {
                    return $user->createToken('auth_token', [$user->role_id])->plainTextToken;
                } else {
                    throw new Exception('Неверный пароль или пин код!', 403);
                }
            }

            //проверка пин кода, если верный, то создание и вывод токена, если нет, то ошибка
            if(isset($data['pin_code']) && Hash::check($data['pin_code'], $user->pin_code)) {
                return $user->createToken('auth_token', [$user->role_id])->plainTextToken;
            } else {
                throw new Exception('Неверный пароль или пин код!', 403);
            }

        } catch (Exception $exception) {
            throw new Exception($exception->getMessage(), $exception->getCode());
        }
    }

    public function userSearch($data)
    {
        try{
            //поиск пользователя, проверка, что найден
            $user = User::where('email', $data['email'])->first();
            !$user ? throw new Exception('Пользователь с таким email не найден!', 403) : null;
            return $user;
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage(), $exception->getCode());
        }
    }
}

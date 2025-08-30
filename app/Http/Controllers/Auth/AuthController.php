<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginUserRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Resources\User\LoginResource;
use App\Models\User;
use App\Services\User\AuthService;
use Exception;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Авторизация",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(
     *                     @OA\Property(property="email", type="string", example="email@gmail.com"),
     *                     @OA\Property(property="password", type="string", example="123456789"),
     *                     @OA\Property(property="pin_code", type="string", example="1234"),
     *                 )
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Пользователь авторизован",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="token", type="string", example="1|tW9TJIbIxVrKnjeJ7Pfg2gDeQb5lOS7KuucGtEkLee9da4cd"),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Неправильный логин и/или пароль",
     *         @OA\JsonContent(
     *             @OA\Property(property="Error", type="boolean", example="true"),
     *             @OA\Property(property="Message", type="string", example="Неверный пароль или пин-код"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Ошибка валидации",
     *         @OA\JsonContent(
     *             @OA\Property(property="Error", type="boolean", example="true"),
     *             @OA\Property(property="Message", type="string", example="email has been requred"),
     *         ),
     *     ),
     *     @OA\Response(
     *          response=500,
     *          description="Ошибка сервера",
     *          @OA\JsonContent(
     *              @OA\Property(property="Error", type="boolean", example="true"),
     *              @OA\Property(property="Message", type="string", example="Error text"),
     *          ),
     *      ),
     * ),
     */
    public function login(LoginUserRequest $request, AuthService $authService)
    {
        try {
            //валидация мейла и пароля/кода
            $data = $request->validated();

            //поиск пользователя, проверка, что найден
            $user = $authService->userSearch($data);

            //генерация токена
            $token = $authService->login($user, $data);

            //вывод токена
            return new LoginResource($token);
        } catch (Exception $exception) {
            return response()->json(['Error' => true, 'Message' => $exception->getMessage()], $exception->getCode());
        }
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Выход",
     *     tags={"Auth"},
     *     security={{ "sanctum":{} }},
     *     @OA\Response(
     *         response=200,
     *         description="Выход произведен",
     *         @OA\JsonContent(
     *             @OA\Property(property="Message", type="string", example="Выход произведён"),
     *         ),
     *     ),
     *     @OA\Response(
     *           response=500,
     *           description="Ошибка сервера",
     *           @OA\JsonContent(
     *               @OA\Property(property="Error", type="boolean", example="true"),
     *               @OA\Property(property="Message", type="string", example="Error text"),
     *           ),
     *       ),
     * ),
     */
    public function logout(Request $request)
    {
        try {
            //Удаление текущего токена пользователя
            $request->user()->currentAccessToken()->delete();
            return response()->json(['Message' => 'Выход произведён'], 200);
        } catch (Exception $exception) {
            return response()->json(['Error' => true, 'Message' => $exception->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/logout_all",
     *     summary="Выход со всех устройств",
     *     tags={"Auth"},
     *     security={{ "sanctum":{} }},
     *     @OA\Response(
     *         response=200,
     *         description="Выход со всех устройсв произведен",
     *         @OA\JsonContent(
     *             @OA\Property(property="Message", type="string", example="Произведён выход со всех устройств!"),
     *         ),
     *     ),
     *     @OA\Response(
     *           response=500,
     *           description="Ошибка сервера",
     *           @OA\JsonContent(
     *               @OA\Property(property="Error", type="boolean", example="true"),
     *               @OA\Property(property="Message", type="string", example="Error text"),
     *           ),
     *       ),
     * ),
     */
    public function logoutAll(Request $request)
    {
        try {
            //Удаление всех токенов пользователя
            $request->user()->tokens()->delete();
            return response()->json(['Message' => 'Произведён выход со всех устройств!'], 200);
        } catch (Exception $exception) {
            return response()->json(['Error' => true, 'Message' => $exception->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/forgot-password",
     *     summary="Сброс пароля",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(
     *                     @OA\Property(property="email", type="string", example="email@gmail.com"),
     *                 )
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="пароль сброшен",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="We send mail"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Ошибка на клиенте",
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string", example="email error"),
     *         )
     *     ),
     *     @OA\Response(
     *          response=422,
     *          description="Ошибка валидации",
     *          @OA\JsonContent(
     *              @OA\Property(property="Error", type="boolean", example="true"),
     *              @OA\Property(property="Message", type="string", example="email has been requred"),
     *          ),
     *      ),
     *     @OA\Response(
     *            response=500,
     *            description="Ошибка сервера",
     *            @OA\JsonContent(
     *                @OA\Property(property="Error", type="boolean", example="true"),
     *                @OA\Property(property="Message", type="string", example="Error text"),
     *            ),
     *        ),
     * ),
     */
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        try {
            $data = $request->validated();

            //Формирование данных для сброса пароля и направление письма с данными на почту
            $status = Password::sendResetLink($data);

            //JSON ответ
            return $status === Password::ResetLinkSent
                ? response()->json(['status' => __($status)])
                : response()->json(['email' => __($status)], 400); /*route('login')->with(['status' => __($status)]) : back()->withErrors(['email' => __($status)]);*/

        } catch (Exception $exception) {
            return response()->json(['Error' => true, 'Message' => $exception->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/reset-password/{token}",
     *     summary="Восстановление пароля",
     *     tags={"Auth"},
     *     @OA\Parameter(
     *         description="Токен",
     *         in="path",
     *         name="token",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         example="c1bb927ef7fdd9c1efda0d2ee650f8887ba5c1fcd4c573ab51176316a07baba8",
     *     ),
     *     @OA\Parameter(
     *          description="Email",
     *          in="query",
     *          name="email",
     *          required=true,
     *          @OA\Schema(type="string"),
     *          example="email@gmail.com",
     *      ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(
     *                     @OA\Property(property="token", type="string", example="c1bb927ef7fdd9c1efda0d2ee650f8887ba5c1fcd4c573ab51176316a07baba8"),
     *                     @OA\Property(property="email", type="string", example="email@gmail.com"),
     *                     @OA\Property(property="password", type="string", example="123456789"),
     *                     @OA\Property(property="password_confirmation", type="string", example="123456789"),
     *                 ),
     *             },
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Пароль восстановлен",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Password has been restored"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Ошибка на клиенте",
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string", example="email error"),
     *         )
     *     ),
     *     @OA\Response(
     *          response=422,
     *          description="Ошибка валидации",
     *          @OA\JsonContent(
     *              @OA\Property(property="Error", type="boolean", example="true"),
     *              @OA\Property(property="Message", type="string", example="email has been requred"),
     *          ),
     *      ),
     *     @OA\Response(
     *            response=500,
     *            description="Ошибка сервера",
     *            @OA\JsonContent(
     *                @OA\Property(property="Error", type="boolean", example="true"),
     *                @OA\Property(property="Message", type="string", example="Error text"),
     *            ),
     *        ),
     * ),
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        try {
            $data = $request->validated();

            //Редактирование пароля в БД
            $status = Password::reset($data, function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ]);
                $user->save();
                event(new PasswordReset($user));
            }
            );

            //JSON ответ
            return $status === Password::PasswordReset
                ? response()->json(['status' => __($status)])
                : response()->json(['email' => __($status)], 400); /*back()->with(['status' => __($status)]) : back()->withErrors(['email' => __($status)]);*/

        } catch (Exception $exception) {
            return response()->json(['Error' => true, 'Message' => $exception->getMessage()], 500);
        }
    }
}

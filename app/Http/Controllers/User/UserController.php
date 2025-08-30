<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\FilterUserRequest;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use App\Services\User\UserService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/user",
     *     summary="Вывод списка пользователей",
     *     tags={"User"},
     *     security={{ "sanctum":{} }},
     *     @OA\Parameter(
     *         description="Страница",
     *         in="query",
     *         name="page",
     *         required=false,
     *         @OA\Schema(type="integer"),
     *         example=1,
     *     ),
     *     @OA\Parameter(
     *          description="Записей на странице",
     *          in="query",
     *          name="per_page",
     *          required=false,
     *          @OA\Schema(type="integer"),
     *          example=20,
     *      ),
     *     @OA\Parameter(
     *          description="ФИО",
     *          in="query",
     *          name="name",
     *          required=false,
     *          @OA\Schema(type="string"),
     *          example="Иван",
     *      ),
     *     @OA\Parameter(
     *          description="Роль",
     *          in="query",
     *          name="role",
     *          required=false,
     *          @OA\Schema(type="string"),
     *          example="Официант",
     *      ),
     *     @OA\Parameter(
     *          description="Email",
     *          in="query",
     *          name="email",
     *          required=false,
     *          @OA\Schema(type="string"),
     *          example="email@gmail.com",
     *      ),
     *     @OA\Parameter(
     *          description="Поле для сортировки",
     *          in="query",
     *          name="sort_by",
     *          required=false,
     *          @OA\Schema(type="string"),
     *          example="name",
     *      ),
     *     @OA\Parameter(
     *          description="Направление сортировки",
     *          in="query",
     *          name="sort_dir",
     *          required=false,
     *          @OA\Schema(type="string"),
     *          example="desc",
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Ok",
     *         @OA\JsonContent(
     *              @OA\Property(property="data", type="array", @OA\Items(
     *                  @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="name", type="string", example="Иван Иванов"),
     *                  @OA\Property(property="email", type="string", example="email@gmail.com"),
     *                  @OA\Property(property="password", type="string", example="$2y$12$Vktr4k7WAyo0QWflwnsvlO.YU7XbqOMEQrYj0jp"),
     *                  @OA\Property(property="pin_code", type="string", example="$2y$12$Vktr4k7WAyo0QWflwnsvlO.YU7XbqOMEQrYj0jp"),
     *                  @OA\Property(property="role", type="object",
     *                      @OA\Property(property="id", type="integer", example=1),
     *                      @OA\Property(property="name", type="string", example="Официант"),
     *                  ),
     *              )),
     *          ),
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
    public function index(FilterUserRequest $request, UserService $userService)
    {
        try {
            $data = $request->validated();

            $users = $userService->filter($data);

            return UserResource::collection($users);
        } catch (Exception $exception) {
            return response()->json(['Error' => true ,'Message' => $exception->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/user",
     *     summary="Ввод нового пользователя",
     *     tags={"User"},
     *     security={{ "sanctum":{} }},
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(
     *                      @OA\Property(property="name", type="string", example="Иван Иванов"),
     *                      @OA\Property(property="email", type="string", example="email@gmail.com"),
     *                      @OA\Property(property="password", type="string", example="123456789"),
     *                      @OA\Property(property="password_confirmation", type="string", example="123456789"),
     *                      @OA\Property(property="pin_code", type="string", example="1234"),
     *                      @OA\Property(property="pin_code_confirmation", type="string", example="1234"),
     *                      @OA\Property(property="role_id", type="integer", example="1"),
     *                  ),
     *              },
     *          ),
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Ok",
     *         @OA\JsonContent(
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="name", type="string", example="Иван Иванов"),
     *                  @OA\Property(property="email", type="string", example="email@gmail.com"),
     *              ),
     *          ),
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
    public function store(StoreUserRequest $request, UserService $userService)
    {
        try {
            $data = $request->validated();

            $user = $userService->create($data);

            return new UserResource($user);
        } catch (Exception $exception) {
            return response()->json(['Error' => true ,'Message' => $exception->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/user/{id}",
     *     summary="Вывод одного пользователя",
     *     tags={"User"},
     *     security={{ "sanctum":{} }},
     *     @OA\Parameter(
     *          description="Id",
     *          in="path",
     *          name="id",
     *          required=true,
     *          @OA\Schema(type="integer"),
     *          example=1,
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Ok",
     *         @OA\JsonContent(
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="name", type="string", example="Иван Иванов"),
     *                  @OA\Property(property="email", type="string", example="email@gmail.com"),
     *                  @OA\Property(property="role", type="object",
     *                      @OA\Property(property="id", type="integer", example=1),
     *                      @OA\Property(property="name", type="string", example="Официант"),
     *                  ),
     *              ),
     *          ),
     *     ),
     *     @OA\Response(
     *            response=404,
     *            description="Запись не найдена",
     *            @OA\JsonContent(
     *                @OA\Property(property="Error", type="boolean", example="true"),
     *                @OA\Property(property="Message", type="string", example="Error text"),
     *            ),
     *        ),
     * ),
     */
    public function show($id)
    {
        try {
            $user = User::with('role')->findOrFail($id);

            return new UserResource($user);
        } catch (ModelNotFoundException $exception) {
            return response()->json(['Error' => true ,'Message' => 'Запись не найдена!'], 404);
        }
    }

    /**
     * @OA\Patch(
     *     path="/api/user/{id}",
     *     summary="Редактирование данных о пользователе",
     *     tags={"User"},
     *     security={{ "sanctum":{} }},
     *     @OA\Parameter(
     *           description="Id",
     *           in="path",
     *           name="id",
     *           required=true,
     *           @OA\Schema(type="integer"),
     *           example=1,
     *       ),
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(
     *                      @OA\Property(property="name", type="string", example="Иван Иванов"),
     *                      @OA\Property(property="email", type="string", example="email@gmail.com"),
     *                      @OA\Property(property="role_id", type="integer", example="1"),
     *                  ),
     *              },
     *          ),
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Ok",
     *         @OA\JsonContent(
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="name", type="string", example="Иван Иванов"),
     *                  @OA\Property(property="email", type="string", example="email@gmail.com"),
     *              ),
     *          ),
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
     *     @OA\Response(
     *             response=404,
     *             description="Запись не найдена",
     *             @OA\JsonContent(
     *                 @OA\Property(property="Error", type="boolean", example="true"),
     *                 @OA\Property(property="Message", type="string", example="Error text"),
     *             ),
     *         ),
     * ),
     */
    public function update(UpdateUserRequest $request, $id, UserService $userService)
    {
        try {
            $user = User::findOrFail($id);

            $data = $request->validated();

            $upUser = $userService->update($user, $data);

            return new UserResource($upUser);
        } catch (ModelNotFoundException $modelNotFoundException) {
            return response()->json(['Error' => true ,'Message' => 'Запись не найдена!'], 404);
        } catch (Exception $exception) {
            return response()->json(['Error' => true ,'Message' => $exception->getMessage()], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/user/{id}",
     *     summary="Удаление данных о пользователе",
     *     tags={"User"},
     *     security={{ "sanctum":{} }},
     *     @OA\Parameter(
     *           description="Id",
     *           in="path",
     *           name="id",
     *           required=true,
     *           @OA\Schema(type="integer"),
     *           example=1,
     *       ),
     *     @OA\Response(
     *         response=200,
     *         description="Ok",
     *         @OA\JsonContent(
     *              @OA\Property(property="Success", type="boolean", example="true"),
     *              @OA\Property(property="Message", type="string", example="Запись удалена!"),
     *          ),
     *     ),
     *     @OA\Response(
     *             response=404,
     *             description="Запись не найдена",
     *             @OA\JsonContent(
     *                 @OA\Property(property="Error", type="boolean", example="true"),
     *                 @OA\Property(property="Message", type="string", example="Error text"),
     *             ),
     *         ),
     * ),
     */
    public function destroy($id, UserService $userService)
    {
        try {
            $user = User::findOrFail($id);

            $userService->delete($user);

            return response()->json(['Success' => true ,'Message' => 'Запись удалена!'], 200);
        } catch (ModelNotFoundException $modelNotFoundException) {
            return response()->json(['Error' => true ,'Message' => 'Запись не найдена!'], 404);
        }
    }
}

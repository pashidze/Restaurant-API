<?php

namespace App\Http\Controllers\MenuCategory;

use App\Http\Controllers\Controller;
use App\Http\Requests\MenuCategory\FilterMenuRequest;
use App\Http\Requests\MenuCategory\StoreMenuRequest;
use App\Http\Requests\MenuCategory\UpdateMenuRequest;
use App\Http\Resources\MenuCategory\MenuResource;
use App\Models\MenuCategory;
use App\Services\MenuCategory\MenuService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class MenuController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/menu_category",
     *     summary="Вывод списка категорий меню",
     *     tags={"Menu category"},
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
     *          description="Наименование",
     *          in="query",
     *          name="name",
     *          required=false,
     *          @OA\Schema(type="string"),
     *          example="Горячие блюда",
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
     *                  @OA\Property(property="name", type="string", example="Горячие блюда"),
     *                  @OA\Property(property="image", type="string", example="Images/MenuCategory/S9EXsmeHwGPkewmzcm0f6T9RtLHiYjzjvEj9z1Tx.jpg"),
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
    public function index(FilterMenuRequest $request, MenuService $menuService)
    {
        try {
            $data = $request->validated();

            $menu = $menuService->filter($data);

            return MenuResource::collection($menu);
        } catch (Exception $exception) {
            return response()->json(['Error' => true, 'Message' => $exception->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/menu_category",
     *     summary="Ввод новой категории меню",
     *     tags={"Menu category"},
     *     security={{ "sanctum":{} }},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="name", type="string", example="Горячие блюда"),
     *                 @OA\Property(property="image", type="string", format="binary"),
     *             ),
     *         ),
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Ok",
     *         @OA\JsonContent(
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="id", type="integer", example=1),
     *                   @OA\Property(property="name", type="string", example="Горячие блюда"),
     *                   @OA\Property(property="image", type="string", example="Images/MenuCategory/S9EXsmeHwGPkewmzcm0f6T9RtLHiYjzjvEj9z1Tx.jpg"),
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
    public function store(StoreMenuRequest $request, MenuService $menuService)
    {
        try {
            $data = $request->validated();

            $category = $menuService->store($data);

            return new MenuResource($category);
        } catch (Exception $exception) {
            return response()->json(['Error' => true, 'Message' => $exception->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/menu_category/{id}",
     *     summary="Вывод одной категории меню",
     *     tags={"Menu category"},
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
     *                  @OA\Property(property="name", type="string", example="Горячие блюда"),
     *                  @OA\Property(property="image", type="string", example="Images/MenuCategory/S9EXsmeHwGPkewmzcm0f6T9RtLHiYjzjvEj9z1Tx.jpg"),
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
            $category = MenuCategory::findOrFail($id);

            return new MenuResource($category);
        } catch (ModelNotFoundException $exception) {
            return response()->json(['Error' => true, 'Message' => 'Запись не найдена!'], 404);
        }
    }

    /**
     * @OA\Patch(
     *     path="/api/menu_category/{id}",
     *     summary="Редактирование данных о категории меню",
     *     tags={"Menu category"},
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
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property(property="name", type="string", example="Горячие блюда"),
     *                  @OA\Property(property="image", type="string", format="binary"),
     *              ),
     *          ),
     *       ),
     *     @OA\Response(
     *         response=200,
     *         description="Ok",
     *         @OA\JsonContent(
     *               @OA\Property(property="data", type="object",
     *                   @OA\Property(property="id", type="integer", example=1),
     *                   @OA\Property(property="name", type="string", example="Горячие блюда"),
     *                   @OA\Property(property="image", type="string", example="Images/MenuCategory/S9EXsmeHwGPkewmzcm0f6T9RtLHiYjzjvEj9z1Tx.jpg"),
     *               ),
     *           ),
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
    public function update(UpdateMenuRequest $request, $id, MenuService $menuService)
    {
        try {
            $category = MenuCategory::findOrFail($id);

            $data = $request->validated();

            $upCategory = $menuService->update($category, $data);

            return new MenuResource($upCategory);
        } catch (ModelNotFoundException $exception) {
            return response()->json(['Error' => true, 'Message' => 'Запись не найдена!'], 404);
        } catch (Exception $exception) {
            return response()->json(['Error' => true, 'Message' => $exception->getMessage()], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/menu_category/{id}",
     *     summary="Удаление данных о категории меню",
     *     tags={"Menu category"},
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
    public function destroy($id, MenuService $menuService)
    {
        try {
            $category = MenuCategory::findOrFail($id);

            $menuService->delete($category);

            return response()->json(['Success' => true]);
        } catch (ModelNotFoundException $exception) {
            return response()->json(['Error' => true, 'Message' => 'Запись не найдена!'], 404);
        }
    }
}

<?php

namespace App\Http\Controllers\Dish;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dish\FilterDishRequest;
use App\Http\Requests\Dish\StoreDishRequest;
use App\Http\Requests\Dish\UpdateDishRequest;
use App\Http\Resources\Dish\DishResource;
use App\Models\Dish;
use App\Services\Dish\DishService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DishController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/dish",
     *     summary="Вывод списка блюд",
     *     tags={"Dish"},
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
     *          example="Кофе",
     *      ),
     *     @OA\Parameter(
     *           description="Состав",
     *           in="query",
     *           name="composition",
     *           required=false,
     *           @OA\Schema(type="string"),
     *           example="Вода, сахар,...",
     *      ),
     *     @OA\Parameter(
     *           description="Категория меню",
     *           in="query",
     *           name="category_id",
     *           required=false,
     *           @OA\Schema(type="integer"),
     *           example=1,
     *       ),
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
     *                  @OA\Property(property="name", type="string", example="Кофе"),
     *                  @OA\Property(property="image", type="string", example="Images/Dish/S9EXsmeHwGPkewmzcm0f6T9RtLHiYjzjvEj9z1Tx.jpg"),
     *                  @OA\Property(property="composition", type="string", example="Вода, сахар,..."),
     *                  @OA\Property(property="calories", type="float", example="122"),
     *                  @OA\Property(property="price", type="float", example="68.5"),
     *                  @OA\Property(property="category_id", type="integer", example=1),
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
    public function index(FilterDishRequest $request, DishService $dishService)
    {
        try {
            $data = $request->validated();

            $dishes = $dishService->filter($data);

            return DishResource::collection($dishes);
        } catch (Exception $exception) {
            return response()->json(['Error' => true, 'Message' => $exception->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/dish",
     *     summary="Ввод нового блюда",
     *     tags={"Dish"},
     *     security={{ "sanctum":{} }},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="name", type="string", example="Кофе"),
     *                 @OA\Property(property="image", type="string", format="binary"),
     *                 @OA\Property(property="composition", type="string", example="Вода, сахар,..."),
     *                 @OA\Property(property="calories", type="float", example="122"),
     *                 @OA\Property(property="price", type="float", example="68.5"),
     *                 @OA\Property(property="category_id", type="integer", example=1),
     *             ),
     *         ),
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Ok",
     *         @OA\JsonContent(
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="name", type="string", example="Кофе"),
     *                  @OA\Property(property="image", type="string", example="Images/Dish/S9EXsmeHwGPkewmzcm0f6T9RtLHiYjzjvEj9z1Tx.jpg"),
     *                  @OA\Property(property="composition", type="string", example="Вода, сахар,..."),
     *                  @OA\Property(property="calories", type="float", example="122"),
     *                  @OA\Property(property="price", type="float", example="68.5"),
     *                  @OA\Property(property="category_id", type="integer", example=1),
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
    public function store(StoreDishRequest $request, DishService $dishService)
    {
        try {
            $data = $request->validated();

            $dish = $dishService->store($data);

            return new DishResource($dish);
        } catch (\Exception $exception) {
            return response()->json(['Error' => true, 'Message' => $exception->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/dish/{id}",
     *     summary="Вывод одного блюда",
     *     tags={"Dish"},
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
     *                  @OA\Property(property="name", type="string", example="Кофе"),
     *                  @OA\Property(property="image", type="string", example="Images/Dish/S9EXsmeHwGPkewmzcm0f6T9RtLHiYjzjvEj9z1Tx.jpg"),
     *                  @OA\Property(property="composition", type="string", example="Вода, сахар,..."),
     *                  @OA\Property(property="calories", type="float", example="122"),
     *                  @OA\Property(property="price", type="float", example="68.5"),
     *                  @OA\Property(property="category_id", type="integer", example=1),
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
            $dish = Dish::findOrFail($id);

            return new DishResource($dish);
        } catch (ModelNotFoundException $exception) {
            return response()->json(['Error' => true, 'Message' => 'Запись не найдена!'], 404);
        }
    }

    /**
     * @OA\Patch(
     *     path="/api/dish/{id}",
     *     summary="Редактирование данных о блюде",
     *     tags={"Dish"},
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
     *                  @OA\Property(property="name", type="string", example="Кофе"),
     *                  @OA\Property(property="image", type="string", format="binary"),
     *                  @OA\Property(property="composition", type="string", example="Вода, сахар,..."),
     *                  @OA\Property(property="calories", type="float", example="122"),
     *                  @OA\Property(property="price", type="float", example="68.5"),
     *                  @OA\Property(property="category_id", type="integer", example=1),
     *              ),
     *          ),
     *       ),
     *     @OA\Response(
     *         response=200,
     *         description="Ok",
     *         @OA\JsonContent(
     *               @OA\Property(property="data", type="object",
     *                   @OA\Property(property="id", type="integer", example=1),
     *                   @OA\Property(property="name", type="string", example="Кофе"),
     *                   @OA\Property(property="image", type="string", example="Images/Dish/S9EXsmeHwGPkewmzcm0f6T9RtLHiYjzjvEj9z1Tx.jpg"),
     *                   @OA\Property(property="composition", type="string", example="Вода, сахар,..."),
     *                   @OA\Property(property="calories", type="float", example="122"),
     *                   @OA\Property(property="price", type="float", example="68.5"),
     *                   @OA\Property(property="category_id", type="integer", example=1),
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
    public function update(UpdateDishRequest $request, $id, DishService $dishService)
    {
        try {
            $dish = Dish::findOrFail($id);

            $data = $request->validated();

            $upDish = $dishService->update($dish, $data);

            return new DishResource($upDish);
        } catch (ModelNotFoundException $exception) {
            return response()->json(['Error' => true, 'Message' => 'Запись не найдена!'], 404);
        } catch (Exception $exception) {
            return response()->json(['Error' => true, 'Message' => $exception->getMessage()], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/dish/{id}",
     *     summary="Удаление данных о блюде",
     *     tags={"Dish"},
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
    public function destroy($id, DishService $dishService)
    {
        try {
            $dish = Dish::findOrFail($id);

            $dishService->delete($dish);

            return response()->json(['Success' => true]);
        } catch (ModelNotFoundException $exception) {
            return response()->json(['Error' => true, 'Message' => 'Запись не найдена!'], 500);
        }
    }
}

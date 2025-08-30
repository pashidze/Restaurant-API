<?php

namespace App\Http\Controllers\Dish;

use App\Http\Controllers\Controller;
use App\Http\Requests\MenuCategory\FilterMenuRequest;
use App\Http\Resources\MenuCategory\MenuResource;
use App\Models\MenuCategory;
use App\Services\Dish\ShowMenuService;
use Exception;

class ShowMenuController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/menu",
     *     summary="Просмотр меню",
     *     tags={"Show menu"},
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
     *          example="Завтрак",
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
     *                  @OA\Property(property="name", type="string", example="Завтрак"),
     *                  @OA\Property(property="image", type="string", example="Images/MenuCategory/S9EXsmeHwGPkewmzcm0f6T9RtLHiYjzjvEj9z1Tx.jpg"),
     *                  @OA\Property(property="dishes", type="array", @OA\Items(
     *                      @OA\Property(property="id", type="integer", example=1),
     *                      @OA\Property(property="name", type="string", example="Кофе"),
     *                      @OA\Property(property="image", type="string", example="Images/Dish/S9EXsmeHwGPkewmzcm0f6T9RtLHiYjzjvEj9z1Tx.jpg"),
     *                      @OA\Property(property="composition", type="string", example="Вода, сахар,..."),
     *                      @OA\Property(property="calories", type="float", example="122"),
     *                      @OA\Property(property="price", type="float", example="68.5"),
     *                      @OA\Property(property="category_id", type="integer", example=1),
     *                  )),
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
    public function index(FilterMenuRequest $request, ShowMenuService $showMenuService)
    {
        try {
            $data = $request->validated();

            $menu = $showMenuService->filter($data);

            return MenuResource::collection($menu);
        } catch (Exception $exception) {
            return response()->json(['Error' => true, 'Message' => $exception->getMessage()], 500);
        }
    }
}

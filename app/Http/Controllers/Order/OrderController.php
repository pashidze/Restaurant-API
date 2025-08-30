<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\FilterOrderRequest;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Requests\Order\UpdateOrderRequest;
use App\Http\Resources\Order\OrderResource;
use App\Models\Order;
use App\Services\Order\OrderService;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Gate;

class OrderController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/order",
     *     summary="Вывод списка заказов",
     *     tags={"Order"},
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
     *          description="Номер",
     *          in="query",
     *          name="number",
     *          required=false,
     *          @OA\Schema(type="string"),
     *          example="Заказ-0101-0001",
     *      ),
     *     @OA\Parameter(
     *          description="Стоимость",
     *          in="query",
     *          name="cost",
     *          required=false,
     *          @OA\Schema(type="float"),
     *          example=125.5,
     *      ),
     *     @OA\Parameter(
     *          description="Официант",
     *          in="query",
     *          name="waiter_id",
     *          required=false,
     *          @OA\Schema(type="integer"),
     *          example=1,
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
     *                  @OA\Property(property="number", type="string", example="Заказ-0101-0001"),
     *                  @OA\Property(property="list_of_dishes", type="array", @OA\Items(
     *                      @OA\Property(property="id", type="integer", example=1),
     *                      @OA\Property(property="name", type="string", example="Кофе"),
     *                      @OA\Property(property="price", type="float", example=125.5),
     *                      @OA\Property(property="quantity", type="integer", example=2),
     *                      @OA\Property(property="cost", type="float", example=251),
     *                  )),
     *                  @OA\Property(property="number_of_item", type="integer", example=1),
     *                  @OA\Property(property="cost", type="float", example=251),
     *                  @OA\Property(property="date_of_creation", type="date", example="2025-01-01"),
     *                  @OA\Property(property="closing_date", type="date", example="2025-01-01"),
     *                  @OA\Property(property="waiter", type="object",
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
    public function index(FilterOrderRequest $request, OrderService $orderService)
    {
        try {
            $data = $request->validated();

            $order = $orderService->filter($data);

            return OrderResource::collection($order);
        } catch (Exception $exception) {
            return response()->json(['Error' => true, 'Message' => $exception->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/order",
     *     summary="Ввод нового заказа",
     *     tags={"Order"},
     *     security={{ "sanctum":{} }},
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(
     *                      @OA\Property(property="list_of_dishes", type="array", @OA\Items(
     *                          @OA\Property(property="name", type="integer", example=1),
     *                          @OA\Property(property="quntatity", type="integer", example=1),
     *                      )),
     *                      @OA\Property(property="user_id", type="integer", example=1),
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
     *                  @OA\Property(property="number", type="string", example="Заказ-0101-0001"),
     *                  @OA\Property(property="number_of_item", type="integer", example=1),
     *                  @OA\Property(property="cost", type="float", example=251),
     *                  @OA\Property(property="date_of_creation", type="date", example="2025-01-01"),
     *                  @OA\Property(property="closing_date", type="date", example="2025-01-01"),
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
    public function store(StoreOrderRequest $request, OrderService $orderService)
    {
        try {
            $data = $request->validated();

            $order = $orderService->create($data);

            return new OrderResource($order);
        } catch (Exception $exception) {
            return response()->json(['Error' => true, 'Message' => $exception->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/order/{id}",
     *     summary="Вывод одного заказа",
     *     tags={"Order"},
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
     *                   @OA\Property(property="id", type="integer", example=1),
     *                   @OA\Property(property="number", type="string", example="Заказ-0101-0001"),
     *                   @OA\Property(property="list_of_dishes", type="array", @OA\Items(
     *                       @OA\Property(property="id", type="integer", example=1),
     *                       @OA\Property(property="name", type="string", example="Кофе"),
     *                       @OA\Property(property="price", type="float", example=125.5),
     *                       @OA\Property(property="quantity", type="integer", example=2),
     *                       @OA\Property(property="cost", type="float", example=251),
     *                   )),
     *                   @OA\Property(property="number_of_item", type="integer", example=1),
     *                   @OA\Property(property="cost", type="float", example=251),
     *                   @OA\Property(property="date_of_creation", type="date", example="2025-01-01"),
     *                   @OA\Property(property="closing_date", type="date", example="2025-01-01"),
     *                   @OA\Property(property="waiter", type="object",
     *                       @OA\Property(property="id", type="integer", example=1),
     *                       @OA\Property(property="name", type="string", example="Официант"),
     *                   ),
     *               ),
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
            $order = Order::with(['dishes', 'user'])->findOrFail($id);

            return new OrderResource($order);
        } catch (ModelNotFoundException $e) {
            return response()->json(['Error' => true, 'Message' => 'Запись не найдена!'], 404);
        } catch (Exception $exception) {
            return response()->json(['Error' => true, 'Message' => $exception->getMessage()], 500);
        }
    }

    /**
     * @OA\Patch(
     *     path="/api/order/{id}",
     *     summary="Редактирование данных о заказе",
     *     tags={"Order"},
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
     *                      @OA\Property(property="list_of_dishes", type="array", @OA\Items(
     *                           @OA\Property(property="name", type="integer", example=1),
     *                           @OA\Property(property="quntatity", type="integer", example=1),
     *                       )),
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
     *                   @OA\Property(property="number", type="string", example="Заказ-0101-0001"),
     *                   @OA\Property(property="number_of_item", type="integer", example=1),
     *                   @OA\Property(property="cost", type="float", example=251),
     *                   @OA\Property(property="date_of_creation", type="date", example="2025-01-01"),
     *                   @OA\Property(property="closing_date", type="date", example="null"),
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
    public function update(UpdateOrderRequest $request, $id, OrderService $orderService)
    {
        try {
            $order = Order::findOrFail($id);

            Gate::authorize('update', $order);

            if ($order->closing_date == null) {
                $data = $request->validated();
                $upOrder = $orderService->update($order, $data);
                return new OrderResource($upOrder);
            } else {
                return response()->json(['Error' => true, 'Message' => 'Заказ уже закрыт! Изменение запрещено!'], 500);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['Error' => true, 'Message' => 'Запись не найдена!'], 404);
        } catch (AuthorizationException $exAuth) {
            return response()->json(['Error' => true, 'Message' => $exAuth->getMessage()], 403);
        } catch (Exception $exception) {
            return response()->json(['Error' => true, 'Message' => $exception->getMessage()], 500);
        }
    }

    /**
     * @OA\Patch(
     *     path="/api/order/{id}/close",
     *     summary="Закрытие заказа",
     *     tags={"Order"},
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
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="id", type="integer", example=1),
     *                   @OA\Property(property="number", type="string", example="Заказ-0101-0001"),
     *                   @OA\Property(property="number_of_item", type="integer", example=1),
     *                   @OA\Property(property="cost", type="float", example=251),
     *                   @OA\Property(property="date_of_creation", type="date", example="2025-01-01"),
     *                   @OA\Property(property="closing_date", type="date", example="2025-01-01"),
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
    public function close($id, OrderService $orderService)
    {
        try {
            $order = Order::findOrFail($id);

            Gate::authorize('update', $order);

            if ($order->closing_date == null) {
                $upOrder = $orderService->close($order);
                return new OrderResource($upOrder);
            } else {
                return response()->json(['Error' => true, 'Message' => 'Заказ уже закрыт!'], 500);
            }
        } catch (AuthorizationException $exAuth) {
            return response()->json(['Error' => true, 'Message' => $exAuth->getMessage()], 403);
        }catch (ModelNotFoundException $e) {
            return response()->json(['Error' => true, 'Message' => 'Запись не найдена!'], 404);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/order/{id}",
     *     summary="Удаление данных о заказе",
     *     tags={"Order"},
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
    public function destroy($id, OrderService $orderService)
    {
        try {
            $order = Order::findOrFail($id);

            Gate::authorize('delete', $order);

            $orderService->delete($order);

            return response()->json(['Success' => true]);
        } catch (AuthorizationException $exAuth) {
            return response()->json(['Error' => true, 'Message' => $exAuth->getMessage()], 403);
        } catch (ModelNotFoundException $exception) {
            return response()->json(['Error' => true, 'Message' => 'Запись не найдена!'], 404);
        } catch (Exception $exception) {
            return response()->json(['Error' => true, 'Message' => $exception->getMessage()], 500);
        }
    }
}

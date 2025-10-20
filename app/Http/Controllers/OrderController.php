<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateOrderRequest;
use App\Services\OrderServiceInterface;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="OlaClick API",
 *     version="1.0.0",
 *     description="API RESTful para gestión de órdenes de restaurante",
 *     @OA\Contact(
 *         email="contact@olaclick.com"
 *     )
 * )
 * @OA\Server(
 *     url="http://localhost:8000",
 *     description="Servidor Local"
 * )
 */
class OrderController extends Controller
{
    public function __construct(
        private OrderServiceInterface $orderService
    ) {}

    /**
     * @OA\Get(
     *     path="/api/orders",
     *     tags={"Orders"},
     *     summary="Listar órdenes activas",
     *     description="Retorna todas las órdenes con status != 'delivered'",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de órdenes activas",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="client_name", type="string", example="Carlos Gómez"),
     *                     @OA\Property(property="status", type="string", example="initiated"),
     *                     @OA\Property(property="total", type="number", format="float", example=80.00)
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $orders = $this->orderService->getActiveOrders();

        return response()->json([
            'success' => true,
            'data' => $orders,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/orders",
     *     tags={"Orders"},
     *     summary="Crear nueva orden",
     *     description="Crea una orden con status 'initiated'",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"client_name", "items"},
     *             @OA\Property(property="client_name", type="string", example="Carlos Gómez"),
     *             @OA\Property(property="items", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="description", type="string", example="Lomo saltado"),
     *                     @OA\Property(property="quantity", type="integer", example=1),
     *                     @OA\Property(property="unit_price", type="number", example=60)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Orden creada exitosamente"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Errores de validación"
     *     )
     * )
     */
    public function store(CreateOrderRequest $request): JsonResponse
    {
        $order = $this->orderService->createOrder($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Orden creada con éxito',
            'data' => $order,
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/orders/{id}",
     *     tags={"Orders"},
     *     summary="Ver detalles de orden",
     *     description="Retorna información completa de una orden incluyendo items y logs",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la orden",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalles de la orden"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Orden no encontrada"
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        $order = $this->orderService->getOrderById($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Orden no encontrada',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $order,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/orders/{id}/advance",
     *     tags={"Orders"},
     *     summary="Avanzar estado de orden",
     *     description="Transición: initiated → sent → delivered (elimina)",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la orden",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Estado avanzado exitosamente"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Orden no encontrada"
     *     )
     * )
     */
    public function advance(int $id): JsonResponse
    {
        $order = $this->orderService->getOrderById($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Orden no encontrada',
            ], 404);
        }

        $updatedOrder = $this->orderService->advanceOrderStatus($id);

        if ($updatedOrder === null) {
            return response()->json([
                'success' => true,
                'message' => 'Orden finalizada y eliminada con éxito',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Estado de la orden avanzado con éxito',
            'data' => $updatedOrder,
        ]);
    }
}
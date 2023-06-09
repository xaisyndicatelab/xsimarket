<?php


namespace Xsimarket\Database\Repositories;

use Exception;
use Xsimarket\Database\Models\Address;
use Xsimarket\Database\Models\Order;
use Xsimarket\Database\Models\Refund;
use Xsimarket\Enums\Permission;
use Xsimarket\Exceptions\XsimarketException;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;

class RefundRepository extends BaseRepository
{
    protected $dataArray = [
        'order_id',
        'images',
        'title',
        'description'
    ];
    /**
     * Configure the Model
     **/
    public function model()
    {
        return Refund::class;
    }

    public function boot()
    {
        try {
            $this->pushCriteria(app(RequestCriteria::class));
        } catch (RepositoryException $e) {
        }
    }

    public function storeRefund($request)
    {
        $user = $request->user();
        $refunds = $this->where('order_id', $request->order_id)->get();
        if (count($refunds)) {
            throw new XsimarketException(ORDER_ALREADY_HAS_REFUND_REQUEST);
        }
        try {
            $order = Order::findOrFail($request->order_id);
            if ($order->parent !== null) {
                throw new XsimarketException(REFUND_ONLY_ALLOWED_FOR_MAIN_ORDER);
            }
        } catch (Exception $th) {
            throw new XsimarketException(NOT_FOUND);
        }
        if ($user->id !== $order->customer_id || $user->hasPermissionTo(Permission::SUPER_ADMIN)) {
            throw new XsimarketException(NOT_AUTHORIZED);
        }
        $data = $request->only($this->dataArray);
        $data['customer_id'] = $order->customer_id;
        $data['amount'] = $order->amount;
        $refund = $this->create($data);
        $this->createChildOrderRefund($order->children, $data);
        return $this->find($refund->id);
    }

    public function createChildOrderRefund($orders, $data)
    {
        try {
            foreach ($orders as  $order) {
                $data['order_id'] = $order->id;
                $data['customer_id'] = $order->customer_id;
                $data['shop_id'] = $order->shop_id;
                $data['amount'] = $order->amount;
                $this->create($data);
            }
        } catch (Exception $th) {
            throw new XsimarketException(SOMETHING_WENT_WRONG);
        }
    }

    public function updateRefund($request, $refund)
    {
        if ($refund->shop_id !==  null) {
            throw new XsimarketException(WRONG_REFUND);
        }
        $data = $request->only(['status']);
        $refund->update($data);
        $this->changeShopSpecificRefundStatus($refund->order_id, $data);
        return $refund;
    }

    private function changeShopSpecificRefundStatus($order_id, $data)
    {
        $order = Order::with('children')->findOrFail($order_id);

        $childOrderIds = array_map(function ($childOrder) {
            return $childOrder['id'];
        }, $order->children->toArray());

        $this->whereIn('order_id',  $childOrderIds)->update($data);
    }
}

<?php

namespace App\Http\Controllers\Api\Admin;

use App\Exceptions\ModelGetEmptyException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubscriptionRequest;
use App\Http\Requests\UpdateSubscriptionRequest;
use App\Http\Resources\SubscriptionResource;
use App\Models\Subscription;
use App\Services\PaymentSubscriptionService;

class PaymentController extends Controller
{
    protected $subscriptionService;

    public function __construct(PaymentSubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subscriptions = SubscriptionResource::collection($this->subscriptionService->getAll());

        if ($subscriptions->count() == 0) {
            // throw new ModelGetEmptyException("Payment Subscription");
        }

        return response()->json([
            'status' => 200,
            'message' => 'Subscriptions Fetched Successfully',
            'data' => $subscriptions->response()->getData(true)
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSubscriptionRequest $request)
    {
        $validated = $request->validated();

        $subscription = SubscriptionResource::make($this->subscriptionService->create($validated)->load('invoices'));

        return response()->json([
            'status' => 200,
            'message' => 'Subscription Created Successfully',
            'data' => $subscription
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Subscription $subscription)
    {
        $subscription = SubscriptionResource::make($this->subscriptionService->getOne($subscription->id)->load(['invoices', 'courseStudents', 'courseWork', 'student']));

        return response()->json([
            'status' => 200,
            'message' => 'Subscription Fetched Successfully',
            'data' => $subscription
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSubscriptionRequest $request, Subscription $subscription)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subscription $subscription)
    {
        //
    }
}

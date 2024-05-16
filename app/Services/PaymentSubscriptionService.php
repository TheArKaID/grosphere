<?php

namespace App\Services;

use App\Models\Subscription;
use Illuminate\Support\Facades\DB;

class PaymentSubscriptionService
{
    private $subscription;

    public function __construct(Subscription $subscription)
    {
        $this->subscription = $subscription;
    }

    /**
     * Get all Subscriptions
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        if ($search = request()->get('search')) {
            $this->subscription = $this->subscription->withWhereHas('student.user', function ($query) use ($search) {
                $query->where('name', 'like', "%$search%");
            });
        }
        if (request()->has('page') && request()->get('page') == 'all') {
            return $this->subscription->get();
        }
        return $this->subscription->paginate(request('size', 10));
    }

    /**
     * Get One Subscription
     * 
     * @param int $id
     * 
     * @return Subscription
     */
    public function getOne($id)
    {
        return $this->subscription->findOrFail($id);
    }

    /**
     * Create Subscription
     * 
     * @param array $data
     * 
     * @return \App\Models\Subscription
     */
    public function create($data)
    {
        DB::beginTransaction();

        $subscription = $this->subscription
        ->firstOrCreate([
            'student_id' => $data['student_id'],
            'course_work_id' => $data['course_work_id'],
        ], $data);

        $subscription->status = 'unpaid';
        $subscription->save();

        $subscription->invoices()->create([
            'invoice_number' => $data['invoice_number'],
            'price' => $data['price'],
            'currency' => $data['currency'],
            'active_days' => $data['active_days'],
            'total_meeting' => $data['total_meeting'],
            'due_date' => $data['due_date'],
            'expired_date' => $data['expired_date'],
        ]);

        DB::commit();
        return $subscription;
    }
}

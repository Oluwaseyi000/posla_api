<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Project;
use App\Models\DealType;
use App\Models\Proposal;
use Illuminate\Support\Str;
use App\Helpers\CartHandler;
use Illuminate\Http\Request;
use App\Helpers\OrderHandler;
use App\Models\OrderTransaction;
use App\Http\Requests\CartRequest;
use Illuminate\Support\Facades\Http;

class CartController extends Controller
{
    protected $cart_type = '';
    protected $amount_expected = null;
    protected $amount_paid = null;
    protected $freelancer_id = null;
    protected $type_data = null;

    const TYPE_DEAL = 'deal';
    const TYPE_PROJECT = 'project';
    const PAYMENT_APPROVED = 'approved';
    const PAYMENT_UNDER_PAID = 'under_paid';
    const PAYMENT_OVER_PAID = 'over_paid';

    public function paymentPaystack(CartRequest $request){
        $orderHandler = new CartHandler('paystack',  $request->toArray());
        $order = $orderHandler->processPayment();
        return $this->successResponse($order);
    }

    private function type(array $request){
        if(array_key_exists('deal_type_id', $request)){
            $this->cart_type = $this::TYPE_DEAL;
            $dealType = DealType::find($request['deal_type_id']);
            $this->type_data = json_encode($dealType->with('deal')->first());
            $this->freelancer_id = $dealType->deal->user_id;
        }
        elseif(array_key_exists('proposal_id', $request)){
            $this->cart_type = $this::TYPE_PROJECT;
            $proposal = Proposal::find($request['proposal_id']);
            $this->freelancer_id = $proposal->user_id;
            $this->type_data = json_encode($proposal->with('project')->first());
        }else{
            throw 'something went wrong with cart type of deal or project';
        }
    }

    private function verifyAmount($paidAmount, $request){
        if($this->cart_type == $this::TYPE_DEAL){
            $dealType = DealType::find($request['deal_type_id']);
            $this->amount_expected = $dealType->total;

        }elseif($this->cart_type == $this::TYPE_PROJECT){
            $proposal = Proposal::find($request['proposal_id']);
            $this->amount_expected = $proposal->deposit;
        }

        if($this->amount_expected > $paidAmount){
            $status = $this::PAYMENT_UNDER_PAID;
        }
        elseif($this->amount_expected < $paidAmount){
            $status = $this::PAYMENT_OVER_PAID;
        }else{
            $status = $this::PAYMENT_APPROVED;
        }
        return $status;
    }

    private function createOrder(){
        $data = [
            'owner_id' => auth()->user()->id,
            'freelancer_id' => $this->freelancer_id,
            'type' => $this->cart_type,
            'type_data' => $this->type_data,
            'reference_number' => Str::random(10),
            'price' =>  $this->amount_expected,
            'service_charge' => 0,
            'delivery_time' => now()->addDays(5),
            'revision_remaining' => 3,
            'quantity' => 1,
            'total_price' => $this->amount_expected,
            'total_paid' => $this->amount_paid,
            'status' => Order::INPROGRESS
        ];

       return $order = Order::create($data);

    }

    private function createTransaction(array $transaction){
        $generalData = [
            'owner_id' => auth()->user()->id,
            'freelancer_id' => $this->freelancer_id,
            'reference' => Str::random(10),
            'amount_expected' => $this->amount_expected,
        ];

        // $order = Order::create(array_merge($generalData, $transaction));
        OrderTransaction::create(array_merge($generalData, $transaction));
    }

    private function provideService(){
        $generalData = [
            'owner_id' => auth()->user()->id,
            'freelancer_id' => $this->freelancer_id,
            'reference_number' => Str::random(10),
            'type' => $this->cart_type,
            'type_data' => $this->type_data,
            'price' => 0,
            'service_charge' => 0,
            'delivery_time' => 0,
            'quantity' => 1,
            'total_price' => 0,
            'total_paid' => 0,
            // 'project_starts_on' => 0,
            // 'project_estimated_completion' => null,
            // 'project_ends_on' => 0,
            'status' => 0,
            // 'type_id' => $this->cart_type,
        ];
    }
}

<?php

namespace App\Helpers;

use App\Models\Order;
use App\Models\DealType;
use App\Models\Proposal;
use Illuminate\Support\Str;
use App\Models\OrderTransaction;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class CartHandler extends Controller
{
    const TYPE_DEAL = 'deal';
    const TYPE_PROJECT = 'project';
    const PAYMENT_APPROVED = 'approved';
    const PAYMENT_UNDER_PAID = 'under_paid';
    const PAYMENT_OVER_PAID = 'over_paid';

    protected $paymentChannel;
    protected $request;
    protected $type_data;
    protected $cart_type;
    protected $amount_expected;
    protected $amount_paid = null;
    protected $amount_status = null;
    protected $freelancer_id = null;


    public function __construct($paymentChannel, array $request)
    {
        $this->paymentChannel = $paymentChannel;
        $this->request = $request;
    }

    public function processPayment()
    {

        if($this->paymentChannel ==  'paystack'){
            $data = $this->processPaystack();
        }



        $this->seTtype();
        $this->verifyAmount($data['amount']);
        $orderData = [
            'owner_id' => auth()->user()->id,
            'freelancer_id' => $this->freelancer_id,
            'type' => $this->cart_type,
            'type_data' => $this->type_data,
            'reference_number' => Str::random(10),
            'price' =>  $this->amount_expected,
            'service_charge' => 0,
            'delivery_time' => now()->addDays($data['metadata']['deal_type']['delivery_timeframe']),
            'revision_remaining' => $data['metadata']['deal_type']['revision_num'],
            'quantity' => 1,
            'total_price' => $this->amount_expected,
            'total_paid' => $this->amount_paid,
            'status' => Order::INPROGRESS,
            'project_estimated_completion' => now()->addDays(25)

        ];

       $order =  Order::create($orderData);

        $this->createTransaction(array_merge($data, ['order_id' => $order->id]));
        return $order;

    }

    private function processPaystack(){
        $paystack_verify_url = env('PAYSTACK_PAYMENT_URL').'/transaction/verify/'.$this->request['transaction_reference'];
        $response = Http::withToken(env('PAYSTACK_SECRET_KEY'))->get($paystack_verify_url);
        $data = $response->json();
        if($response['data']['status'] != 'success'){
            return $this->errorResponse('Payment verification failed');
        }
        $this->amount_paid = $data['data']['amount'];
        return $data['data'];
    }

    private function setType(){
        if(array_key_exists('deal_type_id', $this->request)){
            $this->cart_type = $this::TYPE_DEAL;
            $dealType = DealType::find($this->request['deal_type_id']);
            $this->type_data = json_encode(['type' => 'deal', 'data' => $dealType->with('deal')->first(), 'requirements' => $dealType->deal->requirements], );
            $this->freelancer_id = $dealType->deal->user_id;
        }
        elseif(array_key_exists('proposal_id', $this->request)){
            $this->cart_type = $this::TYPE_PROJECT;
            $proposal = Proposal::find($this->request['proposal_id']);
            $this->freelancer_id = $proposal->user_id;
            $this->type_data = json_encode(['type' => 'project', 'data' => $proposal->with('project')->first()]);
        }else{
            throw 'something went wrong with cart type of deal or project';
        }
    }

    private function verifyAmount($paidAmount){
        if($this->cart_type == $this::TYPE_DEAL){
            $dealType = DealType::find($this->request['deal_type_id']);
            $this->amount_expected = $dealType->total;

        }elseif($this->cart_type == $this::TYPE_PROJECT){
            $proposal = Proposal::find($this->request['proposal_id']);
            $this->amount_expected = $proposal->deposit;
        }

        if($this->amount_expected > $paidAmount){
            $this->amount_status = $this::PAYMENT_UNDER_PAID;
        }
        elseif($this->amount_expected < $paidAmount){
            $this->amount_status = $this::PAYMENT_OVER_PAID;
        }else{
            $this->amount_status = $this::PAYMENT_APPROVED;
        }

    }

    // private function createOrder(){
    //     $data = [
    //         'owner_id' => auth()->user()->id,
    //         'freelancer_id' => $this->freelancer_id,
    //         'type' => $this->cart_type,
    //         'type_data' => $this->type_data,
    //         'reference_number' => Str::random(10),
    //         'price' =>  $this->amount_expected,
    //         'service_charge' => 0,
    //         'delivery_time' => now()->addDays(5),
    //         'revision_remaining' => 3,
    //         'quantity' => 1,
    //         'total_price' => $this->amount_expected,
    //         'total_paid' => $this->amount_paid,
    //         'status' => Order::INPROGRESS
    //     ];

    //    return Order::create($data);

    // }

    private function createTransaction(array $data){
        $data = [
            'ext_reference' => $data['reference'],
            'transaction_channel' => 'paystack',
            'transaction_metadata' => json_encode($data),
            'amount_paid' => $data['amount'],
            'currency' => $data['currency'],
            'status' =>  $this->amount_status,
            'owner_id' => auth()->user()->id,
            'freelancer_id' => $this->freelancer_id,
            'reference' => Str::random(10),
            'amount_expected' => $this->amount_expected,
            'order_id' => $data['order_id'],
            'payment_type' => 'order_payment'
        ];

        OrderTransaction::create(array_merge($data));
    }
}

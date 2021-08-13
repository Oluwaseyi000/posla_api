<?php

namespace App\Http\Controllers;

use App\Models\DealType;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\OrderTransaction;
use App\Http\Requests\CartRequest;
use App\Models\Project;
use App\Models\Proposal;
use Illuminate\Support\Facades\Http;

class CartController extends Controller
{
    protected $cart_type = '';
    protected $amount_expected = '';
    protected $freelancer_id = null;
    protected $type_data = null;

    const TYPE_DEAL = 'deal'; 
    const TYPE_PROJECT = 'project'; 
    const PAYMENT_APPROVED = 'approved'; 
    const PAYMENT_UNDER_PAID = 'under_paid'; 
    const PAYMENT_OVER_PAID = 'over_paid'; 

    public function paymentPaystack(CartRequest $request){
        try {
            $paystack_verify_url = env('PAYSTACK_PAYMENT_URL').'/transaction/verify/'.$request->paystack_transaction_reference;
            $response = Http::withToken(env('PAYSTACK_SECRET_KEY'))->get($paystack_verify_url);
            $data = $response->json();
            
            if($response['data']['status'] != 'success'){
                return $this->errorResponse('Payment verification failed');
            }

            $data = $data['data'];

            $this->type($request->all());
            $amountStatus = $this->verifyAmount($data['amount'], $request->all());
            
            $transaction = [
                'ext_reference' => $data['reference'],
                'transaction_channel' => 'paystack',
                'transaction_metadata' => json_encode($data),
                'amount_paid' => $data['amount'],
                'currency' => $data['currency'],
                'status' =>  $amountStatus,
            ];

            $this->createTransaction($transaction);
            $this->provideService();

        } catch (\Throwable $th) {
            throw $th;
        }
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

    private function createTransaction(array $transaction){
        $generalData = [
            'owner_id' => auth()->user()->id,
            'freelancer_id' => $this->freelancer_id,
            'reference' => Str::random(10),
            'amount_expected' => $this->amount_expected,
        ];

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
            'quantity' => 0,
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

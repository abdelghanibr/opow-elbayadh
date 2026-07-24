<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChargilyPayController extends Controller
{
    /**
     * The client will be redirected to the ChargilyPay payment page
     *
     */
  public function redirect(Request $request)
{
    $user = auth()->user();

    $amount = (float) $request->amount;
    if ($amount <= 0) {
        abort(400, 'Montant invalide');
    }

    $payment = \App\Models\ChargilyPayment::create([
        'user_id'  => $user->id,
        'status'   => 'pending',
        'currency' => 'dzd',
        'amount'   => $amount,
    ]);

    if ($payment) {
            $checkout = $this->chargilyPayInstance()->checkouts()->create([
                "metadata" => [
                    "payment_id" => $payment->id,
                ],
                "locale" => "ar",
                "amount" => $payment->amount,
                "currency" => $payment->currency,
                "description" => "Payment ID={$payment->id}",
                "success_url" => route("chargilypay.back"),
                "failure_url" => route("chargilypay.back"),
                "webhook_endpoint" => route("chargilypay.webhook_endpoint"),
            ]);
            if ($checkout) {
                return redirect($checkout->getUrl());
            }
        }
    return redirect($checkout->getUrl());
}


    /**
     * Your client you will redirected to this link after payment is completed ,failed or canceled
     *
     */
    public function back(Request $request)
    {
        $user = auth()->user();
        $checkout_id = $request->input("checkout_id");
        $checkout = $this->chargilyPayInstance()->checkouts()->get($checkout_id);
        $payment = null;

        if ($checkout) {
            $metadata = $checkout->getMetadata();
            $payment = \App\Models\ChargilyPayment::find($metadata['payment_id']);
            ////
            //// Is not recomended to process payment in back page / success or fail page
            //// Doing payment processing in webhook for best practices
            ////
        }
       
    }
    /**
     * This action will be processed in the background
     */
    public function webhook()
    {
        $webhook = $this->chargilyPayInstance()->webhook()->get();
        if ($webhook) {
            //
            $checkout = $webhook->getData();
            //check webhook data is set
            //check webhook data is a checkout
            if ($checkout and $checkout instanceof \Chargily\ChargilyPay\Elements\CheckoutElement) {
                if ($checkout) {
                    $metadata = $checkout->getMetadata();
                    $payment = \App\Models\ChargilyPayment::find($metadata['payment_id']);
                  
                  dd($checkout->getStatus());
                    if ($payment) {
                        if ($checkout->getStatus() === "paid") {
                            //update payment status in database
                            $payment->status = "paid";
                            $payment->update();
                            /////
                            ///// Confirm your order
                            /////
                            return response()->json(["status" => true, "message" => "Payment has been completed"]);
                        } else if ($checkout->getStatus() === "failed" or $checkout->getStatus() === "canceled") {
                            //update payment status in database
                            $payment->status = "failed";
                            $payment->update();
                            /////
                            /////  Cancel your order
                            /////
                            return response()->json(["status" => true, "message" => "Payment has been canceled"]);
                        }
                    }
                }
            }
        }
        return response()->json([
            "status" => false,
            "message" => "Invalid Webhook request",
        ], 403);
    }

    /**
     * Just a shortcut
     */
    protected function chargilyPayInstance()
    {
        return new \Chargily\ChargilyPay\ChargilyPay(new \Chargily\ChargilyPay\Auth\Credentials([
            "mode" => "test",
            "public" => "test_pk_9aS3yB0wm2byNnuzMVJwu2jmiyWEoQhNswnqcrh0",
            "secret" => "test_sk_U70t8DOOg4uk6vq7ctk2XiHJE15RwscOgOJLXrCn",
        ]));
    }


    


}

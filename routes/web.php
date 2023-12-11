<?php

use Illuminate\Support\Facades\Route;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
use App\Http\Controllers\PayPalController;

Route::get('create-transaction', [PayPalController::class, 'createTransaction'])->name('createTransaction');
Route::get('process-transaction', [PayPalController::class, 'processTransaction'])->name('processTransaction');
Route::get('success-transaction', [PayPalController::class, 'successTransaction'])->name('successTransaction');
Route::get('cancel-transaction', [PayPalController::class, 'cancelTransaction'])->name('cancelTransaction');
Route::get('/success', function () {
    return view("");
});
Route::any('/', function () {
    $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $paypalToken = $provider->getAccessToken();
    $data =[
        "intent" => "CAPTURE",
        // "application_context" => [
        //     "return_url" => route('successTransaction'),
        //     "cancel_url" => route('cancelTransaction'),
        // ],
        "purchase_units" => [
            0 => [
                "amount" => [
                    "currency_code" => "USD",
                    "value" => "1.00"
                ]
            ]
        ]
                ];
        $response = $provider->createOrder($data);
        // dd($response['id']);
        if (isset($response['id']) && $response['id'] != null) {
            // redirect to approve href
            foreach ($response['links'] as $links) {
                if ($links['rel'] == 'approve') {
                    return redirect()->away($links['href']);
                }
            }
            $response = $provider->capturePaymentOrder($response['id']);
            dd($response);
            // return redirect()
            //     ->route('createTransaction')
            //     ->with('error', 'Something went wrong.');
        } else {

            // return redirect()
            //     ->route('createTransaction')
            //     ->with('error', $response['message'] ?? 'Something went wrong.');
        }
        // $order = $provider->authorizePaymentOrder($response['id']);
        // $order = $provider->showOrderDetails($response['id']);
        // dd($order);
    // $config = [
    //     'mode' => env('PAYPAL_MODE'),
    //     'sandbox' => [
    //         'client_id' => env('PAYPAL_SANDBOX_CLIENT_ID'),
    //         'client_secret' => env('PAYPAL_SANDBOX_CLIENT_SECRET'),
    //         'app_id' => 'APP-80W284485P519543T',
    //     ],
    //     'payment_action' => 'Sale',
    //     'currency' => 'USD',
    //     'notify_url' => 'http://127.0.0.1:8000/success',
    //     'locale' => 'en_US',
    //     'validate_ssl' => true,
    // ];

    // $provider = \PayPal::setProvider();
    // $provider->setApiCredentials($config);

    // $data = json_decode('{
    //     "intent": "CAPTURE",

    //     "purchase_units": [
    //       {
    //         "amount": {
    //           "currency_code": "USD",
    //           "value": "100.00"
    //         }
    //       }
    //     ]
    // }', true);
    // $data = [
    //     "intent" => "CAPTURE",
    //     "application_context" => [
    //         "return_url" => "success",
    //         "cancel_url" => "cancel",
    //     ],
    //     "purchase_units" => [
    //         [
    //             "amount" => [
    //                 "currency_code" => "USD",
    //                 "value" => "100",
    //             ]
    //         ]
    //     ]
    // ];





});

<?php

use Hanafalah\ApiHelper\Facades\ApiAccess;
use Hanafalah\LaravelSupport\Facades\LaravelSupport;
use Hanafalah\MicroTenant\Facades\MicroTenant;
use Illuminate\Support\Facades\Route;
use Projects\WellmedGateway\Controllers\API\Import\ImportController;
use Projects\WellmedGateway\Controllers\API\PatientEmr\Patient\PatientController;
use Projects\WellmedGateway\Controllers\API\Tenant\AddTenantController;
use Projects\WellmedGateway\Controllers\API\Xendit\XenditController;

ApiAccess::secure(function(){
    Route::group([
        'as' => 'api.',
        'prefix' => 'api/'
    ],function(){
        LaravelSupport::callRoutes(__DIR__.'/api');
        Route::get('/wellmed-view',function(){
            $workspace = tenancy()->tenant->reference;
            return view('wellmed::exports.billing',['workspace'=>$workspace]);
        });
        Route::get('/wellmed-pdf',function(){
            MicroTenant::tenantImpersonate(4);
            $workspace = tenancy()->tenant->reference;
            $workspace = $workspace->load($workspace->showUsingRelation());
            $workspace = $workspace->toShowApi()->resolve();
            $workspace = json_decode(json_encode($workspace));
            $transaction = app(config('database.models.PosTransaction'));
            $transaction = $transaction->with($transaction->showUsingRelation())->find('01kdbnfdzw35bbhb4hexy87622');
            $transaction = $transaction->toShowApi()->resolve();
            $transaction = json_decode(json_encode($transaction));
            $transaction->created_at = \Carbon\Carbon::parse($transaction->created_at)->format('d/m/Y');
            $billing = &$transaction->billing;
            if (isset($billing)){
                $invoices = &$billing->invoices;
                foreach ($invoices as &$invoice){
                    $payment_history = &$invoice->payment_history;
                    if (isset($payment_history->form)){
                        $payment_summaries = &$payment_history->form->payment_summaries;
                        $payment_summary_model = app(config('database.models.PaymentSummary'));
                        foreach ($payment_summaries as &$payment_summary){
                            $payment_summary_model = $payment_summary_model->with([
                                'paymentDetails' => function($query) use ($payment_summary){
                                    $query->with('transactionItem')->whereIn('id',array_column($payment_summary->payment_details,'id'));
                                }
                            ])->findOrFail($payment_summary->id);
                            $payment_summary = $payment_summary_model->toShowApi()->resolve();
                            $payment_summary = json_decode(json_encode($payment_summary));
                        }
                    }
                }
            }
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('wellmed::exports.billing', ['workspace'=>$workspace,'transaction'=>$transaction]);
            // return $pdf->download('wellmed-billing.pdf');
            return $pdf->stream('wellmed-billing.pdf');
            // $workspace = tenancy()->tenant->reference;
            // $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('wellmed::exports.billing', ['workspace'=>$workspace]);
            // return $pdf->download('wellmed-billing.pdf');
        });
    });
});
Route::get('/wellmed-view',function(){
    MicroTenant::tenantImpersonate(4);
    $workspace = tenancy()->tenant->reference;
    $workspace = $workspace->load($workspace->showUsingRelation());
    $workspace = $workspace->toShowApi()->resolve();
    $workspace = json_decode(json_encode($workspace));
    $transaction = app(config('database.models.PosTransaction'));
    $transaction = $transaction->with($transaction->showUsingRelation())->find('01kdbnfdzw35bbhb4hexy87622');
    $transaction = $transaction->toShowApi()->resolve();
    $transaction = json_decode(json_encode($transaction));
    $transaction->created_at = \Carbon\Carbon::parse($transaction->created_at)->format('d/m/Y');
    $billing = &$transaction->billing;
    if (isset($billing)){
        $invoices = &$billing->invoices;
        foreach ($invoices as &$invoice){
            $payment_history = &$invoice->payment_history;
            if (isset($payment_history->form)){
                $payment_summaries = &$payment_history->form->payment_summaries;
                $payment_summary_model = app(config('database.models.PaymentSummary'));
                foreach ($payment_summaries as &$payment_summary){
                    $payment_summary_model = $payment_summary_model->with([
                        'paymentDetails' => function($query) use ($payment_summary){
                            $query->with('transactionItem')->whereIn('id',array_column($payment_summary->payment_details,'id'));
                        }
                    ])->findOrFail($payment_summary->id);
                    $payment_summary = $payment_summary_model->toShowApi()->resolve();
                    $payment_summary = json_decode(json_encode($payment_summary));
                }
            }
        }
    }
    return view('wellmed::exports.billing',['workspace'=>$workspace,'transaction'=>$transaction]);
});
Route::get('/wellmed-pdf',function(){
    MicroTenant::tenantImpersonate(4);
    $workspace = tenancy()->tenant->reference;
    $workspace = $workspace->load($workspace->showUsingRelation());
    $workspace = $workspace->toShowApi()->resolve();
    $workspace = json_decode(json_encode($workspace));
    $transaction = app(config('database.models.PosTransaction'));
    $transaction = $transaction->with($transaction->showUsingRelation())->find('01kdbnfdzw35bbhb4hexy87622');
    $transaction = $transaction->toShowApi()->resolve();
    $transaction = json_decode(json_encode($transaction));
    $transaction->created_at = \Carbon\Carbon::parse($transaction->created_at)->format('d/m/Y');
    $billing = &$transaction->billing;
    if (isset($billing)){
        $invoices = &$billing->invoices;
        foreach ($invoices as &$invoice){
            $payment_history = &$invoice->payment_history;
            if (isset($payment_history->form)){
                $payment_summaries = &$payment_history->form->payment_summaries;
                $payment_summary_model = app(config('database.models.PaymentSummary'));
                foreach ($payment_summaries as &$payment_summary){
                    $payment_summary_model = $payment_summary_model->with([
                        'paymentDetails' => function($query) use ($payment_summary){
                            $query->with('transactionItem')->whereIn('id',array_column($payment_summary->payment_details,'id'));
                        }
                    ])->findOrFail($payment_summary->id);
                    $payment_summary = $payment_summary_model->toShowApi()->resolve();
                    $payment_summary = json_decode(json_encode($payment_summary));
                }
            }
        }
    }
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('wellmed::exports.billing', ['workspace'=>$workspace,'transaction'=>$transaction]);
    // return $pdf->download('wellmed-billing.pdf');
    return $pdf->stream('wellmed-billing.pdf');
        // return $pdf->download('wellm
    // return view('wellmed::exports.billing',['workspace'=>$workspace,'transaction'=>$transaction]);
});
Route::post('api/patient/import/process',[PatientController::class,'import'])->name('import');
Route::post('api/add-tenant',[AddTenantController::class,'store'])->name('add-tenant.store');
Route::post('api/import/{type}',[ImportController::class,'store'])->name('import.store');
Route::post('api/xendit/paid',[XenditController::class,'store'])->name('api.xendit.paid');

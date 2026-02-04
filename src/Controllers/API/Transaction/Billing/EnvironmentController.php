<?php

namespace Projects\WellmedGateway\Controllers\API\Transaction\Billing;

use Hanafalah\ModulePayment\Contracts\Schemas\Billing;
use Projects\WellmedGateway\Controllers\API\ApiController;

class EnvironmentController extends ApiController{
    public function __construct(
        public Billing $__schema,
    ){
        parent::__construct();
    }

    protected function commonConditional($query){

    }

    protected function commonRequest(){
        $this->userAttempt();
    }

    protected function getBillingPaginate(?callable $callback = null){        
        $this->commonRequest();
        return $this->__schema->conditionals(function($query) use ($callback){
            $this->commonConditional($query);
            $query->when(isset($callback),function ($query) use ($callback){
                $callback($query);
            });
        })->viewBillingPaginate();
    }

    protected function getBillingList(?callable $callback = null){        
        $this->commonRequest();
        return $this->__schema->conditionals(function($query) use ($callback){
            $this->commonConditional($query);
            $query->when(isset($callback),function ($query) use ($callback){
                $callback($query);
            });
        })->viewBillingList();
    }

    protected function showBilling(?callable $callback = null){        
        $this->commonRequest();
        return $this->__schema->conditionals(function($query) use ($callback){
            $this->commonConditional($query);
            $query->when(isset($callback),function ($query) use ($callback){
                $callback($query);
            });
        })->showBilling();
    }

    protected function deleteBilling(?callable $callback = null){        
        $this->commonRequest();
        return $this->__schema->conditionals(function($query) use ($callback){
            $this->commonConditional($query);
            $callback($query);
        })->deleteBilling();
    }

    protected function storeBilling(?callable $callback = null){
        $this->commonRequest();
        return $this->__schema->conditionals(function($query) use ($callback){
            $this->commonConditional($query);
            $callback($query);
        })->storeBilling();
    }

    public function kwitansi(){
        $workspace = tenancy()->tenant->reference;
        $workspace = $workspace->load($workspace->showUsingRelation());
        $workspace = $workspace->toShowApi()->resolve();
        $workspace = json_decode(json_encode($workspace));
        if (isset(request()->billing_model)){
            $billing = request()->billing_model;
            $billing->load(['invoices' => function($query){
                $query->with(['splitPayments',"paymentHistory.childs.paymentHistoryDetails"]);
            }]);
            $transaction = $this->PosTransactionModel()->with(['consument','reference'])->findOrFail($billing->has_transaction_id);
            $transaction->setRelation('billing',$billing);
            $transaction = $transaction->toShowApi()->resolve();
            $transaction = json_decode(json_encode($transaction));
            $transaction->created_at = \Carbon\Carbon::parse($transaction->created_at)->format('d/m/Y');
            $view = 'wellmed::exports.billing-paid';
        }else{
            $transaction = app(config('database.models.PosTransaction'));
            $transaction = $transaction->with($transaction->showUsingRelation())->find(request()->transaction_id);
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
                    foreach ($invoice->split_payments as &$split_payment) {
                        $split_payment->created_at = \Carbon\Carbon::parse($split_payment->created_at)->format('d/m/Y');
                    }
                }
            }
            $view = 'wellmed::exports.billing';
        }
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            $view,
            [
                'workspace'   => $workspace,
                'transaction' => $transaction
            ]
        )->setOptions([
            'enable_php'    => true,
            'enable_remote'=> true,
        ]);
        $dompdf = $pdf->getDomPDF();
        $pdf->render();
        $canvas = $dompdf->getCanvas();

        $font = $dompdf->getFontMetrics()->get_font('Helvetica', 'normal');

        $canvas->page_text(
            // 260,
            40,
            820,
            "Halaman {PAGE_NUM} dari {PAGE_COUNT} | Dicetak pada ".date('d/m/Y H:i'),
            $font,
            9,
            [0, 0, 0]
        );

        return $pdf->stream();
    }
}
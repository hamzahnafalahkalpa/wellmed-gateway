<?php

namespace Projects\WellmedGateway\Controllers\API\ItemManagement\OpnameStock;

use Hanafalah\ModuleOpnameStock\Contracts\Schemas\OpnameStock;
use Projects\WellmedGateway\Controllers\API\ApiController;

class EnvironmentController extends ApiController
{
    public function __construct(
        protected OpnameStock $__schema    
    ){
        parent::__construct();
    }

    protected function commonConditional($query){

    }

    protected function commonRequest(){
        
    }

    protected function getOpnameStockPaginate(?callable $callback = null){        
      $this->commonRequest();
      return $this->__schema->conditionals(function($query) use ($callback){
          $this->commonConditional($query);
          $query->when(isset($callback),function ($query) use ($callback){
              $callback($query);
          });
      })->viewOpnameStockPaginate();
    }

    protected function showOpnameStock(?callable $callback = null){        
        $this->commonRequest();
        return $this->__schema->conditionals(function($query) use ($callback){
            $this->commonConditional($query);
            $query->when(isset($callback),function ($query) use ($callback){
                $callback($query);
            });
        })->showOpnameStock();
    }

    protected function deleteOpnameStock(?callable $callback = null){        
        $this->commonRequest();
        return $this->__schema->conditionals(function($query) use ($callback){
            $this->commonConditional($query);
            $callback($query);
        })->deleteOpnameStock();
    }

    protected function storeOpnameStock(?callable $callback = null){
        $this->userAttempt();   
        $this->commonRequest();
        $warehouse = config('module-opname-stock.warehouse', 'Room');
        $author    = config('module-opname-stock.author', 'Employee');
        request()->merge([
            'warehouse_id'   => request()->warehouse_id ?? $this->global_employee->room->getKey(),
            'warehouse_type' => request()->warehouse_type ?? $warehouse,
            'author_type'    => request()->author_type ?? $author,
            'author_id'      => $this->global_employee->getKey()
        ]);
        return $this->__schema->conditionals(function($query) use ($callback){
            $this->commonConditional($query);
            $callback($query);
        })->storeOpnameStock();
    }
}

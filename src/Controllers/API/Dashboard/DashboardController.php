<?php

namespace Projects\WellmedGateway\Controllers\API\Dashboard;

use Projects\WellmedGateway\Controllers\API\ApiController;
use Projects\WellmedGateway\Contracts\Schemas\Dashboard as DashboardSchema;
use Projects\WellmedGateway\Requests\API\Dashboard\IndexRequest;

class DashboardController extends ApiController
{
    public function __construct(
        protected DashboardSchema $__dashboard_schema
    ) {
        parent::__construct();
    }

    /**
     * Get dashboard metrics from Elasticsearch or dummy data
     *
     * Supports filtering by search_type: daily, monthly, yearly
     * Toggle dummy data with DASHBOARD_USE_DUMMY_DATA in .env
     *
     * @param IndexRequest $request
     * @return array
     */
    public function index(IndexRequest $request)
    {
        return $this->__dashboard_schema->getDashboardMetrics(
            $request->validated()
        );
    }
}

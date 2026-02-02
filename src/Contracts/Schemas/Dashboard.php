<?php

namespace Projects\WellmedGateway\Contracts\Schemas;

interface Dashboard
{
    /**
     * Get dashboard metrics from Elasticsearch
     *
     * @param array $params
     * @return array
     */
    public function getDashboardMetrics(array $params);
}

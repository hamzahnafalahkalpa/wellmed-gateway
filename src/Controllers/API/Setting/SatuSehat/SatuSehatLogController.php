<?php

namespace Projects\WellmedGateway\Controllers\API\Setting\SatuSehat;

use Projects\WellmedGateway\Requests\API\Setting\SatuSehat\SatuSehatLog\{
    DeleteRequest, ViewRequest, StoreRequest
};
use Illuminate\Http\Request;

class SatuSehatLogController extends EnvironmentController{
    protected function commonConditional($query){
        $query->where('method','POST');
    }

    public function index(ViewRequest $request){
        return $this->getSatuSehatLogPaginate();
    }

    public function store(StoreRequest $request){
        $raw_payload = request()->api_resource['raw_payload'];
        $reference_id = request()->reference_id;
        $name = request()->name;
        $existing = [
            'id' => request()->id,
            'referenceType' => request()->reference_type,
            'referenceId' => request()->reference_id
        ];
        switch ($name) {
            case 'PatientSatuSehat':
                $patient_model = $this->PatientModel()->findOrFail($reference_id);
                app(config('app.contracts.Patient'))->prepareStorePatientSatuSehatLog($patient_model,$raw_payload,$existing);
            break;
            case 'EncounterSatuSehat':
                $visit_registration_model = $this->VisitRegistrationModel()->with('visitPatient.patient')->findOrFail($reference_id);
                $visit_patient_model = $visit_registration_model->visitPatient;
                $patient_model = $visit_patient_model->patient;
                app(config('app.contracts.VisitRegistration'))->prepareStoreEncounterSatuSehatLog($visit_registration_model,$visit_patient_model,$patient_model,$raw_payload,$existing);
            break;
        }
        return [
            'message' => 'Sync running in background',
            'raw_payload' => $raw_payload
        ];
        // return $this->storeSatuSehatLog();
    }

    public function destroy(DeleteRequest $request){
        return $this->deleteSatuSehatLog();
    }

    /**
     * Get Satu Sehat dashboard data
     *
     * @param Request $request
     * @return mixed
     */
    public function dashboard(Request $request)
    {
        $month = $request->input('month'); // Optional: Y-m format

        $result = $this->dashboardService->getDashboard(
            tenantId: null,
            workspaceId: null,
            month: $month
        );

        if (!$result['success'] && isset($result['error'])) {
            return response()->json([
                'message' => 'Failed to retrieve dashboard',
                'error' => $result['error'],
                'data' => $result['data'] ?? null
            ], 500);
        }

        return $result['data'];
    }

    /**
     * Update current/wellMed count for a resource type.
     * Call this when a new record is created in WellMed.
     *
     * @param Request $request
     * @return mixed
     */
    public function updateCurrentCount(Request $request)
    {
        $request->validate([
            'resource_type' => 'required|string|in:organization,location,practitioners,patients,encounter,condition,observation',
            'count' => 'required|integer|min:0'
        ]);

        $result = $this->dashboardService->updateCurrentCount(
            resourceType: $request->input('resource_type'),
            currentCount: $request->input('count')
        );

        if (!$result['success']) {
            return response()->json([
                'message' => 'Failed to update current count',
                'error' => $result['error']
            ], 400);
        }

        return [
            'message' => 'Current count updated successfully',
            'data' => $result
        ];
    }

    /**
     * Update synced/satuSehat count for a resource type.
     * Call this when sync to Satu Sehat completes.
     *
     * @param Request $request
     * @return mixed
     */
    public function updateSyncedCount(Request $request)
    {
        $request->validate([
            'resource_type' => 'required|string|in:organization,location,practitioners,patients,encounter,condition,observation',
            'count' => 'required|integer|min:0'
        ]);

        $result = $this->dashboardService->updateSyncedCount(
            resourceType: $request->input('resource_type'),
            syncedCount: $request->input('count')
        );

        if (!$result['success']) {
            return response()->json([
                'message' => 'Failed to update synced count',
                'error' => $result['error']
            ], 400);
        }

        return [
            'message' => 'Synced count updated successfully',
            'data' => $result
        ];
    }

    /**
     * Increment synced count for a resource type.
     * Call this when a single record syncs successfully.
     *
     * @param Request $request
     * @return mixed
     */
    public function incrementSyncedCount(Request $request)
    {
        $request->validate([
            'resource_type' => 'required|string|in:organization,location,practitioners,patients,encounter,condition,observation',
            'increment' => 'integer|min:1'
        ]);

        $result = $this->dashboardService->incrementSyncedCount(
            resourceType: $request->input('resource_type'),
            increment: $request->input('increment', 1)
        );

        if (!$result['success']) {
            return response()->json([
                'message' => 'Failed to increment synced count',
                'error' => $result['error']
            ], 400);
        }

        return [
            'message' => 'Synced count incremented successfully',
            'data' => $result
        ];
    }

    /**
     * Bulk update multiple resource counts at once.
     * Useful for periodic sync operations.
     *
     * @param Request $request
     * @return mixed
     */
    public function bulkUpdateDashboard(Request $request)
    {
        $request->validate([
            'updates' => 'required|array',
            'updates.*.resource_type' => 'required|string|in:organization,location,practitioners,patients,encounter,condition,observation',
            'updates.*.current' => 'integer|min:0',
            'updates.*.synced' => 'integer|min:0'
        ]);

        // Transform array to keyed format
        $updates = [];
        foreach ($request->input('updates') as $update) {
            $resourceType = $update['resource_type'];
            $updates[$resourceType] = [
                'current' => $update['current'] ?? null,
                'synced' => $update['synced'] ?? null
            ];
        }

        $result = $this->dashboardService->bulkUpdate($updates);

        if (!$result['success']) {
            return response()->json([
                'message' => 'Failed to bulk update dashboard',
                'error' => $result['error']
            ], 400);
        }

        return [
            'message' => 'Dashboard updated successfully',
            'data' => $result
        ];
    }

    /**
     * Get current/live dashboard data (no date filtering).
     *
     * @param Request $request
     * @return mixed
     */
    public function currentDashboard(Request $request)
    {
        $result = $this->dashboardService->getCurrentDashboard();

        if (!$result['success'] && isset($result['error'])) {
            return response()->json([
                'message' => 'Failed to retrieve current dashboard',
                'error' => $result['error'],
                'data' => $result['data'] ?? null
            ], 500);
        }

        return $result['data'];
    }

    /**
     * Get example dashboard data from JSON file.
     * Useful for testing or frontend development.
     *
     * @param Request $request
     * @return mixed
     */
    public function exampleDashboard(Request $request)
    {
        $result = $this->dashboardService->getExampleData();

        if (!$result['success']) {
            return response()->json([
                'message' => 'Failed to retrieve example data',
                'error' => $result['error']
            ], 500);
        }

        return $result['data'];
    }

    /**
     * Get list of available monthly snapshots.
     *
     * @param Request $request
     * @return mixed
     */
    public function availableSnapshots(Request $request)
    {
        $limit = $request->input('limit', 12);

        $result = $this->dashboardService->getAvailableSnapshots(
            tenantId: null,
            workspaceId: null,
            limit: $limit
        );

        if (!$result['success']) {
            return response()->json([
                'message' => 'Failed to retrieve snapshots',
                'error' => $result['error']
            ], 500);
        }

        return [
            'snapshots' => $result['data'],
            'total' => $result['total']
        ];
    }

    /**
     * Get monthly snapshot for a specific month.
     *
     * @param Request $request
     * @param string $month Month in Y-m format (e.g., 2025-01)
     * @return mixed
     */
    public function monthlySnapshot(Request $request, string $month)
    {
        // Validate month format
        if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
            return response()->json([
                'message' => 'Invalid month format. Use Y-m format (e.g., 2025-01)'
            ], 400);
        }

        $result = $this->dashboardService->getMonthlySnapshot(
            tenantId: null,
            workspaceId: null,
            month: $month
        );

        if (!$result['success']) {
            return response()->json([
                'message' => 'Failed to retrieve monthly snapshot',
                'error' => $result['error']
            ], 500);
        }

        if ($result['data'] === null) {
            return response()->json([
                'message' => $result['message'] ?? "No snapshot found for {$month}",
                'month' => $month
            ], 404);
        }

        return $result['data'];
    }

    /**
     * Store a monthly snapshot of current dashboard data.
     *
     * @param Request $request
     * @param string $month Month in Y-m format (e.g., 2025-01)
     * @return mixed
     */
    public function storeSnapshot(Request $request, string $month)
    {
        // Validate month format
        if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
            return response()->json([
                'message' => 'Invalid month format. Use Y-m format (e.g., 2025-01)'
            ], 400);
        }

        $result = $this->dashboardService->storeMonthlySnapshot($month);

        if (!$result['success']) {
            return response()->json([
                'message' => 'Failed to store monthly snapshot',
                'error' => $result['error']
            ], 400);
        }

        return [
            'message' => $result['message'],
            'month' => $month,
            'id' => $result['id']
        ];
    }
}
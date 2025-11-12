<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\Http\Resources\PatientsResource;
use App\Models\Patient;
use App\Traits\CustomErrorMessage;
use App\Traits\DebugError;
use App\Traits\HandlesAuthorization;
use App\Traits\HttpResponses;
use Exception;

class PatientsController extends Controller
{
    use CustomErrorMessage, DebugError, HandlesAuthorization, HttpResponses;

    /* Display a listing of the resource.
     */
    public function index()
    {
        try {
            $this->ensureAuthenticated();
            $patients = PatientsResource::collection(Patient::orderBy('created_at', 'desc')->paginate(10));

            return $this->success($patients, 'List of patients', 200);
        } catch (Exception $e) {
            $this->debugAppError($e);
            $err_msg = $e->getMessage();

            return $this->error(null, $err_msg, $e->getCode() ?: 404);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePatientRequest $request)
    {
        try {
            $this->ensureAuthenticated();
            $request->validated();
            $patient = Patient::create([
                'user_id' => $request->user_id,
                'blood_type' => $request->blood_type,
                'allergies' => $request->allergies,
                'current_medications' => $request->current_medications,
                'emergency_contact' => $request->emergency_contact,
            ]);

            $formatedPatients = new PatientsResource($patient);

            return $this->success($formatedPatients, 'patient record created successfully', 201);
        } catch (Exception $e) {
            $this->debugAppError($e);
            $err_msg = $e->getMessage();

            return $this->error(null, $err_msg, 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $this->ensureAuthenticated();
            $patient = Patient::findOrFail($id);
            $patient_data = PatientsResource::make($patient);

            return $this->success($patient_data, 'Patient Information', 200);
        } catch (Exception $e) {
            $this->debugAppError($e);
            $err_msg = $e->getMessage();

            return $this->error(null, $err_msg, $e->getCode() ?: 422);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePatientRequest $request, Patient $patient)
    {
        try {
            $this->ensureAuthenticated();
            $this->ensureOwnership($patient);
            $request->validated();
            $formatted_data = PatientsResource::make($patient->update($request->all()));

            return $this->success($formatted_data, 'Patient updated successfully', 200);
        } catch (Exception $e) {
            $this->debugAppError($e);
            $err_msg = $e->getMessage();

            return $this->error(null, $err_msg, $e->getCode() ?: 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */ /**
     * Remove the specified patient from storage.
     */
    public function destroy(Patient $patient)
    {
        try {
            $this->ensureAuthenticated();
            // Ensure the authenticated user owns the patient resource
            $this->ensureOwnership($patient);
            // Delete the patient record
            $patient->delete();

            return $this->success(null, null, 204);
        } catch (Exception $e) {
            $this->debugAppError($e);
            $err_msg = $e->getMessage();

            return $this->error(null, $err_msg, $e->getCode() ?: 422);
        }
    }
}

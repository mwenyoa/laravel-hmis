<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Patient;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\PatientsResource;
use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;

class PatientsController extends Controller
{
    use HttpResponses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            if (!auth()->check()) {
                return $this->error(null, "Please login to view patients", 401);
            }
            $patients = PatientsResource::collection(Patient::orderBy("created_at", "desc")->paginate(10));
            return $this->success($patients, "List of patients", 200);
        } catch (Exception $e) {
            return $this->error(null, $e->getMessage(), $e->getCode() ?: 404);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePatientRequest $request)
    {
        try {
            if (!auth()->user()) {
                return $this->error(null, "You are not logged in to create a patient", 401);
            }

            $request->validated($request->all());
            $user = Auth::user();
            $patient = Patient::create([
                "user_id" => $user->id,
                "home_address" => $request->home_address,
                "diagnosis" => $request->diagnosis,
            ]);

            $formatedPatients = PatientsResource::make($patient);
            return $this->success($formatedPatients, "patient record created successfully", 200);
        } catch (Exception $e) {
            $error_msg = $e->getMessage();
            return $this->error(null, $error_msg, 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            if (!auth()->check()) {
                return $this->error(null, "Please login to view this patient", 401);
            }
            $patient = Patient::findOrFail($id);
            $patient_data = PatientsResource::make($patient);
            return $this->success($patient_data, "Patient Information", 200);
        } catch (Exception $e) {
            $error_msg = $e->getMessage();
            return $this->error(null, $error_msg, $e->getCode() ?:422);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePatientRequest $request, string $id)
    {
        try{
            if (!auth()->check()) {
                return $this->error(null, "please login to continue",401);
            }
            $patient = Patient::findOrFail($id);
           if(auth()->id() !== $patient->user_id){
            return $this->error(null, "You're not permitted to update this patients data", 403);
           }
           $formated_data = PatientsResource::make( $patient->update($request->all()));

           return $this->success($formated_data, "Patient updated successfully", 200);
        } catch (Exception $e) {
        }catch(Exception $e){
            $error_msg = $e->getMessage();
            return $this->error(null, $error_msg, 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            if (!auth()->check()) {
                return $this->error(null, "You must be logged in to continue", 401);
            }

            $patient = Patient::findOrFail($id);
            if (auth()->id() !== $patient->user_id) {
                return $this->error(null, "You're not permitted to delete this patient's record", 403);
            }
            $patient->delete();
            return $this->success(null, null, 204);
        } catch (Exception $e) {
            $error_msg = $e->getMessage();
            return $this->error(null, $error_msg, $e->getCode() ?: 500);
        }
    }
}

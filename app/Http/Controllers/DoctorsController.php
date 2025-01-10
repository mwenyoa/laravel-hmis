<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Doctor;
use App\Traits\DebugError;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use App\Traits\CustomErrorMessage;
use App\Traits\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\DoctorsResource;
use App\Http\Requests\StoreDoctorRequest;

class DoctorsController extends Controller
{
    use HttpResponses, HandlesAuthorization, DebugError, CustomErrorMessage;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        try {
            $this->ensureAuthenticated();
            $doctors = DoctorsResource::collection(Doctor::orderBy("created_at", "desc")->paginate(10));
            return $this->success($doctors, "List of doctors", 200);
        } catch (Exception $e) {
            $this->debugAppError($e);
            $err_msg = $e->getMessage();;
            return $this->error(null, $err_msg, $e->getCode() ?: 404);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDoctorRequest $request)
    {
        try {
            $this->ensureAuthenticated();
            $request->validated($request->all());
            $user = Auth::user();
            $doctor = Doctor::create([
                "user_id" => $user->id,
                "specialization" => $request->specialization,
                "hpcno" => $request->hpcno,
                "consultancy_fee" => $request->consultancy_fee,
            ]);
            $doctorResource = new DoctorsResource($doctor);
            return $this->success($doctorResource, "Doctor created successfully", 201);
        } catch (Exception $e) {
            $this->debugAppError($e);
            $err_msg = $e->getMessage();;
            return $this->error(null, $err_msg, 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Doctor $doctor)
    {
        try {
            $this->ensureAuthenticated();
            $doctor = DoctorsResource::make($doctor);
            return $this->success($doctor, "Doctor's Information", 200);
        } catch (Exception $e) {
            $this->debugAppError($e);
            $err_msg = $e->getMessage();;
            return $this->error(null, $err_msg, 404);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $this->ensureAuthenticated();
            $doctor = Doctor::findOrFail($id);
            $this->ensureOwnership($doctor);
            $doctor->update($request->all());
            $formatted_data = DoctorsResource::make($doctor);
            return $this->success($formatted_data, "Doctor updated successfully", 200);
        } catch (Exception $e) {
            $this->debugAppError($e);
            return $this->error(null, $e->getMessage(), $e->getCode() ?: 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Doctor $doctor)
    {
        try {
            $this->ensureAuthenticated();
            $this->ensureOwnership($doctor);
            $doctor->delete();
            return $this->success(null, null, 204);
        } catch (Exception $e) {
            $this->debugAppError($e);
            $err_msg = $e->getMessage();
            return $this->error(null, $err_msg, $e->getCode() ?: 500);
        }
    }
}

<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Doctor;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\DoctorsResource;
use App\Http\Requests\StoreDoctorRequest;
use App\Traits\HandlesAuthorization;

class DoctorsController extends Controller
{
    use HttpResponses, HandlesAuthorization;
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
            return $this->error(null, $e->getMessage(), $e->getCode() ?: 404);
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

            return $this->success($doctor, "Doctor created successfully", 201);
        } catch (Exception $e) {
            $error_msg = $e->getMessage();
            return $this->error(null, $error_msg, 422);
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
            $error_msg = $e->getMessage();
            return $this->error(null, $error_msg, 404);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $this->ensureAuthenticated();
            $doctor = Doctor::findOrFail($id);
            $this->ensureOwnership($doctor);
            $doctor->update($request->all());
            $formated_data = DoctorsResource::make($doctor);
            return $this->success($formated_data, "Doctor updated successfully", 200);
        } catch (Exception $e) {
            $error_msg = $e->getMessage();
            return $this->error(null, $error_msg, $e->getCode() ?: 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $this->ensureAuthenticated();
            $doctor = Doctor::findOrFail($id);
            $this->ensureOwnership($doctor);
            $doctor->delete();
            return $this->success(null, "Doctor's record successfully deleted", 204);
        } catch (Exception $e) {
            $error_msg = $e->getMessage();
            return $this->error(null, $error_msg, $e->getCode() ?: 500);
        }
    }
}

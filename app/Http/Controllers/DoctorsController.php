<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDoctorRequest;
use App\Http\Resources\DoctorsResource;
use App\Models\Doctor;
use App\Traits\HttpResponses;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DoctorsController extends Controller
{
    use HttpResponses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        try {
            if (!Auth::check()) {
                return $this->error(null, "You not logged in to view doctors", 401);
            }

            $doctors = DoctorsResource::collection(Doctor::orderBy("created_at", "desc")->paginate(10));
            return $this->success($doctors, "List of doctors", 200);
        } catch (Exception $e) {
            return $this->error(null, $e->getMessage(), $e->getCode()?:404);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDoctorRequest $request)
    {
        try {
            if (!auth()->user()) {
                return $this->error(null, "You are not logged in to create doctor's account", 401);
            }
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

            if (!auth()->check()) {
                return $this->error(null, "Please login to view this doctor", 401);
            }
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
            if (!auth()->check()) {
                return $this->error(null, "You must be logged in to update doctors information", 401);
            }
            $doctor = Doctor::findOrFail($id);
            if (auth()->id() !== $doctor->user->id) {
                return $this->error(null, "You're not permited to update this doctor's information", 403);
            }
            $doctor->update($request->all());
            $formated_data = DoctorsResource::make($doctor);
            return $this->success($formated_data, "Doctor updated successfully", 200);
        } catch (Exception $e) {
            $error_msg = $e->getMessage();
            return $this->error(null, $error_msg, $e->getCode() ?:500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            if (!auth()->check()) {
                return $this->error(null, "You must be logged in to delete this doctor's record", 401);
            }

            $doctor = Doctor::findOrFail($id);
            if (auth()->id() !== $doctor->user->id) {
                return $this->error(null, "You're not permited to delete this doctor's record", 403);
            }
            $doctor->delete();
            return $this->success(null, "Doctor's record sucessfully deleted", 204);
        } catch (Exception $e) {
            $error_msg = $e->getMessage();
            return $this->error(null, $error_msg, $e->getCode() ?: 500);
        }
    }
}

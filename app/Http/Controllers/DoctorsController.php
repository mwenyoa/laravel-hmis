<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDoctorRequest;
use App\Http\Resources\DoctorsResource;
use App\Models\Doctor;
use App\Traits\CustomErrorMessage;
use App\Traits\DebugError;
use App\Traits\HandlesAuthorization;
use App\Traits\HttpResponses;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

class DoctorsController extends Controller
{
    use CustomErrorMessage, DebugError, HandlesAuthorization, HttpResponses;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $this->ensureAuthenticated();

            // Eager load relationships and paginate
            $doctors = Doctor::with(['user', 'specialties'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            // Transform using DoctorsResource
            $doctorsResource = DoctorsResource::collection($doctors);

            return $this->success($doctorsResource, 'List of doctors');
        } catch (HttpResponseException $e) {
            // Re-throw authorization exceptions to let Laravel handle them
            throw $e;
        } catch (Exception $e) {
            $this->debugAppError($e);

            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDoctorRequest $request)
    {
        try {
            $this->ensureAuthenticated();

            // Fix: Remove the parameter from validated() method
            $request->validated();

            $doctor = Doctor::create([
                'user_id' => $request->user_id,
                'hpcno' => $request->hpcno,
                'consultancy_fee' => $request->consultancy_fee,
                'experience' => $request->experience,
                'availability' => $request->availability,
            ]);

            $doctorResource = new DoctorsResource($doctor);

            return $this->success($doctorResource, 'Doctor created successfully', null, 201);
        } catch (HttpResponseException $e) {
            throw $e;
        } catch (Exception $e) {
            $this->debugAppError($e);

            return $this->error($e->getMessage(), 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $this->ensureAuthenticated();
            $doctor = Doctor::findOrFail($id);
            $doctorResource = DoctorsResource::make($doctor);

            return $this->success($doctorResource, "Doctor's Information");
        } catch (HttpResponseException $e) {
            throw $e;
        } catch (ModelNotFoundException $e) {
            $this->debugAppError($e);

            return $this->error('Doctor not found', 404);
        } catch (Exception $e) {
            $this->debugAppError($e);

            return $this->error($e->getMessage(), 500);
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

            return $this->success($formatted_data, 'Doctor updated successfully');
        } catch (HttpResponseException $e) {
            throw $e;
        } catch (ModelNotFoundException $e) {
            $this->debugAppError($e);

            return $this->error('Doctor not found', 404);
        } catch (Exception $e) {
            $this->debugAppError($e);

            return $this->error($e->getMessage(), 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $this->ensureAuthenticated();
            $doctor = Doctor::findOrFail($id);
            $this->ensureOwnership($doctor);
            $doctor->delete();

            return $this->success(null, 'Doctor deleted successfully', null, 204);
        } catch (HttpResponseException $e) {
            throw $e;
        } catch (ModelNotFoundException $e) {
            $this->debugAppError($e);

            return $this->error('Doctor not found', 404);
        } catch (Exception $e) {
            $this->debugAppError($e);

            return $this->error($e->getMessage(), 500);
        }
    }
}

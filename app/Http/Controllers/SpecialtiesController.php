<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSpecialtyRequest;
use App\Http\Requests\UpdateSpecialtyRequest;
use App\Http\Resources\SpecialtiesResource;
use App\Models\Specialty;
use App\Traits\DebugError;
use App\Traits\HandlesAuthorization;
use App\Traits\HttpResponses;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SpecialtiesController extends Controller
{
    use DebugError, HandlesAuthorization, HttpResponses;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $this->ensureAuthenticated();

            // Eager load the doctor relationship
            $specialties = Specialty::with('doctor')->orderBy('created_at', 'desc')->paginate(10);
            $specialtiesResource = SpecialtiesResource::collection($specialties);

            return $this->success($specialtiesResource, 'Specialties List');
        } catch (Exception $e) {
            $this->debugAppError($e);

            return $this->error($e->getMessage(), 500);
        }
    }

    public function store(StoreSpecialtyRequest $request)
    {
        try {
            $this->ensureAuthenticated();
            $request->validated($request->all());

            $specialty = Specialty::create([
                'specialization_name' => $request->specialization_name,
                'description' => $request->description,
                'doctor_id' => $request->doctor_id,
                'years_experience' => $request->years_experience,
                'certification' => $request->certification,
            ]);

            $specialtyResource = new SpecialtiesResource($specialty->load('doctor'));

            return $this->success($specialtyResource, 'Specialty created successfully', null, 201);
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
            $specialty = Specialty::with('doctors')->findOrFail($id);
            $specialtyResource = SpecialtiesResource::make($specialty);

            return $this->success($specialtyResource, 'Specialty Information');
        } catch (ModelNotFoundException $e) {
            $this->debugAppError($e);

            return $this->error('Specialty not found', 404);
        } catch (Exception $e) {
            $this->debugAppError($e);

            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSpecialtyRequest $request, $id)
    {
        try {
            $this->ensureAuthenticated();
            $specialty = Specialty::findOrFail($id);
            $this->authorize('update', $specialty);
            $this->ensureOwnership($specialty);
            $specialty->update($request->validated());

            return $this->success($specialty, 'Specialty updated successfully');
        } catch (ModelNotFoundException $e) {
            $this->debugAppError($e);

            return $this->error('Specialty not found', 404);
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
            $specialty = Specialty::findOrFail($id);
            $this->ensureOwnership($specialty);

            if ($specialty->delete()) {
                return $this->success(null, 'Specialty deleted successfully', null, 204);
            }

        } catch (ModelNotFoundException $e) {
            $this->debugAppError($e);

            return $this->error('Specialty not found', 404);
        } catch (Exception $e) {
            $this->debugAppError($e);

            return $this->error($e->getMessage(), 500);
        }
    }
}

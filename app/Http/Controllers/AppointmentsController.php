<?php

namespace App\Http\Controllers;

use Exception;
use App\Traits\DebugError;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use App\Traits\CustomErrorMessage;
use App\Traits\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\AppointmentsResource;

class AppointmentsController extends Controller
{
    use CustomErrorMessage, DebugError, HandlesAuthorization, HttpResponses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {try {
        $this->ensureAuthenticated();
        $appointments = Appointment::orderBy("created_at", "desc")->paginate(10);
        $AppointmentResource = AppointmentsResource::collection($appointments);
        return $this->success($AppointmentResource, "List of appointments", 200);
    } catch (Exception $e) {
        $this->debugAppError($e);
        $err_msg = $e->getMessage();;
        return $this->error(null, $err_msg, $e->getCode() ?: 404);
    }}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $this->ensureAuthenticated();
            $request->validated($request->all());
            $auth_doctor = Auth::user()->doctors->first();
            $appointment = Appointment::create([
                "doctor_id" => $auth_doctor->doctor_id,
                "patient_id" => $request->patient_id,
                "appointment_date" => $request->appointment_date,
                "appointment_time" => $request->appointment_time,
                "purpose" => $request->purpose,
            ]);
            $appointmentResource = new AppointmentsResource($appointment);
            return $this->success($appointmentResource, "Appointment created successfully", 201);
        } catch (Exception $e) {
            $this->debugAppError($e);
            $err_msg = $e->getMessage();;
            return $this->error(null, $err_msg, 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Appointment $appointment)
    {
        try {
            $this->ensureAuthenticated();
            $appointmentResource = AppointmentsResource::make($appointment);
            return $this->success($appointmentResource, "Appointment Information", 200);
        } catch (Exception $e) {
            $this->debugAppError($e);
            $err_msg = $e->getMessage();;
            return $this->error(null, $err_msg, $e->getCode() ?: 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Appointment $appointment)
    {
        try {
            $this->ensureAuthenticated();
            // Ensure the authenticated doctor owns the appointment
            $auth_doctor = Auth::user()->doctors->first();
            $this->ensureOwnership($auth_doctor);
            $request->validated($request->all());
            $appointment->update([
                "doctor_id" => $auth_doctor->id,
                "patient_id" => $request->patient_id,
                "appointment_date" => $request->appointment_date,
                "appointment_time" => $request->appointment_time,
                "purpose" => $request->purpose,
            ]);

        } catch (Exception $e) {
            $this->debugAppError($e);
            $err_msg = $e->getMessage();;
            return $this->error(null, $err_msg, $e->getCode() ?: 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Appointment $appointment)
    {
        try {
            $this->ensureAuthenticated();
            // Ensure the authenticated doctor owns the appointment
            $auth_doctor = Auth::user()->doctors->first();
            $this->ensureOwnership($auth_doctor);
            if ($appointment->doctor_id !== $auth_doctor->id) {
                return $this->error(null, "You are not authorized to delete this appointment", 403);
            }
            $appointment->delete();
            return $this->success(null, "Appointment deleted successfully", 200);
        } catch (Exception $e) {
            $this->debugAppError($e);
            $err_msg = $e->getMessage();;
            return $this->error(null, $err_msg, $e->getCode() ?: 422);
        }
    }

}

<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Patient;
use App\Models\Feedback;
use App\Traits\DebugError;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use App\Traits\CustomErrorMessage;
use App\Traits\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\FeedbacksResource;
use App\Http\Requests\StoreFeedbackRequest;

// Singular name for resource

class FeedbacksController extends Controller
{
    use HttpResponses, HandlesAuthorization, DebugError, CustomErrorMessage;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $this->ensureAuthenticated();
            $feedbackResources = FeedbacksResource::collection(Feedback::orderBy("created_at", "desc")->paginate(10));
            return $this->success($feedbackResources, "Patient's feedback", 200);
        } catch (Exception $e) {
            $this->debugAppError($e);
            $err_msg = $e->getMessage();;
            return $this->error(null, $err_msg, 422); // Ensure return statement
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFeedbackRequest $request)
    {
        try {
            $this->ensureAuthenticated();
            $request->validated();
            $auth_user = Auth::user();
            $patient = $auth_user->patients->first();
            // dd($auth_user->patients);
            $this->ensureOwnership($patient);
            $feedback = Feedback::create([
                "patient_id" => $patient->id, // Use patient's actual ID
                "message" => $request->message,
            ]);
            $feedbackResource = new FeedbacksResource($feedback);
            return $this->success($feedbackResource, "Patient feedback saved successfully", 201);
        } catch (Exception $e) {
            $this->debugAppError($e);
            $err_msg = $e->getMessage();;
            return $this->error(null, $err_msg, 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Feedback $feedback)
    {
        try {
            $this->ensureAuthenticated();
            $this->ensureOwnership($feedback->patient); // Verify ownership

            $feedbackResource = FeedbacksResource::make($feedback);
            return $this->success($feedbackResource, "Feedback retrieved successfully", 200);
        } catch (Exception $e) {
            $this->debugAppError($e);
            $err_msg = $e->getMessage();;
            return $this->error(null, $err_msg, 422);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Feedback $feedback)
    {
        try {
            $this->ensureAuthenticated();
            // Verify ownership
            $this->ensureOwnership($feedback->patient);
            $feedbackResource = new FeedbacksResource($feedback->update($request->only('message')));
            return $this->success($feedbackResource, "Feedback updated successfully", 200);
        } catch (Exception $e) {
            $this->debugAppError($e);
            $err_msg = $e->getMessage();;
            return $this->error(null, $err_msg, 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Feedback $feedback)
    {
        try {
            $this->ensureAuthenticated();
            // Verify ownership
            $this->ensureOwnership($feedback->patient);
            $feedback->delete();
            return $this->success(null, "Feedback deleted successfully", 200);
        } catch (Exception $e) {
            $this->debugAppError($e);
            $err_msg = $e->getMessage();;
            return $this->error(null, $err_msg, 422);
        }
    }
}

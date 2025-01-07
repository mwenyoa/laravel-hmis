<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Patient;
use App\Models\Feedback;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use App\Traits\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\FeedbackResource;
use App\Http\Resources\FeedbacksResource;
use App\Http\Requests\StoreFeedbackRequest; // Singular name for resource

class FeedbacksController extends Controller
{
    use HttpResponses, HandlesAuthorization;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $this->ensureAuthenticated();
            $feedbacks = Feedback::orderBy("created_at", "desc")->paginate(10); // Fetch feedbacks directly
            $feedbackResources = FeedbacksResource::collection($feedbacks); // Use collection for multiple items
            return $this->success($feedbackResources, "Patient's feedback", 200);
        } catch (Exception $e) {
            $err_msg = $e->getMessage();
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
            // dd($auth_user->patients);
            $this->ensureOwnership($auth_user->patients);
            $feedback = Feedback::create([
                "patient_id" => $auth_user->patients->id, // Use patient's actual ID
                "message" => $request->message,
            ]);

            return $this->success($feedback, "Patient feedback saved successfully", 201);
        } catch (Exception $e) {
            $err_msg = $e->getMessage();
            return $this->error(null, $e->getMessage(), 422);
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
            $err_msg = $e->getMessage();
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
            $this->ensureOwnership($feedback->patient); // Verify ownership

            $feedback->update($request->only('message')); // Update only specific fields
            return $this->success($feedback, "Feedback updated successfully", 200);
        } catch (Exception $e) {
            $err_msg = $e->getMessage();
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
            $this->ensureOwnership($feedback->patient); // Verify ownership

            $feedback->delete();
            return $this->success(null, "Feedback deleted successfully", 200);
        } catch (Exception $e) {
            $err_msg = $e->getMessage();
            return $this->error(null, $err_msg, 422);
        }
    }
}
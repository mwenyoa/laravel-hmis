<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UsersResource;
use App\Models\User;
use App\Traits\HttpResponses;
use Exception;

class UsersController extends Controller
{

    use HttpResponses;
    /**
     * Display a listing of the resource.
     */
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Check if the user is authenticated
            if (!auth()->check()) {
                return $this->error(null, "Please login to view", 401);
            }

            // Fetch users and return them using the resource
            $users = UsersResource::collection(User::orderBy("created_at", "desc")->paginate(10));
            return $this->success($users, "Users Information", 200);
        } catch (Exception $e) {
            return $this->error(null, $e->getMessage(), 404);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        try {
            // Check if the user is authenticated
            if (!auth()->check()) {
                return $this->error(null, "Unauthorized access", 401);
            }
            $user_data = UsersResource::make($user);
            return $this->success($user_data, "$user->first_name $user->last_name Information");
        } catch (Exception $e) {
            return $this->error(null, $e->getMessage(), $e->getCode());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, $id)
    {
        try {

            if (!auth()->check()) {
                return $this->error(null, "You are not authorized to update this data", 401);
            }

            $user = User::findOrFail($id);

            if ($user->id !== auth()->id()) {
                return $this->error(null, "You are not authorized to update this record", 403);
            }

            $validatedData = $request->validated();

            $user->update($validatedData);

            $formattedData = UsersResource::make($user);

            // Return success response
            return $this->success($formattedData, "User data updated successfully", 200);
        } catch (Exception $e) {
            // Handle exceptions and return error response
            return $this->error(null, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**

     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

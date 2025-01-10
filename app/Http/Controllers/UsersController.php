<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Traits\DebugError;
use App\Traits\HttpResponses;
use App\Traits\CustomErrorMessage;
use App\Traits\HandlesAuthorization;
use App\Http\Resources\UsersResource;
use App\Http\Requests\UpdateUserRequest;

class UsersController extends Controller
{

    use HttpResponses, HandlesAuthorization, DebugError, CustomErrorMessage;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Check if the user is authenticated
            $this->ensureAuthenticated();
            // Fetch users and return them using the resource
            $users = User::orderBy("created_at", "desc")->paginate(10); 
            $usersResource = UsersResource::collection($users);
            return $this->success($usersResource, "Users Information", 200);
        } catch (Exception $e) {
            $this->debugAppError($e);
            $err_msg = $e->getMessage();;
            return $this->error(null, $err_msg, 404);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        try {
            // Check if the user is authenticated
            $this->ensureAuthenticated();
            $user_data = UsersResource::make($user);
            return $this->success($user_data, "$user->first_name $user->last_name Information");
        } catch (Exception $e) {
            $this->debugAppError($e);
            $err_msg = $e->getMessage();;
            return $this->error(null, $err_msg, $e->getCode() ?: 422);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, $id)
    {
        try {
            $this->ensureAuthenticated();
            $user = User::findOrFail($id);
            $this->ensureOwnership($user);
            $validatedData = $request->validated();
            $user->update($validatedData);
            $formattedData = UsersResource::make($user);
            return $this->success($formattedData, "User data updated successfully", 200);
        } catch (Exception $e) {
            $this->debugAppError($e);
            $err_msg = $e->getMessage();;
            return $this->error(null, $err_msg, $e->getCode() ?: 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try {
            $this->ensureAuthenticated();
            $this->ensureOwnership($user);
            $user->delete();
            return $this->success(null, null, 204);
        } catch (Exception $e) {
            $this->debugAppError($e);
            $err_msg = $e->getMessage();;
            return $this->error(null, $err_msg, $e->getCode() ?: 500);
        }
    }
}

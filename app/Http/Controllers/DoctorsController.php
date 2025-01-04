<?php

namespace App\Http\Controllers;

use Log;
use Exception;
use App\Models\Doctor;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreDoctorRequest;

class DoctorsController extends Controller
{
    use HttpResponses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDoctorRequest $request)
    {

     $user = Auth::user();

        try {
            if($user){
                $request->validated($request->all());
                $doctor = Doctor::create([
                    "user_id" => $user->id,
                    "specialization" => $request->specialization,
                    "hpcno" => $request->hpcno,
                    "consultancy_fee" => $request->consultancy_fee,
                ]);
    
                return $this->success([
                    "doctor" => $doctor,
                ], "Doctor created successfully", 201);
            }else{
                return $this->error(null, "User not found", 404);
            }
           
        } catch (Exception $e) {
            $error_msg = $e->getMessage();
            return $this->error(null, $error_msg, 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

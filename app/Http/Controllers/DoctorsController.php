<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDoctorRequest;
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
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDoctorRequest $request)
    {

        try {
            $request->validated($request->all());

            $doctor = Doctor::create([
                "user_id" => auth()->user()->id,
                "specialization" => $request->specialization,
                "hpcno" => $request->hpcno,
                "consultancy_fee" => $request->consultancy_fee,
            ]);

            return $this->success([
                "doctor" => $doctor,
            ], "Doctor created successfully", 201);
        } catch (Exception $e) {
            $error_msg = $e->getMessage();
            return $this->error(null, $error_msg, 401);
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

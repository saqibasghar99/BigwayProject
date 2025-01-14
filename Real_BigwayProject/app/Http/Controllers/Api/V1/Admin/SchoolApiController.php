<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSchoolRequest;
use App\Http\Requests\UpdateSchoolRequest;
use App\Http\Resources\Admin\SchoolResource;
use App\Models\School;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Http;

class SchoolApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('school_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new SchoolResource(School::with(['createdBy', 'updatedBy', 'location', 'user'])->get());
    }

    public function store(Request $request)
    {
        
        try{

            $validatedData = $request->validate([
                'email' => 'nullable|email|unique:users,email',
                'password' => 'required|min:8',
                'contact' => 'required|unique:users,contact',
                'name' => 'required',
                'address' => 'nullable',
            ]);
    
            $user = User::create([
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'contact' => $validatedData['contact'],
                'type' => 'school',
            ]);
    
            $school = School::create([
                'name' => $validatedData['name'],
                'address' => $validatedData['address'],
                'user_id' => $user->id,
            ]);
    
            $resultShow = [
                "id" => $user->id,
                "email" => $user->email,
                "contact" => $user->contact,
                "type" => $user->type,
                "name" => $school->name,
                "address" => $school->address,
            ];
    
            return response()->json(['success' => true, 'data' => $resultShow], 201);
        }
        catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        }
    }


    public function show(School $school)
    {
        abort_if(Gate::denies('school_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new SchoolResource($school->load(['createdBy', 'updatedBy', 'location', 'user']));
    }

    public function update(UpdateSchoolRequest $request, School $school)
    {
        $school->update($request->validated());

        return (new SchoolResource($school))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(School $school)
    {
        abort_if(Gate::denies('school_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $school->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

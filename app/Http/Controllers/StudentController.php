<?php

namespace App\Http\Controllers;

use App\Http\Resources\StudentResource;
use Illuminate\Http\Request;
use App\Models\Student;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StudentImport;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::paginate(10);
        return StudentResource::collection($students);
    }
    
    public function show($id)
    {
        $student = Student::find($id);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found'
            ], 404);
        }

        return new StudentResource($student);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:students',
            'address' => 'required',
            'study_course' => 'required'
        ]);

        $student = new Student([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'address' => $request->input('address'),
            'study_course' => $request->input('study_course')
        ]);
        
        $student->save();

        $name = $student->name;
        $address = $student->address;
    
        return response()->json([
            'success' => true,
            'Name' => $name,
            'Address' => $address
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $student = Student::find($id);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found'
            ], 404);
        }

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:students,email,'.$id,
            'address' => 'required',
            'study_course' => 'required'
        ]);

        $student->name = $request->input('name');
        $student->email = $request->input('email');
        $student->address = $request->input('address');
        $student->study_course = $request->input('study_course');
        $student->save();

        $name = $student->name;
        $address = $student->address;
    
        return response()->json([
            'success' => true,
            'Name' => $name,
            'Address' => $address
        ], 201);
    }
    
    public function destroy($id)
    {
        $student = Student::find($id);
    
        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found'
            ], 404);
        }
    
        $student->delete();
    
        return response()->json([
            'success' => true,
            'message' => 'Student deleted'
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->input('search');
    
        $students = Student::where('name', 'LIKE', "%$query%")
                            ->orWhere('email', 'LIKE', "%$query%")
                            ->get();
    
        if ($students->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found',
            ], 404);
        }
        
        return StudentResource::collection($students);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xls,xlsx,csv'
        ]);

        $file = $request->file('file');

        if (!$file) {
            return response()->json([
                'success' => false,
                'message' => 'Select file'
            ], 404);
        }

        Excel::import(new StudentImport, $file);

        return response()->json([
            'success' => true,
            'message' => 'Imported successfully'
        ]);
    }
}  

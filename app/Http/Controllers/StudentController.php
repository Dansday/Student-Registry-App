<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 3);
        $students = Student::paginate($perPage);
        return response()->json($students, 200);
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

        return response()->json([
            'success' => true,
            'data' => $student
        ], 201);
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

        return response()->json([
            'success' => true,
            'data' => $student
        ]);
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
    
        return response()->json([
            'success' => true,
            'data' => $student
        ]);
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
    
        return response()->json([
            'success' => true,
            'data' => $students,
        ]);
    }
    
}  

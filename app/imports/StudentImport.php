<?php

namespace App\Imports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $student = Student::where('email', $row['email'])->first();

        if ($student) {
            $student->update([
                'name' => $row['name'],
                'address' => $row['address'],
                'study_course' => $row['study_course']
            ]);
        } else {
            $student = new Student([
                'name' => $row['name'],
                'email' => $row['email'],
                'address' => $row['address'],
                'study_course' => $row['study_course']
            ]);

            $student->save();
        }

        return $student;
    }

}
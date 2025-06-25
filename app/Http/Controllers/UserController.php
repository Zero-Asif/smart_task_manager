<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Spatie\SimpleExcel\SimpleExcelReader;
use App\Models\User;

class UserController extends Controller
{
    // Export method
    public function exportUsers()
    {
        $filePath = storage_path('app/users-export.csv');
        $users = User::all(['name', 'email'])->toArray();

        SimpleExcelWriter::create($filePath)
            ->addRows($users);

        return response()->download($filePath)->deleteFileAfterSend();
    }

    // Import method
    public function importUsers(Request $request)
    {
        $request->validate([
            'csv' => 'required|file|mimes:csv,txt',
        ]);

        if (!$request->hasFile('csv')) {
            return back()->withErrors(['csv' => 'CSV file is required.']);
        }

        $path = $request->file('csv')->store('imports');
        SimpleExcelReader::create(storage_path("app/{$path}"))->getRows()
            ->each(function (array $row) {
                User::updateOrCreate(
                    ['email' => $row['email']],
                    [
                        'name' => $row['name'],
                        'password' => bcrypt($row['password'] ?? 'password'), // Default password
                    ]
                );
            });

        return back()->with('success', 'Users imported!');
    }

}

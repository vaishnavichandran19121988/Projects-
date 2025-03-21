<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // Ensure user is authenticated
    }

    public function index()
    {
        $user = Auth::user(); // Get the currently authenticated user
             

       # fetching taught course 
        $courses = $user->taughtCourses; 

        if ($user->role === 'Lecturer') {
            // For lecturers, return the lecturer dashboard
            return view('lecturer.dashboard', ['user' => $user, 'courses' => $courses]);
        } else {
            // For students, return the student dashboard
            return view('student.dashboard', ['user' => $user]);
        }
    }
    public function showProfile()
    {
        $user = Auth::user(); // Get the authenticated user
        return view('profile', compact('user')); // Return the profile view
    }

    public function updateProfile(Request $request, $id)
    {
        $user = Auth::user();

        // Validate the form data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Optional, file must be an image
        ]);

        // Update user's name and email
        $user->name = $request->input('name');
        $user->email = $request->input('email');

        // If a profile photo is uploaded, process it
        if ($request->hasFile('profile_photo')) {
            // Delete old profile photo if it exists
            if ($user->profile_photo && Storage::exists('profile_photos/' . $user->profile_photo)) {
                Storage::delete('profile_photos/' . $user->profile_photo);
            }

            // Store new profile photo
            $file = $request->file('profile_photo');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('profile_photos', $filename, 'public');
            $user->profile_photo = $filename;
        }

        // Save the updated user data
        $user->save();

        return redirect()->route('profile')->with('success', 'Profile updated successfully!');
    }


}

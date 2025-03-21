<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    // Method to create groups based on the number of groups needed
    public function createGroups($groupCount)
    {
        $groups = collect();
        
        // Create the required number of groups
        for ($i = 1; $i <= $groupCount; $i++) {
            $group = Group::create([
                'name' => 'Group ' . $i,
            ]);
            $groups->push($group); // Add the new group to the collection
        }

        return $groups;
    }
}

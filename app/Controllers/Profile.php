<?php
namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\Shield\Authorization\Groups;

class Profile extends BaseController
{
    public function index()
    {
        $user = auth()->user();
        $name = $user->username ?? $user->email;

        if ($user && $user->inGroup('admin')) {
            echo "Welcome, admin " . $user->username;
        } else {
            echo "Not an admin or not logged in.";
            // return redirect()->to('/')->with('error', 'Access denied');
        }

        // if (!auth()->inGroup('admin') && !auth()->inGroup('superadmin')) {
        //     // dd(auth()->user()->inGroup('admin'));
        //     // dd(auth()->user());
        //     // dd(auth()->user()->getGroups());

        //     echo $name;
        //     echo 'Access Denied';
        //     exit;
        //     return redirect()->to('/')->with('error', 'Access denied');
        // }



        return view('profile', ['name' => $name]);
    }
}
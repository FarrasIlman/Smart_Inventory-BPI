<?php

namespace App\Http\Controllers;

use App\Models\CMSLogs;
use App\Models\HeroContent;
use Illuminate\Http\Request;

class CMSController extends Controller
{
    public function cmslog()
    {

        $logs = CMSLogs::orderBy('created_at', 'desc')->get();

        return view('cms.cmshomepage', compact('logs'));
    }

    public function blank()
    {
        // $log = new CMSLogs();
        $log_id = null;
        $heroes = collect();

        return view('cms.detailhomepage', compact('log_id', 'heroes'));
    }

    public function edit($id)
    {
        $log = CMSLogs::findOrFail($id);
        $log_id = $log->id;
        $heroes = HeroContent::where('log_id', $id)->get();

        return view('cms.detailhomepage', compact('log_id', 'heroes'));
    }

    // public function create(Request $request)
    // {
    //     $request->validate([

    //     ]);
    // }
}

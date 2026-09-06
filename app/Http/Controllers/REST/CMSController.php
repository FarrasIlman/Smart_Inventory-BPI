<?php

namespace App\Http\Controllers\REST;

use App\Http\Controllers\Controller;
use App\Models\AboutUs;
use App\Models\CMSLogs;
use App\Models\HeroContent;
use App\Models\Services;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CMSController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $log = CMSLogs::where('status', '=', 'approved')->first(); 

            if (!$log) {
                return response()->json([
                    'status' => 'error', 
                    'code' => 404, 
                    'message' => 'data not found'
                ]);
            }

            $log_id = $log->id;
            $heroes = HeroContent::with(['image', 'imageMobile'])->where('log_id', $log_id)->get();
            $aboutUs = AboutUs::with('image')->where('log_id', $log_id)->first();
            $service = Services::with('details.image')->where('log_id', $log_id)->first();

            if ($aboutUs->bg_config && is_string($aboutUs->bg_config)) {
                $aboutUs->bg_config = json_decode($aboutUs->bg_config, true);
            }

            if ($aboutUs->metrics && is_string($aboutUs->metrics)) {
                $aboutUs->metrics = json_decode($aboutUs->metrics, true);
            }

            if ($service->bg_config && is_string($service->bg_config)) {
                $service->bg_config = json_decode($service->bg_config, true);
            }

            $data = [
                'heroes' => $heroes,
                'about_us' => $aboutUs, 
                'service' => $service
            ];

            Log::info('api data : ', [$data]);

            return response()->json([
                'status' => 'success', 
                'data' => $data, 
                'message' => 'Data successfully loaded'
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error', 
                'code' => 500,
                'message' => 'internal server error'
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
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

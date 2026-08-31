<?php

namespace App\Http\Controllers;

use App\Models\AboutUs;
use App\Models\CMSLogs;
use App\Models\HeroContent;
use App\Models\Images;
use App\Models\Services;
use App\Models\ServiceDetail;
use Cloudinary\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CMSController extends Controller
{
    public function cmslog()
    {

        $logs = CMSLogs::orderBy('created_at', 'desc')->get();

        return view('cms.cmshomepage', compact('logs'));
    }

    public function blank()
    {
        $log_id = null;
        $heroes = collect();
        $abouts = new AboutUs;
        $service = new Services;

        return view('cms.detailhomepage', compact('log_id', 'heroes', 'abouts', 'service'));
    }

    public function edit($id)
    {
        $log = CMSLogs::findOrFail($id);
        $log_id = $log->id;
        $heroes = HeroContent::where('log_id', $id)->get();

        return view('cms.detailhomepage', compact('log_id', 'heroes'));
    }

    public function create(Request $request)
    {

        return DB::transaction(function () use ($request) {

            $log = CMSLogs::create([
                'created_by' => Auth::user()?->username,
                'status' => 'WAITING_REVIEW',
                'notes' => $request->notes,
            ]);

            $cloudinary = new Cloudinary(env('CLOUDINARY_URL'));

            // hero start

            if ($request->has('heroes')) {
                foreach ($request->heroes as $index => $heroData) {
                    $desktopId = null;
                    $mobileId = null;

                    if ($request->hasFile('heroes.'.$index.'.image')) {
                        $file = $request->file('heroes.'.$index.'.image');

                        $fileName = $file->getClientOriginalName();
                        $fileSize = $file->getSize();

                        $uploadDesktop = $cloudinary->uploadApi()->upload(
                            $file->getRealPath(),
                            ['folder' => 'hero_banners/desktop']
                        );

                        $url = $uploadDesktop['secure_url'];
                        $public_id = $uploadDesktop['public_id'];

                        $saveImage = Images::create([
                            'image_url' => $url,
                            'image_public_id' => $public_id,
                            'file_name' => $fileName,
                            'image_size' => $fileSize,
                        ]);

                        $desktopId = $saveImage->id;
                    }

                    if ($request->hasFile('heroes.'.$index.'.image_mobile')) {
                        $file = $request->file('heroes.'.$index.'.image_mobile');

                        $fileName = $file->getClientOriginalName();
                        $fileSize = $file->getSize();

                        $uploadMobile = $cloudinary->uploadApi()->upload(
                            $file->getRealPath(),
                            ['folder' => 'hero_banners/mobile']
                        );

                        $url = $uploadMobile['secure_url'];
                        $public_id = $uploadMobile['public_id'];

                        $saveImageMobile = Images::create([
                            'image_url' => $url,
                            'image_public_id' => $public_id,
                            'file_name' => $fileName,
                            'image_size' => $fileSize,
                        ]);

                        $mobileId = $saveImageMobile->id;
                    }

                    HeroContent::create([
                        'log_id' => $log->id,
                        'title' => $heroData['title'],
                        'subtitle' => $heroData['subtitle'],
                        'image_id' => $desktopId,
                        'image_mobile_id' => $mobileId,
                    ]);
                }
            }

            // hero end

            // about us start

            if ($request->has('about')) {
                $aboutImageId = null;

                if ($request->hasFile('about.image')) {
                    $file = $request->file('about.image');

                    $aboutFileName = $file->getClientOriginalName();
                    $aboutFileSize = $file->getSize();

                    $uploadAbout = $cloudinary->uploadApi()->upload(
                        $file->getRealPath(), 
                        ['folder' => 'aboutus']
                    ); 

                    $aboutImageUrl = $uploadAbout['secure_url'];
                    $aboutImagePublicId = $uploadAbout['public_id'];

                    $saveAboutImage = Images::create([
                        'image_url' => $aboutImageUrl, 
                        'image_public_id' => $aboutImagePublicId, 
                        'file_name' => $aboutFileName, 
                        'image_size' => $aboutFileSize 
                    ]);

                    $aboutImageId = $saveAboutImage->id;
                }

                $aboutData = $request->about;

                AboutUs::create([
                    'log_id' => $log->id, 
                    'title' => $aboutData['title'], 
                    'description' => $aboutData['description'], 
                    'bg_config' => $aboutData['bg_config'], 
                    'metrics' => $aboutData['metrics'], 
                    'image_id' => $aboutImageId
                ]);
            }

            // about us end

            // service start

            if ($request->has('service')) {
                $serviceData = $request->service; 

                $saveService = Services::create([
                    'log_id' => $log->id, 
                    'title' => $serviceData['title'],
                    'description' => $serviceData['description'],
                    'bg_config' => $serviceData['bg_config']
                ]);

                if (isset($serviceData['details'])) {
                    foreach ($serviceData['details'] as $index => $detail) {

                        $imageId = null;
                        
                        if ($request->hasFile('service.details.' . $index . '.image')) {
                            $file = $request->file('service.details.' .$index. '.image');

                            $fileName = $file->getClientOriginalName() . '.' . $file->getClientOriginalExtension();
                            $fileSize = $file->getSize();

                            $uploadService = $cloudinary->uploadApi()->upload(
                                $file->getRealPath(), 
                                ['folder' => 'service']
                            );

                            $serviceImgUrl = $uploadService['secure_url'];
                            $servicePublicId = $uploadService['public_id'];

                            $saveServiceImg = Images::create([
                                'image_url' => $serviceImgUrl, 
                                'image_public_id' => $servicePublicId, 
                                'file_name' => $fileName, 
                                'image_size' => $fileSize
                            ]);

                            $imageId = $saveServiceImg->id;
                        }

                        ServiceDetail::create([
                            'service_id' => $saveService->id, 
                            'name' => $detail['name'], 
                            'description' => $detail['description'], 
                            'image_id' => $imageId
                        ]);
                    }
                }
            }

            // service end

           

            return redirect()->route('cms.log')->with('success', 'Banner homepage berhasil dibuat');
        });

    }
}

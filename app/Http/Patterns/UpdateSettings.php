<?php

namespace App\Http\Patterns;


use App\Models\Logs;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;


class UpdateSettings implements IOperations
{
    public  function doOperation(array $data)
    {
        $setting = Setting::where('name', '=', $data['name'])->first();

        $settings = [
            'point_price', 
            'home.slides', 
            'home.page.status', 
            'home.page.cta', 
            'home.page.textSectionOne', 
            'home.page.textSectionTwo', 
            'home.page.video',
            'home.page.videoText',
        ];
        if (
            !$setting &&
            (array_search($data['name'], $settings) !== false ||
                str_contains($data['name'], $settings[1])
            )
        ) {
            if (gettype($data['value']) === 'array' && isset($data['value']['image_file'])) {
                $img = $data['value']['image_file'];
                unset($data['value']['image_file']);
            }
            if (isset($data['id'])) unset($data['id']);
            
            if ($data['options']['type'] === 'slide') {
                $data['value'] = json_encode($data['value']);
                 $folder = 'home_slides';
                $setting = Setting::create($data);
            } else if ($data['options']['type'] === 'video') {
                $vid = $data['value']['vfile'] ?? '';
                $data['value'] = '';
                $setting = Setting::create($data);
                if ($vid) {
                    $setting->addMedia($vid)->toMediaCollection('video', 'settings_files');
                    $setting->refresh();
                }
            } else {
                if ($data['options']['type'] === 'json') {
                    $data['value'] = json_encode($data['value']);
                }
                $setting = Setting::create($data);
                 $folder = 'settings_files';
            }


            if(isset($img)) {
                $setting->addMedia($img)->toMediaCollection('image', $folder);
                $setting->refresh();
            }
        } else if ($setting) {
            if ($data['value']) {

                if (gettype($data['value']) === 'array' && isset($data['value']['image_file'])) {
                    $img = $data['value']['image_file'];
                    unset($data['value']['image_file']);
                }
                if ($setting->options['type'] === 'slide') {
                    $data['value'] = json_encode($data['value']);
                    $setting->update($data);
                    $folder = 'home_slides';
                    
                } else if ($data['options']['type'] === 'video') {
                    $vid = $data['value']['vfile'] ?? '';
                    $data['value'] = '';
                    if ($vid) {
                        $vids = $setting->getMedia('video');
                    
                        if ($vids->count() > 0) {
                            $setting->deleteMedia($setting->getMedia('video')[0]);
                        }
                        $setting->addMedia($vid)->toMediaCollection('video', 'settings_files');
                        $setting->refresh();
                    }
                } else {
                    if ($data['options']['type'] === 'json') {
                        // $oldData = $setting->value;
                        // foreach ($oldData as $key => $value) {
                        //     if (isset($data['value'][$key])) {
                        //         $oldData[$key] = $value;
                        //     }
                        // }
                        $data['value'] = json_encode($data['value']);
                        // $setting->save();
                    } else {
                        $setting->update($data);
                    }
                    $setting->update($data);
                    $folder = 'settings_files';
                }
                
                if (isset($img)) {
                    $imgs = $setting->getMedia('image');
                    if ($imgs->count() > 0) {
                        $setting->deleteMedia($setting->getMedia('image')[0]);
                    }
                    
                    $setting->addMedia($img)->toMediaCollection('image', $folder);
                    $setting->refresh();
                }
            }
            
        }


        return $setting;
    }

    public function audit()
    {
        Logs::createNewRecord('Update', 'settings');
    }

    public function getErrorMessage()
    {
        Log::info('ERROR IN Update settings IN settings CONTROLLER');
    }

    public function returnPage()
    {
        return redirect()->back();
    }

    protected function fillData($array) {
        
    }
}

<?php

namespace App\Services;

use App\Http\Data\AddBrandPageData;
use App\Http\Patterns\AddBrand;
use App\Models\HomeSection;
use App\Http\Patterns\AddBrandDiscount;
use App\Http\Patterns\AddBrandPage;
use App\Http\Patterns\DeleteBrand;
use App\Http\Patterns\UpdateBrand;
use App\Http\Patterns\UpdateSettings;
use App\Models\BrandModel;
use App\Models\Setting;
use Illuminate\Database\Eloquent\Collection;
use Lunar\Models\Brand;


class SettingService
{

    protected Setting $setting;

    public function __construct(Setting $setting)
    {
        $this->setting = $setting;
    }

    public function getHomeTabs()
        {
            // Settings tabs
            $settingsTabs = $this->getHomeSettings()
                ->map(function ($item) {
                    return [
                        'type'  => 'setting',
                        'order' => $item->order ?? 0,
                        'active'=>$item->active,
                        'data'  => $item,
                    ];
                });
    
            // Sections tabs
            $sectionsTabs = HomeSection::with('items')
                ->get()
                ->map(function ($item) {
                    return [
                        'type'  => 'section',
                        'order' => $item->order ?? 0,
                        'data'  => $item,
                        'active'=>$item->active,
                    ];
                });
    
            return $settingsTabs
                ->merge($sectionsTabs)
                ->sortBy('order')
                ->values();
        }

    public function getAllSetings(): Collection
    {
        return $this->setting::whereNot('name', 'like', 'home.%')->get();
    }
    public function getHomeSettings(): Collection
    {
        return $this->setting::where('name', 'like', 'home.page.%')->get();
    }

    public function getByName($name)
    {
        return $this->setting::where('name', $name)->first();
    }

    public function updateSetting($data)
    {
        $setting = new UpdateSettings();

        return $setting->doOperation($data);
    }

    public function updateSettings($settings) {
        $s = [];
        
        foreach ($settings->data as $setting) {
            $ss = $this->updateSetting($setting);
            if ($ss) {
                array_push($s, $ss);
            }
        }
        
        return $s;
    }
    public function updateSlidesSettings($slides, $new=true) {
        $s = [];
        $count = $this->setting->where('name', 'like', 'home.slides.%')->count();
        
        foreach ($slides->slides as $key=>$setting) {
            $num = time()+(int)$key;
            $set = [];
            
            if ($new) {
                $set['name'] = "{$slides->name}.{$num}";
                $set['options'] = [
                    'type' => 'slide'
                ];
            } else {
                $set['name'] = $setting['name']??null;
                unset($setting['name']);
            }
            $set['value'] = $setting;
            $ss = $this->updateSetting($set);
            if ($ss) {
                array_push($s, $ss);
            }
        }
        
        return $s;
    }

    public function getHomeSlides() {
        $locale = app()->getLocale() ?? 'en';
        return $this->setting::where('name', 'like', 'home.slides.%')
        ->get();
    }

    public function updateHomeSettings($settings) {
        $s = [];
        foreach ($settings as $key=>$setting) {
            $set = [];
            $set['name'] = "home.page.$key";
           if (isset($setting['video']['vfile']) && $setting['video']['vfile'] instanceof \Illuminate\Http\UploadedFile) {
                $uploadedFile = $setting['video']['vfile'];
                $path = $uploadedFile->store('videos', 'public'); // يحفظ الفيديو في storage/app/public/videos
                $setting['video']['vfile'] = 'storage/' . $path;
    }



           $set['value'] = $setting;
            $type = 'text';
            if ($key === 'status' || $key === 'cta' || $key === 'textSectionOne' || $key === 'textSectionTwo' || $key === 'videoText') {
                $type = 'json';
            } else if ($key === 'video') {
                $type = 'video';
            }
            $set['options'] = [
                'type' => $type
            ];
             
            $ss = $this->updateSetting($set);
            if ($ss) {
                array_push($s, $ss);
            }
        }
       
        return $s;
    }
}

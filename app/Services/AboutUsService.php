<?php

namespace App\Services;

use App\Http\Patterns\AddAboutUs;
use App\Http\Patterns\AddAboutUsDiscount;
use App\Http\Patterns\DeleteAboutUs;
use App\Http\Patterns\UpdateAboutUs;
use App\Models\AboutUs;
use App\Models\AboutUsModelTranslation;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\App;



class AboutUsService
{

    protected AboutUs $model;

    public function __construct(AboutUs $model)
    {
        $this->model = $model;
    }

public function getAllAboutUss(): Collection
    {
   
    $locale = request()->header('locale'); // هنا نقرأ قيمة الهيدر
    
    if ($locale) {
        return AboutUsModelTranslation::where("locale", $locale)->get();
    } else {
        
        return  AboutUsModelTranslation::orderBy('about_us_id')->get();

      }
    }


   
    public function createAboutUs( $data)
    {
        $brand = new AddAboutUs();

        return $brand->doOperation($data->toArray());
    }

    public function getById($id)
    {
        return  $this->model->getAboutUsById($id);
    }

    public function updateAboutUs($id, $data)
    {
        $about = new UpdateAboutUs();
        
       
        return $about->doOperation(['id'=>$id,'data'=>$data->toArray()]);
    }

    public function deleteAboutUs($id)
    {
        $brand = new DeleteAboutUs();

        return $brand->doOperation(['id'=>$id]);
    }

}

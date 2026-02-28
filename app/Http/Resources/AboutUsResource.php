<?php


namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Partner;


class AboutUsResource extends JsonResource
{
    public function toArray($request)
    {
        
        $data   = [
          'id'=> $this->id ?? "",
          'about_us_id'=> $this->about_us_id ?? '',
          'title' => $this->title ?? "",
          'description' => $this->description ?? "",
          'image_url' => $this->image_url ?? "",
          'section' => __('translations.sections.' . $this->section) ?? ""
        ];
        if ($this->section === 'partners') {
            $data['partners'] = Partner::where('is_active', true)
                ->orderBy('order_index')
                ->get([
                    'id',
                    'name',
                    'logo_url',
                    'website_url',
                ]);
        }
        
        if ($this->withTranslation) {
            $data['translations'] = $this->translations;
        }

        return $data;

    }
}

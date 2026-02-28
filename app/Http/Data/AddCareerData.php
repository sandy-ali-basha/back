<?php


namespace App\Http\Data;

use Spatie\LaravelData\Data;

class AddCareerData extends Data
{
    public string $requisition_no;
    public string|NULL $time_type = NULL;
    public int $category_id;
    public array    $ar;
    public array    $kr;
    public array    $en;

    public function __construct(
                                string $requisition_no,
                                string|NULL $time_type,
                                int $category_id,
                                array $ar,
                                array $kr,
                                array $en
    )
    {
        $this->requisition_no = $requisition_no;
        $this->time_type      = $time_type;
        $this->category_id    = $category_id;
        $this->ar           = $ar;
        $this->kr           = $kr;
        $this->en           = $en;
    }


    public static function rules(): array
    {
        return [
            'requisition_no' => 'required|string|max:255',
            'time_type' => 'string|max:255',
            'location' => 'string|max:255',
            'country' => 'string|max:255',
            'description' => 'string',
            'about_us' => 'string',
            'category_id' => 'exists:career_categories,id',
            'ar.vacancy_name' => 'required',
            'kr.vacancy_name' => 'required',
            'en.vacancy_name' => 'required',
        ];
    }
}

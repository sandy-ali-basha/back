<?php
/**
 * dawaa - ${FILE_NAME}
 *
 * Date: 24/05/06
 * Time: 6:49 PM
 * @author    Feras Alshaher <feras@lamsaworld.com>
 * @copyright Copyright (c) 2024 XYZ
 */

namespace App\Services;

use App\Http\Data\AddCareerCategoryData;
use App\Http\Data\UpdateCareerCategoryData;
use App\Http\Patterns\AddCareerCategory;
use App\Http\Patterns\DeleteCareerCategory;
use App\Http\Patterns\UpdateCareerCategory;
use App\Models\CareerCategory;

class CareerCategoryService
{
    protected CareerCategory $careerCategory;

    /**
     * @param CareerCategory $careerCategory
     */
    public function __construct(CareerCategory $careerCategory)
    {
        $this->careerCategory = $careerCategory;
    }

    public function getAllCareersCategories()
    {
        return $this->careerCategory->get();
    }

    public function getCareerById(string $id)
    {
        return $this->careerCategory->getCareerCatById($id);
    }

    public function saveCareerCategory(AddCareerCategoryData $careerCategoryData)
    {
        $careerCategory = new AddCareerCategory();

        return $careerCategory->doOperation($careerCategoryData->toArray());
    }

    public function deleteCareerCategory(string $id)
    {
        $career = new DeleteCareerCategory();

        return $career->doOperation(['id'=>$id]);
    }

    public function updateCareerCategory(int $id,UpdateCareerCategoryData $careerCategoryData)
    {
        $careerCategory = new UpdateCareerCategory();

        return $careerCategory->doOperation(['id'=>$id, 'data'=>$careerCategoryData->toArray()]);
    }
}

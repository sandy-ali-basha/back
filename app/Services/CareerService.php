<?php
/**
 * dawaa - CareerService.php
 *
 * Date: 24/05/06
 * Time: 4:56 PM
 * @author    Feras Alshaher <feras@lamsaworld.com>
 * @copyright Copyright (c) 2024 LamsaWorld (http://www.lamsaworld.com/)
 */

namespace App\Services;

use App\Http\Data\AddCareerData;
use App\Http\Data\UpdateCareerData;
use App\Http\Patterns\AddCareer;
use App\Http\Patterns\DeleteCareer;
use App\Http\Patterns\UpdateCareer;
use App\Models\Career;
use Illuminate\Support\Facades\App;

class CareerService
{
    protected Career $career;

    /**
     * @param Career $career
     */
    public function __construct(Career $career)
    {
        $this->career = $career;
    }

    public function getAllCareers()
    {
        $careers = $this->career->with('category')->get();
        return $careers;
    }

    public function getCareerById(string $id)
    {
        return $this->career->getCareerById($id);
    }

    public function saveCareer(AddCareerData $careerData)
    {
        $career = new AddCareer();

        return $career->doOperation($careerData->toArray());
    }

    public function deleteCareer(string $id)
    {
        $career = new DeleteCareer();

        return $career->doOperation(['id' => $id]);
    }

    public function updateCareer(string $id, UpdateCareerData $careerData)
    {

        $careerCategory = new UpdateCareer();

        return $careerCategory->doOperation(['id' => $id, 'data' => $careerData->toArray()]);
    }

}

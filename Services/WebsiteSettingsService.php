<?php


namespace App\Services;

use App\Http\Data\AddTermsData;
use App\Http\Data\UpdateTermsData;
use App\Http\Patterns\AddTerms;
use App\Http\Patterns\UpdateTerms;
use App\Models\TermsAndConditions;

class WebsiteSettingsService
{
    protected TermsAndConditions $termsAndConditions;

    public function __construct(TermsAndConditions $termsAndConditions)
    {
        $this->termsAndConditions = $termsAndConditions;
    }

    public function getTerms($id)
    {
        return $this->termsAndConditions->getTermById($id);
    }

    public function updateTerms(UpdateTermsData $termsData, $id)
    {
        $careerCategory = new UpdateTerms();

        return $careerCategory->doOperation(['id' => $id, 'data' => $termsData->toArray()]);
    }

    public function createTerms(AddTermsData $termsData)
    {
        $careerCategory = new AddTerms();
        return $careerCategory->doOperation($termsData->toArray());
    }

    public function getAllTerms()
    {
        return $this->termsAndConditions->all();
    }

    public function deleteTerm(int $id)
    {
        $term = TermsAndConditions::find($id);
        $term->deleteTranslations();
        return $term->delete();
    }
}

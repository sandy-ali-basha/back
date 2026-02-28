<?php
/**
 * Dawaa - RegisterData.php
 *
 * Date: 24/04/28
 * Time: 7:58 PM
 * @author    Feras Alshaher <feras@lamsaworld.com>
 * @copyright Copyright (c) 2024 LamsaWorld (http://www.lamsaworld.com/)
 */

namespace App\Http\Data;

use Illuminate\Validation\Rule;
use Spatie\LaravelData\Data;
use Illuminate\Support\Str;

class UpdateHomeSettingsData extends Data
{
    public null|array $status;
    public null|array $cta;
    public null|array $textSectionOne;
    public null|array $textSectionTwo;
    public null|array $video;
    public null|array $videoText;

    public function __construct(
        null|array $status,
        null|array $cta,
        null|array $textSectionOne,
        null|array $textSectionTwo,
        null|array $video,
        null|array $videoText
    )
    {
        $this->status = $status;
        $this->cta = $cta;
        $this->textSectionOne = $textSectionOne;
        $this->textSectionTwo= $textSectionTwo;
        $this->video = $video;
        $this->videoText = $videoText;
    }
    public static function rules(): array
    {
        return [
            'status' => 'nullable|array',
            'status.ar.title1' => 'nullable|string|max:255',
            'status.kr.title1' => 'nullable|string|max:255',
            'status.en.title1' => 'nullable|string|max:255',
            'status.ar.subtitle1' => 'nullable|string|max:255',
            'status.kr.subtitle1' => 'nullable|string|max:255',
            'status.en.subtitle1' => 'nullable|string|max:255',
            'status.ar.title2' => 'nullable|string|max:255',
            'status.kr.title2' => 'nullable|string|max:255',
            'status.en.title2' => 'nullable|string|max:255',
            'status.ar.subtitle2' => 'nullable|string|max:255',
            'status.en.subtitle2' => 'nullable|string|max:255',
            'status.kr.subtitle2' => 'nullable|string|max:255',
            'status.info' => 'nullable|array',
            'status.info.*.number' => 'nullable|string',
            'status.info.*.ar.text' => 'nullable|string',
            'status.info.*.en.text' => 'nullable|string',
            'status.info.*.kr.text' => 'nullable|string',

            'cta' => 'nullable|array',
            'cta.title' => 'nullable|array',
            'cta.title.*' => 'nullable|string|max:255',
            'cta.subtitle' => 'nullable|array',
            'cta.subtitle.*' => 'nullable|string|max:255',
            'cta.link' => 'nullable|url',

            'textSectionOne' => 'nullable|array',
            'textSectionOne.text' => 'nullable|array',
            'textSectionOne.text.*' => 'nullable|string',
            'textSectionOne.image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp',
            
            'textSectionTwo' => 'nullable|array',
            'textSectionTwo.text' => 'nullable|array',
            'textSectionTwo.text.*' => 'nullable|string',
            'textSectionTwo.image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp',

            'video' => 'nullable|array',
            'video.ar.vfile' => 'nullable|file|mimes:mp4,avi,mov,wmv|max:10240',
            'video.en.vfile' => 'nullable|file|mimes:mp4,avi,mov,wmv|max:10240',
            'video.kr.vfile' => 'nullable|file|mimes:mp4,avi,mov,wmv|max:10240',
            'videoText' => 'nullable|array',
            'videoText.*' => 'nullable|string',
        ];
    }
}

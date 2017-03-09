<?php
/**
 * Created by PhpStorm.
 * User: tiago
 * Date: 08/03/17
 * Time: 22:31
 */

namespace App\Http\Helpers;


use App\Http\Models\Country;
use App\Http\Models\CountryBorder;
use App\Http\Models\Language;
use App\Http\Models\Translation;

class DBProcessor
{
    public static function process($data)
    {
        foreach ($data as $country) {
            $languageIds = Language::processLanguages($country->languages);

            $translationIds = Translation::processTranslations($country->translations);

            $countryBorderIds = CountryBorder::processCountryBorders($country->borders);

            $countryId = Country::processCountry($country);

            if (count($languageIds)) {
                Country::processLanguages($countryId, $languageIds);
            }

            if (count($translationIds)) {
                Country::processTranslations($countryId, $translationIds);
            }

            if (count($countryBorderIds)) {
                Country::processBorders($countryId, $countryBorderIds);
            }
        }

        return ['success' => true];
    }
}
<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Country extends Model
{
    public static function processCountry($country)
    {
        $countryId = null;

        $countryExists = self::countryExists($country);

        if (!$countryExists) {
            $countryId = self::insertCountry($country);
        } else {
            $countryId = self::getCountryId($country);
        }

        return $countryId;
    }

    private static function countryExists($country)
    {
        $sql = "
            SELECT 1
              FROM SamKnows.countries 
             WHERE name = :country_name
        ";

        $result = DB::selectOne($sql, [
            'country_name' => $country->name
        ]);

        return ($result == null ? false : true);
    }

    private static function insertCountry($country)
    {
        return DB::table('SamKnows.countries')
            ->insertGetId([
                'name'             => $country->name,
                'top_level_domain' => $country->topLevelDomain[0],
                'iso2_code'        => $country->alpha2Code,
                'iso3_code'        => $country->alpha3Code,
                'latitude'         => (isset($country->latlng[0]) ? $country->latlng[0] : null),
                'longitude'        => (isset($country->latlng[1]) ? $country->latlng[1] : null)
            ]);
    }

    private static function getCountryId($country)
    {
        return DB::table('SamKnows.countries')
            ->select('id')
            ->where('name', '=', $country->name)
            ->get()
            ->first()
            ->id;
    }

    public static function processLanguages($countryId, $languageIds)
    {
        foreach ($languageIds as $languageId) {
            $countryLanguageExists = self::countryLanguageExists($countryId, $languageId);

            if (!$countryLanguageExists) {
                DB::table('SamKnows.countries_languages')
                    ->insert([
                        'country_id'  => $countryId,
                        'language_id' => $languageId
                    ]);
            }
        }
    }

    private static function countryLanguageExists($countryId, $languageId)
    {
        $sql = "
            SELECT 1
              FROM SamKnows.countries_languages 
             WHERE country_id = :country_id 
               AND language_id = :language_id
        ";

        $result = DB::selectOne($sql, [
            'country_id'  => $countryId,
            'language_id' => $languageId
        ]);

        return ($result == null ? false : true);
    }

    public static function processTranslations($countryId, $translationIds)
    {
        foreach ($translationIds as $translationId) {
            $countryLanguageExists = self::countryTranslationExists($countryId, $translationId);

            if (!$countryLanguageExists) {
                DB::table('SamKnows.countries_translations')
                    ->insert([
                        'country_id'     => $countryId,
                        'translation_id' => $translationId
                    ]);
            }
        }
    }

    private static function countryTranslationExists($countryId, $translationId)
    {
        $sql = "
            SELECT 1
              FROM SamKnows.countries_translations
             WHERE country_id = :country_id 
               AND translation_id = :translation_id
        ";

        $result = DB::selectOne($sql, [
            'country_id'     => $countryId,
            'translation_id' => $translationId
        ]);

        return ($result == null ? false : true);
    }

    public static function processBorders($countryId, $countryBorderIds)
    {
        foreach ($countryBorderIds as $borderId) {
            $countryBorderExists = self::countryBorderExists($countryId, $borderId);

            if (!$countryBorderExists) {
                DB::table('SamKnows.countries_borders')
                    ->insert([
                        'country_id' => $countryId,
                        'border_id'  => $borderId
                    ]);
            }
        }
    }

    private static function countryBorderExists($countryId, $borderId)
    {
        $sql = "
            SELECT 1
              FROM SamKnows.countries_borders
             WHERE country_id = :country_id 
               AND border_id = :border_id
        ";

        $result = DB::selectOne($sql, [
            'country_id' => $countryId,
            'border_id'  => $borderId
        ]);

        return ($result == null ? false : true);
    }

    public static function getAll()
    {
        $final = [];

        $countries = collect(Country::all()->toArray());

        foreach ($countries as $id => $country) {
            $countryId = $country['id'];

            $borders = CountryBorder::getBordersByCountryId($countryId);

            $languages = Language::getLanguagesByCountryId($countryId);

            $translations = Translation::getTranslationsByCountryId($countryId);

            $final[$id] = $country;
            $final[$id]['borders'] = $borders;
            $final[$id]['languages'] = $languages;
            $final[$id]['translations'] = $translations;
        }

        return $final;
    }

    public static function getCountryByISO2Code($iso2Code)
    {
        $country = Country::where('iso2_code', $iso2Code)->first()->toArray();

        $country['borders'] = CountryBorder::getBordersByCountryId($country['id']);
        $country['languages'] = Language::getLanguagesByCountryId($country['id']);
        $country['translations'] = Translation::getTranslationsByCountryId($country['id']);

        return $country;
    }

    public static function getCountriesByLanguage($language)
    {
        $sql = "
            SELECT countries.* 
              FROM SamKnows.countries AS countries 
              JOIN SamKnows.countries_languages AS cl ON cl.country_id = countries.id 
              JOIN SamKnows.languages AS languages ON languages.id = cl.language_id 
             WHERE languages.name = :language_name
        ";

        $result = DB::select($sql, ['language_name' => $language]);

        $final = [];

        foreach ($result as $id => $country) {
            $country = collect($country);

            $borders = CountryBorder::getBordersByCountryId($country['id']);
            $languages = Language::getLanguagesByCountryId($country['id']);
            $translations = Translation::getTranslationsByCountryId($country['id']);

            $final[$id] = $country->toArray();
            $final[$id]['borders'] = $borders;
            $final[$id]['languages'] = $languages;
            $final[$id]['translations'] = $translations;
        }

        return $final;
    }
}

<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Language extends Model
{
    public static function processLanguages($languages)
    {
        $languageIds = [];

        foreach ($languages as $language) {
            $languageExists = self::languageExists($language);

            if (!$languageExists) {
                $languageIds[] = self::insertLanguage($language);
            } else {
                $languageIds[] = self::getLanguageId($language);
            }
        }

        return $languageIds;
    }

    private static function languageExists($language)
    {
        $sql = "
            SELECT 1
              FROM SamKnows.languages 
             WHERE name = :lang_name
        ";

        $result = DB::selectOne($sql, ['lang_name' => $language]);

        return ($result == null ? false : true);
    }

    private static function insertLanguage($language)
    {
        return DB::table('SamKnows.languages')
            ->insertGetId(['name' => $language]);
    }

    private static function getLanguageId($language)
    {
        return DB::table('SamKnows.languages')
            ->select('id')
            ->where('name', '=', $language)
            ->get()
            ->first()
            ->id;
    }

    public static function getLanguagesByCountryId($id)
    {
        $result = DB::table('SamKnows.languages AS languages')
            ->select('languages.name')
            ->join('SamKnows.countries_languages AS countries_languages', 'languages.id', '=',
                'countries_languages.language_id')
            ->where('countries_languages.country_id', '=', $id)
            ->get();

        $final = [];

        foreach ($result as $language) {
            $final[] = $language->name;
        }

        return $final;
    }
}

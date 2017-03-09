<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Translation extends Model
{
    public static function processTranslations($translations)
    {
        $translationIds = [];

        foreach ($translations as $translationCode => $translationText) {
            // Skip if translation doesn't exist
            if ($translationText === null) {
                continue;
            }

            $translationExists = self::translationExists($translationCode, $translationText);

            if (!$translationExists) {
                $translationIds[] = self::insertTranslation($translationCode, $translationText);
            } else {
                $translationIds[] = self::getTranslationId($translationCode, $translationText);
            }
        }

        return $translationIds;
    }

    private static function translationExists($translationCode, $translationText)
    {
        $sql = "
            SELECT 1
              FROM SamKnows.translations 
             WHERE translation_code = :trans_code 
               AND translation_text = :trans_text
        ";

        $result = DB::selectOne($sql, [
            'trans_code' => $translationCode,
            'trans_text' => $translationText
        ]);

        return ($result == null ? false : true);
    }

    private static function insertTranslation($translationCode, $translationText)
    {
        return DB::table('SamKnows.translations')
            ->insertGetId([
                'translation_code' => $translationCode,
                'translation_text' => $translationText
            ]);
    }

    private static function getTranslationId($translationCode, $translationText)
    {
        return DB::table('SamKnows.translations')
            ->select('id')
            ->where([
                ['translation_code', '=', $translationCode],
                ['translation_text', '=', $translationText]
            ])
            ->get()
            ->first()
            ->id;
    }

    public static function getTranslationsByCountryId($id)
    {
        $result = DB::table('SamKnows.translations AS translations')
            ->select('translations.translation_code', 'translations.translation_text')
            ->join('SamKnows.countries_translations AS countries_translations', 'translations.id', '=',
                'countries_translations.translation_id')
            ->where('countries_translations.country_id', '=', $id)
            ->get();

        $final = [];

        foreach ($result as $translation) {
            $final[$translation->translation_code] = $translation->translation_text;
        }

        return $final;
    }
}

<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CountryBorder extends Model
{
    public static function processCountryBorders($borders)
    {
        $borderIds = [];

        foreach ($borders as $border) {
            $borderExists = self::borderExists($border);

            if (!$borderExists) {
                $borderIds[] = self::insertBorder($border);
            } else {
                $borderIds[] = self::getBorderId($border);
            }
        }

        return $borderIds;
    }

    private static function borderExists($border)
    {
        $sql = "
            SELECT 1
              FROM SamKnows.borders 
             WHERE border_code = :border_code
        ";

        $result = DB::selectOne($sql, [
            'border_code' => $border
        ]);

        return ($result == null ? false : true);
    }

    private static function insertBorder($border)
    {
        return DB::table('SamKnows.borders')
            ->insertGetId([
                'border_code' => $border
            ]);
    }

    private static function getBorderId($border)
    {
        return DB::table('SamKnows.borders')
            ->select('id')
            ->where('border_code', '=', $border)
            ->get()
            ->first()
            ->id;
    }

    public static function getBordersByCountryId($id)
    {
        $result = DB::table('SamKnows.borders AS borders')
            ->select('borders.border_code')
            ->join('SamKnows.countries_borders AS countries_borders', 'borders.id', '=', 'countries_borders.border_id')
            ->where('countries_borders.country_id', '=', $id)
            ->get();

        $final = [];

        foreach ($result as $border) {
            $final[] = $border->border_code;
        }

        return $final;
    }
}

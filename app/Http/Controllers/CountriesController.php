<?php

namespace App\Http\Controllers;

use App\Http\Helpers\DBProcessor;
use App\Http\Helpers\Fetch;
use App\Http\Models\Country;
use Illuminate\Http\Request;

class CountriesController extends Controller
{
    /**
     * Parses a list of countries and inserts it on the database.
     */
    public function parse()
    {
        $uri = 'https://restcountries.eu/rest/v1/all';

        $data = Fetch::fetchData($uri);

        return response()->json(DBProcessor::process($data));
    }

    public function all()
    {
        $countries = Country::getAll();

        return response()->json($countries);
    }

    public function getCountryByISO2Code(Request $request)
    {
        $country = Country::getCountryByISO2Code($request->iso2_code);

        return response()->json($country);
    }

    public function getCountriesByLanguage(Request $request)
    {
        $countries = Country::getCountriesByLanguage($request->language);

        return response()->json($countries);
    }
}

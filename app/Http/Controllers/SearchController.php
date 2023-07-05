<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activity;
use App\Models\User;
use App\Models\Region;
use App\Models\Guide;
use Dotenv\Validator;
use App\Models\SearchHistory;
use Illuminate\Support\Facades\Auth;


class SearchController extends Controller
{
    //for user
    public function autocompletesearch( $regionName){

        $user_id = Auth::guard('api')->user()->id;

        $region = Region::where("name", "LIKE", "%{$regionName}%")->first();

        if (!$region) {
            return response()->json([
                'success' => false,
                'message' => 'Region not found',
            ], 404);
        }

        $search = SearchHistory::create([
            'text_search' => $region->name,
            'region_id' => $region->id,
            'user_id' => $user_id,
        ]);

        $response = [
            'text_search' => $region->name,
            'region_id' => $region->id,
            'user_id' => $user_id,
        ];

        return response()->json([
            'success' => true,
            'data' => $response,
        ]);

    }
        //for guide
        public function autocomplete_search( $regionName){

            $guide_id = Auth::guard('guide-api')->user()->id;

            $region = Region::where("name", "LIKE", "%{$regionName}%")->first();

            if (!$region) {
                return response()->json([
                    'success' => false,
                    'message' => 'Region not found',
                ], 404);
            }

            $search = SearchHistory::create([
                'text_search' => $region->name,
                'region_id' => $region->id,
                'guide_id' => $guide_id,
            ]);

            $response = [
                'text_search' => $region->name,
                'region_id' => $region->id,
                'guide_id' => $guide_id,
            ];

            return response()->json([
                'success' => true,
                'data' => $response,
            ]);

        }

        //for user
        public function get_search_history(){

            $user_id = Auth::guard('api')->user()->id;

            $searchHistory = SearchHistory::select('id', 'text_search', 'region_id', 'user_id')
                ->where('user_id', $user_id)
                ->get();

            if (!$searchHistory) {
                return response()->json(['message' => 'Search history not found'], 404);
            }

            return response()->json([
                'data' => $searchHistory,

            ]);
        }
        // for guide
        public function get_search_history_guide(){

            $guide_id = Auth::guard('guide-api')->user()->id;

            $searchHistory = SearchHistory::select('id', 'text_search', 'region_id', 'guide_id')
                ->where('guide_id', $guide_id)
                ->get();

            if (!$searchHistory) {
                return response()->json(['message' => 'Search history not found'], 404);
            }

            return response()->json([
                'data' => $searchHistory,

            ]);
        }




}



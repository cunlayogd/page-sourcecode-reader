<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DOMDocument; use Validator;

class HtmlCodeSummaryController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page_url' => 'required|url',
        ]);

        // validate page_url is a valid URL
        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        }

        // read the page url content into a string
        $HTML = @file_get_contents($request->page_url);

        if($HTML === false) {
           return response()->json([
                'error' => true,
                'message' => "The provided URL seems to be unreachable. Check the URL and try again."
            ], 500);
        }

        // load HTML string into dom, this way we can have access to each elements in the HTML string
        try {
            $dom = new DOMDocument();
            $loaded = @$dom->loadHTML($HTML);
        } catch (Exception $e) {
             return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], 500);
        }

        $tagsArr = $dom->getElementsByTagName('*');
        $tagsWithCountArr = array();

        foreach($tagsArr as $key => $tag) {
            if($this->custom_array_search($tag->tagName, $tagsWithCountArr)) {
                $key = $this->custom_array_search($tag->tagName, $tagsWithCountArr);
                $tagsWithCountArr[$key]['count'] += 1;
            } else {
                $tagsWithCountArr[$key]['tag'] = $tag->tagName;
                $tagsWithCountArr[$key]['count'] = 1;
            }
        }

        return response()->json([
            'summary' => $tagsWithCountArr,
            'source_code' => htmlentities($HTML, ENT_QUOTES | ENT_HTML401 | ENT_SUBSTITUTE | ENT_DISALLOWED, 'UTF-8', true)
        ]);
    }

    /*
     * Params - search (needle), multidimensional array (haystack)
     * Checks to see if search value is present as a key in the multidimensiona array
     * return boolean
     */
    public function custom_array_search($needle,$haystack) {
        foreach($haystack as $key=>$value) {
           $current_key = $key;
           if($needle === $value OR (is_array($value) && $this->custom_array_search($needle,$value) !== false)) {
                return $current_key;
            }
        }
        return false;
    }
}

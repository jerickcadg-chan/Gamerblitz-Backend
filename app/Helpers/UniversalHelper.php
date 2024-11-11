<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Discount;

if (!function_exists('calc_discount')) {
    function calc_discount($price, $type, $nominal) {
        return $type == 'percentage'
            ? $price * ($nominal / 100)
            : $nominal;
    }
}

if (!function_exists('get_active_discount')) {
    function get_active_discount($price, $productId, $productItemId): array
    {
        $discounts = Discount::active()->where('code', '')->orWhere('code', null)->get();

        foreach ($discounts as $discount) {
            switch ($discount->product_type) {
                case Discount::ALL:
                    return [
                        'disc_id' => $discount->id,
                        'nominal' => calc_discount($price, $discount->disc_type, $discount->nominal)
                    ];
                break;

                case Discount::PRODUCT_TYPE:
                    $disc = DB::table('discount_product')
                    ->where('productable_id', $productId)
                    ->where('discount_id', $discount->id)
                    ->where('productable_type', 'App\Models\Product')
                    ->first();

                    if ($disc) {
                        return [
                            'disc_id' => $discount->id,
                            'nominal' => calc_discount($price, $discount->disc_type, $discount->nominal)
                        ];
                    }
                break;

                case Discount::PRODUCT_ITEM:
                    $disc = DB::table('discount_product')
                    ->where('productable_id', $productItemId)
                    ->where('discount_id', $discount->id)
                    ->where('productable_type', 'App\Models\ProductItem')
                    ->first();

                    if ($disc) {
                        return [
                            'disc_id' => $discount->id,
                            'nominal' => calc_discount($price, $discount->disc_type, $discount->nominal)
                        ];
                    }
                break;
            }
        }

        return [
            'disc_id' => null,
            'nominal' => 0
        ];
    }
}

if (!function_exists('convert_to_62')) {
    function convert_to_62($phone_number) {
        $hp = $phone_number;
        // kadang ada penulisan no hp 0811 239 345
        $phone_number = str_replace(" ","",$phone_number);
        // kadang ada penulisan no hp (0274) 778787
        $phone_number = str_replace("(","",$phone_number);
        // kadang ada penulisan no hp (0274) 778787
        $phone_number = str_replace(")","",$phone_number);
        // kadang ada penulisan no hp 0811.239.345
        $phone_number = str_replace(".","",$phone_number);
        // kadang ada penulisan no hp +62857 121231
        $phone_number = str_replace("+","",$phone_number);
        // kadang ada penulisan no hp +0812-1212-1212
        $phone_number = str_replace("-","",$phone_number);

        // cek apakah no hp mengandung karakter + dan 0-9
        if(!preg_match('/[^+0-9]/', trim($phone_number))){
            // cek apakah no hp karakter 1-3 adalah 62
            if(substr(trim($phone_number), 0, 3)=='62'){
                $phone_number = trim($phone_number);
            }
            // cek apakah no hp karakter 1-4 adalah 620
            elseif(substr(trim($phone_number), 0, 3)=='620'){
                $phone_number = '62'.substr(trim($phone_number), 3);
            }
            // cek apakah no hp karakter 1 adalah 0
            elseif(substr(trim($phone_number), 0, 1)=='0'){
                $phone_number = '62'.substr(trim($phone_number), 1);
            }
            // cek apakah no hp karakter 1 adalah 8
            elseif(substr(trim($phone_number), 0, 1)=='8'){
                $phone_number = '62'.substr(trim($phone_number), 0);
            }
        }

        return $phone_number;
    }
}

if (!function_exists('throw_custom_exception')) {
    function throw_custom_exception(Exception $exception) {
        if(app()->environment('production')) {
            $url = url()->full();
            $method = request()->getMethod();
            $reqData = json_encode(request()->all());

            $error = "The legendary wild bug has appeared!";
            $error .= "\n";
            $error .= "```";
            $error .= "\n Message        : {$exception->getMessage()}";
            $error .= "\n File           : {$exception->getFile()}";
            $error .= "\n Line           : {$exception->getLine()}";
            $error .= "\n Route          : {$url}";
            $error .= "\n Method         : {$method}";
            $error .= "\n Request        : {$reqData}";
            $error .= "```";
            Log::critical($error);
        }
    }
}

if (!function_exists('set_env')) {
    function set_env($key, $value) {
    	file_put_contents(app()->environmentFilePath(), str_replace(
    		$key . '=' . env($key),
    		$key . '=' . $value,
    		file_get_contents(app()->environmentFilePath())
    	));
    }
}

if (!function_exists('http_post_request')) {
    function http_post_request($url, $headers, $body) {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($body));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }
}

if (!function_exists('slugify')) {
    function slugify($text, string $divider = '-') {
        // replace non letter or digits by divider
        $text = preg_replace('~[^\pL\d]+~u', $divider, $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, $divider);

        // remove duplicate divider
        $text = preg_replace('~-+~', $divider, $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }
}

if (!function_exists('transformer')) {
    function transformer($query, $transformer, $includes = [], $method = 'toArray') {
        return fractal($query, $transformer)
            ->serializeWith(new App\Serializers\Serializer)
            ->parseIncludes($includes)
            ->$method();
    }
}

if (!function_exists('paginateTransformer')) {
    function paginateTransformer($query, $transformer, $includes = [], $perPage = 15, $method = 'toArray') {
        $paginator = $query->paginate($perPage)->appends(request()->except('page'));

        return [
            'data' => fractal($paginator->getCollection(), $transformer)
                ->serializeWith(new App\Serializers\Serializer)
                ->parseIncludes($includes)
                ->$method(),
            'meta' => [
                'pagination' => [
                    'total' => $paginator->total(),
                    'count' => $paginator->count(),
                    'per_page' => $paginator->perPage(),
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                    'has_more_pages' => $paginator->hasMorePages(),
                    'from' => $paginator->firstItem(),
                    'to' => $paginator->lastItem(),
                    'links' => [
                        'previous' => $paginator->previousPageUrl(),
                        'next' => $paginator->nextPageUrl(),
                        'current' => $paginator->url($paginator->currentPage()),
                        'first' => $paginator->url(1),
                        'last' => $paginator->url($paginator->lastPage())
                    ]
                ]
            ]
        ];
    }
}

?>

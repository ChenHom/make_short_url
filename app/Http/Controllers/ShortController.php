<?php

namespace App\Http\Controllers;

use App\Filters\FilterRepeatedComments;
use App\Models\Short;

class ShortController extends Controller
{
    public function index()
    {
        $url = request('url');
        if (empty($url)) {
            return response('輸入的網址無效', '400');
        }

        $parsedUrl = parse_url($url);

        $host = $parsedUrl['host'];
        $scheme = $parsedUrl['scheme'];
        // 使用 murmur3f 產生的數字較大，需改用 bcmath
        $short = base62(hexdec(hash('murmur3a', $url)));

        $store = new FilterRepeatedComments();

        if($store->exists($short) /** && Redis::has($short) */) {
            Short::whereShort($short)->touch();
        } else {
            $store->add($short);
        }
        Short::create([
            'long' => $url,
            'is_secure' => $scheme,
            'short' => $short,
            'host' => $host
        ]);
        return response("http://laravel.test/{$short}");
    }
}

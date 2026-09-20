<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class CareerBrand
{
    /**
     * @return array<string, mixed>
     */
    public static function resolve(?Request $request = null): array
    {
        $request ??= request();

        $host = Str::lower($request->getHost());
        $domainBrandKey = config('careers.domains')[$host] ?? null;
        $brandKey = $request->string('brand')->lower()->toString()
            ?: $request->string('brand_key')->lower()->toString()
            ?: $domainBrandKey
            ?: session('career_brand')
            ?: config('careers.default_brand');
        $brandKey = config("careers.aliases.{$brandKey}", $brandKey);

        if (! Arr::has(config('careers.brands'), $brandKey)) {
            $brandKey = config('careers.default_brand');
        }

        session(['career_brand' => $brandKey]);

        return self::brand($brandKey);
    }

    /**
     * @return array<string, mixed>
     */
    public static function brand(?string $brandKey): array
    {
        $defaultBrandKey = config('careers.default_brand');
        $brandKey = $brandKey && Arr::has(config('careers.brands'), $brandKey)
            ? $brandKey
            : $defaultBrandKey;

        $brand = config("careers.brands.{$brandKey}", config("careers.brands.{$defaultBrandKey}"));

        return [
            ...$brand,
            'key' => $brandKey,
            'phone' => config('careers.phone'),
            'whatsapp_url' => 'https://wa.me/'.self::whatsAppNumber((string) config('careers.phone')),
        ];
    }

    private static function whatsAppNumber(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone) ?: '';

        if (Str::startsWith($digits, '0')) {
            return '62'.Str::after($digits, '0');
        }

        return $digits;
    }
}

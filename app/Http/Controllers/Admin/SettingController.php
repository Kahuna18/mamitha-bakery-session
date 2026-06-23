<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'store_name' => 'required|string|max:100',
            'store_address' => 'required|string',
            'store_phone' => 'required|string|max:20',
            'store_whatsapp' => 'required|string|max:20',
            'store_email' => 'nullable|email',
            'open_time' => 'required|string',
            'close_time' => 'required|string',
            'open_time_sunday' => 'required|string',
            'close_time_sunday' => 'required|string',
            'daily_order_limit' => 'integer|min:0',
            'is_closed' => 'boolean',
            'delivery_fee_enabled' => 'boolean',
            'discount_enabled' => 'boolean',
            'delivery_fee_amount' => 'integer|min:0',
            'discount_percentage' => 'integer|min:1|max:100',
            'about_text' => 'nullable|string',
            'google_maps_api_key' => 'nullable|string',
            'store_latitude' => 'nullable|string',
            'store_longitude' => 'nullable|string',
            'store_gmaps_link' => 'nullable|string',
            'courier_name' => 'nullable|string|max:100',
            'courier_phone' => 'nullable|string|max:20',
            'baking_duration_minutes' => 'required|integer|min:1',
            'delivery_duration_minutes' => 'required|integer|min:1',
        ]);

        $validated['is_closed'] = $request->input('is_closed') == '1' ? 'true' : 'false';
        $validated['delivery_fee_enabled'] = $request->input('delivery_fee_enabled') == '1' ? 'true' : 'false';
        $validated['discount_enabled'] = $request->input('discount_enabled') == '1' ? 'true' : 'false';

        if ($request->filled('store_gmaps_link')) {
            $coords = $this->extractLatLngFromGmaps($request->store_gmaps_link);
            if ($coords) {
                $validated['store_latitude'] = $coords['lat'];
                $validated['store_longitude'] = $coords['lng'];
            }
        }

        foreach ($validated as $key => $value) {
            Setting::setValue($key, (string) $value);
        }

        return redirect()->route('admin.settings.index')->with('success', 'Pengaturan berhasil disimpan.');
    }

    private function extractLatLngFromGmaps($url)
    {
        if (empty($url)) return null;

        // Resolve short links like maps.app.goo.gl or goo.gl/maps
        if (strpos($url, 'maps.app.goo.gl') !== false || strpos($url, 'goo.gl/maps') !== false) {
            try {
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_HEADER, true);
                curl_setopt($ch, CURLOPT_NOBODY, true);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
                curl_setopt($ch, CURLOPT_TIMEOUT, 5);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                $response = curl_exec($ch);
                
                preg_match('/[Ll]ocation:\s*(.+)/', $response, $matches);
                if (isset($matches[1])) {
                    $url = trim($matches[1]);
                }
                curl_close($ch);
            } catch (\Exception $e) {
                // Ignore and proceed with original url
            }
        }

        // Regex pattern to search for @lat,lng
        if (preg_match('/@(-?\d+\.\d+),(-?\d+\.\d+)/', $url, $matches)) {
            return ['lat' => $matches[1], 'lng' => $matches[2]];
        }

        // Regex pattern to search for q=lat,lng or q=lat+lng
        if (preg_match('/[?&]q=(-?\d+\.\d+)(?:,|%2C|\+)(-?\d+\.\d+)/i', $url, $matches)) {
            return ['lat' => $matches[1], 'lng' => $matches[2]];
        }

        // Regex pattern for ll=lat,lng
        if (preg_match('/[?&]ll=(-?\d+\.\d+),(-?\d+\.\d+)/i', $url, $matches)) {
            return ['lat' => $matches[1], 'lng' => $matches[2]];
        }

        // Fallback: fetch HTML page if coordinates are not directly in the URL
        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 8);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
            $html = curl_exec($ch);
            curl_close($ch);

            if ($html) {
                if (preg_match('/staticmap\?[^"]*center=(-?\d+\.\d+)(?:%2C|,)(-?\d+\.\d+)/i', $html, $matches)) {
                    return ['lat' => $matches[1], 'lng' => $matches[2]];
                }
            }
        } catch (\Exception $e) {
            // Ignore
        }

        return null;
    }
}

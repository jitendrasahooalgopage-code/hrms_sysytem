<?php

namespace App\Services;

use App\Models\LoginActivity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Jenssegers\Agent\Agent;

class LoginActivityService
{
    public function store(
        User $user,
        Request $request,
        string $type = 'login',
        string $status = 'success',
        string $loginMethod = 'Password'
    ): LoginActivity {

        $agent = new Agent();

        $agent->setUserAgent(
            $request->userAgent()
        );

        $ip = $this->getIpAddress($request);

        $location = $this->fetchLocation($ip);

        return LoginActivity::create([
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,

            'ip_address' => $ip,

            'latitude' => $location['lat'] ?? null,
            'longitude' => $location['lon'] ?? null,

            'country' => $location['address']['country'] ?? null,
            'state' => $location['address']['state'] ?? null,
            'city' =>
                $location['address']['city']
                ?? $location['address']['town']
                ?? $location['address']['village']
                ?? null,

            'postal_code' =>
                $location['address']['postcode'] ?? null,

            'device_type' => $agent->isMobile()
                ? 'Mobile'
                : ($agent->isTablet()
                    ? 'Tablet'
                    : 'Desktop'),

            'browser' => $agent->browser(),

            'platform' => $agent->platform(),

            'user_agent' => $request->userAgent(),

            'activity_type' => $type,

            'status' => $status,

            'login_method' => $loginMethod,

            'login_at' => now(),

            'location_response' => $location
        ]);
    }

    protected function getIpAddress(Request $request): string
    {
        return $request->header('X-Forwarded-For')
            ? explode(',', $request->header('X-Forwarded-For'))[0]
            : $request->ip();
    }

    protected function fetchLocation(string $ip): array
    {
        try {

            $ipInfo = Http::timeout(10)
                ->get("http://ip-api.com/json/{$ip}")
                ->json();

            if (!isset($ipInfo['lat'])) {
                return [];
            }

            $response = Http::withHeaders([
                'User-Agent' => 'NTTF ERP'
            ])->get(
                'https://nominatim.openstreetmap.org/reverse',
                [
                    'lat' => $ipInfo['lat'],
                    'lon' => $ipInfo['lon'],
                    'format' => 'jsonv2'
                ]
            );

            $data = $response->json();

            $data['lat'] = $ipInfo['lat'];
            $data['lon'] = $ipInfo['lon'];

            return $data;

        } catch (\Throwable $e) {

            return [];
        }
    }
}
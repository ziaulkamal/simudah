<?php

namespace App\Http\Controllers;

use App\Models\Kemendagri\Districts;
use App\Models\Kemendagri\Provinces;
use App\Models\Kemendagri\Regencies;
use App\Models\Kemendagri\Villages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\HmacService;


class MendagriController extends Controller
{
    private $baseUrl;
    private $clientId;
    private $clientSecret;

    public function __construct()
    {
        $this->baseUrl = env('PYTHON_GATEWAY_BASE_URL', 'http://localhost:8000');
        $this->clientId = env('PYTHON_GATEWAY_CLIENT_ID');
        $this->clientSecret = env('PYTHON_GATEWAY_CLIENT_SECRET');
    }

    public function getProvinces()
    {
        return Provinces::all()->pluck('name', 'id');
    }

    public function getRegencies($provinceId)
    {
        return Regencies::where('province_id', $provinceId)->pluck('name', 'id');
    }

    public function getDistricts($regencyId)
    {
        return Districts::where('regency_id', $regencyId)->pluck('name', 'id');
    }

    public function getVillages($districtId)
    {
        return Villages::where('district_id', $districtId)->pluck('name', 'id');
    }

    /**
     * Ambil identitas penduduk berdasarkan NIK
     */
    public function fetchIdentityByNik(Request $request)
    {
        $nik = $request->input('nik');

        if (!$nik || strlen($nik) !== 16) {
            return response()->json(['error' => 'Invalid NIK'], 400);
        }

        $body = ['nik' => $nik];
        $signature = HmacService::generateSignature($body, $this->clientSecret);


        try {
            $response = Http::withHeaders([
                'x-client-id' => $this->clientId,
                'x-signature' => $signature,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/identity/nik", $body);

            $gatewayData = $response->json();

            // Hitung gender, birthdate, age dari NIK
            $gender = $this->extractGenderFromNik($nik);
            $birthdate = $this->extractBirthdateFromNik($nik);
            $age = $birthdate ? $this->calculateAge($birthdate) : null;

            // Timpa / merge response gateway dengan data tambahan
            $finalData = array_merge($gatewayData, [
                'gender' => $gender,
                'birthdate' => $birthdate,
                'age' => $age,
            ]);

            if ($response->status() === 403) return response()->json(['error' => 'Forbidden: Invalid client credentials'], 403);
            return response()->json($finalData, $response->status());
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Failed to connect to Python Gateway API',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Ambil identitas penduduk berdasarkan nama + NIK
     */
    public function fetchIdentityBySearch(Request $request)
    {
        $nik = $request->input('nik');
        $name = $request->input('name');

        if (!$nik || !$name) {
            return response()->json(['error' => 'Missing name or NIK'], 400);
        }

        $body = ['nik' => $nik, 'name' => $name];
        $signature = HmacService::generateSignature($body, $this->clientSecret);

        try {
            $response = Http::withHeaders([
                'x-client-id' => $this->clientId,
                'x-signature' => $signature,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/identity/search", $body);

            if ($response->status() === 403) return response()->json(['error' => 'Forbidden: Invalid client credentials'], 403);
            return response()->json($response->json(), $response->status());
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Failed to connect to Python Gateway API',
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    private function extractGenderFromNik(string $nik): ?string
    {
        if (strlen($nik) < 12) return null;

        $day = intval(substr($nik, 6, 2));
        return $day > 40 ? 'female' : 'male';
    }

    /**
     * Ambil birthdate dari NIK
     */
    private function extractBirthdateFromNik(string $nik): ?string
    {
        if (strlen($nik) < 12) return null;

        $day = intval(substr($nik, 6, 2));
        $month = intval(substr($nik, 8, 2));
        $year = intval(substr($nik, 10, 2));

        if ($day > 40) $day -= 40; // Perempuan
        $fullYear = $year <= 25 ? 2000 + $year : 1900 + $year;

        return sprintf('%04d-%02d-%02d', $fullYear, $month, $day);
    }

    /**
     * Hitung usia dari birthdate (YYYY-MM-DD)
     */
    private function calculateAge(string $birthdate): int
    {
        $birth = new \DateTime($birthdate);
        $today = new \DateTime();
        $age = $today->diff($birth)->y;

        return $age;
    }


}

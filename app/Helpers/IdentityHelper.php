<?php

if (!function_exists('extract_birth_info_from_nik')) {
    /**
     * Ekstrak tanggal lahir, jenis kelamin, dan usia dari NIK.
     *
     * @param  string  $nik
     * @return array|null
     */
    function extract_birth_info_from_nik(string $nik): ?array
    {
        if (strlen($nik) < 12) {
            return null;
        }

        $day = (int) substr($nik, 6, 2);
        $month = (int) substr($nik, 8, 2);
        $year = (int) substr($nik, 10, 2);

        // Tentukan jenis kelamin
        $gender = $day > 40 ? 'female' : 'male';
        if ($day > 40) {
            $day -= 40;
        }

        // Tentukan tahun lahir (1900-an atau 2000-an)
        $fullYear = $year <= (int) date('y') ? 2000 + $year : 1900 + $year;
        $birthdate = sprintf('%04d-%02d-%02d', $fullYear, $month, $day);

        // Hitung usia
        try {
            $birthDateObj = new DateTime($birthdate);
            $today = new DateTime();
            $age = $today->diff($birthDateObj)->y;
        } catch (Exception $e) {
            $age = null;
        }

        return [
            'birthdate' => $birthdate,
            'gender'    => $gender,
            'age'       => $age,
        ];
    }
}

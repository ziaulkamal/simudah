<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PeopleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // ubah sesuai kebutuhan auth
    }

    public function rules(): array
    {
        return [
            'fullName' => 'required|string|max:255',
            'identityNumber' => 'required|string|size:16',
            'familyIdentityNumber' => 'nullable|string',
            'gender' => 'required|in:male,female',
            'birthdate' => 'required|date',
            'streetAddress' => 'required|string',
            'religion' => 'required|integer',
            'provinceId' => 'required|integer',
            'regencieId' => 'required|integer',
            'districtId' => 'required|integer',
            'villageId' => 'required|integer',
            'phoneNumber' => 'required|string',
            'email' => 'nullable|email',
            'role_id' => 'nullable|exists:roles,id',
            'category_id' => 'nullable|exists:categories,id'
        ];
    }

    public function messages(): array
    {
        return [
            'fullName.required' => 'Nama lengkap wajib diisi.',
            'fullName.string' => 'Nama lengkap harus berupa teks.',
            'fullName.max' => 'Nama lengkap maksimal 255 karakter.',

            'identityNumber.required' => 'NIK wajib diisi.',
            'identityNumber.string' => 'NIK harus berupa teks.',
            'identityNumber.size' => 'NIK harus 16 digit.',

            'familyIdentityNumber.string' => 'Nomor KK harus berupa teks.',

            'gender.required' => 'Gender wajib diisi.',
            'gender.in' => 'Gender harus "male" atau "female".',

            'birthdate.required' => 'Tanggal lahir wajib diisi.',
            'birthdate.date' => 'Tanggal lahir tidak valid.',

            'streetAddress.required' => 'Alamat wajib diisi.',
            'streetAddress.string' => 'Alamat harus berupa teks.',

            'religion.required' => 'Agama wajib diisi.',
            'religion.integer' => 'Agama tidak valid.',

            'provinceId.required' => 'Provinsi wajib dipilih.',
            'provinceId.integer' => 'Provinsi tidak valid.',

            'regencieId.required' => 'Kabupaten/Kota wajib dipilih.',
            'regencieId.integer' => 'Kabupaten/Kota tidak valid.',

            'districtId.required' => 'Kecamatan wajib dipilih.',
            'districtId.integer' => 'Kecamatan tidak valid.',

            'villageId.required' => 'Desa/Kelurahan wajib dipilih.',
            'villageId.integer' => 'Desa/Kelurahan tidak valid.',

            'phoneNumber.required' => 'Nomor telepon wajib diisi.',
            'phoneNumber.string' => 'Nomor telepon tidak valid.',

            'email.email' => 'Email tidak valid.',

            'role_id.required' => 'Role wajib dipilih.',
            'role_id.exists' => 'Role tidak ditemukan.',

            'category_id.exists' => 'Kategori tidak ditemukan.',
        ];
    }
}

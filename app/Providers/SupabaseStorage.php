<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SupabaseStorage
{
    public static function upload($filePath, $fileName)
    {
        $url = "https://simhjkvtmmsdnkinsmun.supabase.co"
            . "/storage/v1/object/"
            . "pdf"
            . "/$fileName";

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InNpbWhqa3Z0bW1zZG5raW5zbXVuIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NjcwNjk0MDksImV4cCI6MjA4MjY0NTQwOX0.krHd1NnF325CMf-JfYc4oI1XArYTh3nSpWEcRiuxc2M',
            'Content-Type'  => 'application/pdf'
        ])->post($url, fopen($filePath, 'r'));

        if (!$response->successful()) {
            throw new \Exception($response->body());
        }

        return true;
    }
}
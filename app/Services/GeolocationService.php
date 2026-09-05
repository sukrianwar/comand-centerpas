<?php

namespace App\Services;

class GeolocationService
{
    /**
     * Hitung jarak antara dua titik koordinat menggunakan rumus Haversine.
     *
     * @param  float  $lat1   Latitude titik 1 (derajat)
     * @param  float  $lon1   Longitude titik 1 (derajat)
     * @param  float  $lat2   Latitude titik 2 (derajat)
     * @param  float  $lon2   Longitude titik 2 (derajat)
     * @return float          Jarak dalam meter
     */
    public static function distanceInMeters(
        float $lat1,
        float $lon1,
        float $lat2,
        float $lon2
    ): float {
        // Radius bumi dalam meter
        $earthRadius = 6_371_000;

        // Konversi derajat ke radian
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) ** 2
           + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 2);
    }

    /**
     * Alias lebih deskriptif — cek apakah device berada dalam radius checkpoint.
     *
     * @param  float  $deviceLat
     * @param  float  $deviceLon
     * @param  float  $checkpointLat
     * @param  float  $checkpointLon
     * @param  int    $radiusMeters     Radius maksimum checkpoint (dari DB)
     * @return bool
     */
    public static function isWithinRadius(
        float $deviceLat,
        float $deviceLon,
        float $checkpointLat,
        float $checkpointLon,
        int $radiusMeters
    ): bool {
        $distance = self::distanceInMeters($deviceLat, $deviceLon, $checkpointLat, $checkpointLon);
        return $distance <= $radiusMeters;
    }
}

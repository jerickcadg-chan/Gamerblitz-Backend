<?php

namespace App\Services;

class CustAccountService
{
    public static function idExtractor($productName, $value)
    {
        $account = json_decode($value);

        switch ($productName) {
            case 'Mobile Legends Fast':
            case 'Mobile Legends Promo':
                $requiredInformation = trim($account->player_id) . trim($account->server_id);
                break;

            case 'Valorant':
            case 'League of Legends: Wild Rift':
                $requiredInformation = trim($account->riot_id);
                break;

            case 'Genshin Impact':
                $requiredInformation = self::genshinFormatter($account);
                break;

            default:
                if (!self::isValidJSON($value)) {
                    return self::convertPlayerIdFormat($value);
                }

                $requiredInformation = isset($account->player_id) ? self::convertPlayerIdFormat($account->player_id) : self::convertPlayerIdFormat($value);
                break;
        }

        return $requiredInformation;
    }

    private static function genshinFormatter($account): string
    {
//        $zoneIdMap = [
//            'Asia' => 'os_asia',
//            'America' => 'os_usa',
//            'Europe' => 'os_euro',
//            'TW,HK,MO' => 'os_cht'
//        ];

        $zoneIdMap = [
            'Asia' => 'Asia',
            'America ' => 'America',
            'Europe' => 'Europe',
            'TW,HK,MO' => 'TW_HK_MO'
        ];

        $zoneId = $zoneIdMap[$account->zone_id] ?? "Asia";

        return "$account->uid|$zoneId";
    }

    public static function convertPlayerIdFormat($id)
    {
        $match = [];
        preg_match('/\b\d+\b/', $id, $match);

        if ($match) {
            return $match[0];
        }

        $idMatch = [];
        preg_match('/(?:Id\s*[:.]\s*|ID\s*[:.]\s*)(\d+)\b/i', $id, $idMatch);

        if ($idMatch) {
            return $idMatch[1];
        }

        return $id;
    }

    private static function isValidJSON($jsonString): bool
    {
        json_decode($jsonString);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}

<?php

declare(strict_types=1);

function weather_cache_path(): string
{
    return dirname(__DIR__) . '/storage/weather-cache.json';
}

function weather_default_live_config(array $weather): array
{
    $live = is_array($weather['live'] ?? null) ? $weather['live'] : [];

    return array_replace_recursive([
        'provider' => 'open_meteo',
        'enabled' => true,
        'latitude' => 42.34889,
        'longitude' => 42.28417,
        'cache_minutes' => 30,
        'nearby' => [
            ['name' => 'მარტვილი', 'latitude' => 42.4146, 'longitude' => 42.3792],
            ['name' => 'აბაშა', 'latitude' => 42.2007, 'longitude' => 42.2096],
            ['name' => 'გაჭედილი', 'latitude' => 42.455, 'longitude' => 42.377],
        ],
    ], $live);
}

function weather_code_summary(int $code): string
{
    return match (true) {
        $code === 0 => 'მზიანი',
        in_array($code, [1, 2], true) => 'ნაწილობრივ ღრუბლიანი',
        $code === 3 => 'ღრუბლიანი',
        in_array($code, [45, 48], true) => 'ნისლი',
        in_array($code, [51, 53, 55, 56, 57], true) => 'ჟინჟღლი',
        in_array($code, [61, 63, 65, 66, 67, 80, 81, 82], true) => 'წვიმა',
        in_array($code, [71, 73, 75, 77, 85, 86], true) => 'თოვლი',
        in_array($code, [95, 96, 99], true) => 'ჭექა-ქუხილი',
        default => 'ცვალებადი',
    };
}

function weather_cache_read(string $cacheKey, int $ttlSeconds): ?array
{
    $path = weather_cache_path();
    if (!is_file($path)) {
        return null;
    }

    $data = json_decode((string) file_get_contents($path), true);
    if (!is_array($data) || ($data['key'] ?? '') !== $cacheKey) {
        return null;
    }

    $fetchedAt = (int) ($data['fetched_at'] ?? 0);
    if ($fetchedAt <= 0 || time() - $fetchedAt > $ttlSeconds) {
        return null;
    }

    return is_array($data['weather'] ?? null) ? $data['weather'] : null;
}

function weather_cache_write(string $cacheKey, array $weather): void
{
    $path = weather_cache_path();
    $directory = dirname($path);
    if (!is_dir($directory) && !mkdir($directory, 0775, true)) {
        return;
    }

    $payload = json_encode([
        'key' => $cacheKey,
        'fetched_at' => time(),
        'weather' => $weather,
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    if (is_string($payload)) {
        file_put_contents($path, $payload . PHP_EOL, LOCK_EX);
    }
}

function weather_http_json(string $url): ?array
{
    $context = stream_context_create([
        'http' => [
            'timeout' => 4,
            'ignore_errors' => true,
            'header' => "User-Agent: BanzaSite/1.0\r\n",
        ],
    ]);

    $json = @file_get_contents($url, false, $context);
    if ((!is_string($json) || $json === '') && function_exists('curl_init')) {
        $curl = curl_init($url);
        if ($curl !== false) {
            curl_setopt_array($curl, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 4,
                CURLOPT_USERAGENT => 'BanzaSite/1.0',
            ]);
            $curlJson = curl_exec($curl);
            curl_close($curl);
            if (is_string($curlJson) && $curlJson !== '') {
                $json = $curlJson;
            }
        }
    }

    if (!is_string($json) || $json === '') {
        return null;
    }

    $data = json_decode($json, true);
    return is_array($data) ? $data : null;
}

function weather_format_open_meteo_location(array $location, string $name): array
{
    $current = is_array($location['current'] ?? null) ? $location['current'] : [];
    $daily = is_array($location['daily'] ?? null) ? $location['daily'] : [];

    $temperature = is_numeric($current['temperature_2m'] ?? null) ? (int) round((float) $current['temperature_2m']) : null;
    $wind = is_numeric($current['wind_speed_10m'] ?? null) ? (int) round((float) $current['wind_speed_10m']) : null;
    $humidity = is_numeric($current['relative_humidity_2m'] ?? null) ? (int) round((float) $current['relative_humidity_2m']) : null;
    $weatherCode = is_numeric($current['weather_code'] ?? null) ? (int) $current['weather_code'] : 3;
    $rainChance = is_array($daily['precipitation_probability_max'] ?? null)
        && is_numeric($daily['precipitation_probability_max'][0] ?? null)
        ? (int) round((float) $daily['precipitation_probability_max'][0])
        : 0;

    return [
        'name' => $name,
        'forecast' => weather_code_summary($weatherCode),
        'temperature' => $temperature !== null ? $temperature . '°C' : '',
        'summary' => weather_code_summary($weatherCode),
        'wind' => $wind !== null ? $wind . ' კმ/სთ' : '',
        'humidity' => $humidity !== null ? $humidity . '%' : '',
        'rain' => $rainChance . '%',
    ];
}

function weather_fetch_open_meteo(array $weather): ?array
{
    $live = weather_default_live_config($weather);
    $locations = array_merge([
        ['name' => 'ბანძა', 'latitude' => $live['latitude'], 'longitude' => $live['longitude']],
    ], is_array($live['nearby'] ?? null) ? $live['nearby'] : []);

    $latitudes = [];
    $longitudes = [];
    foreach ($locations as $location) {
        if (!is_numeric($location['latitude'] ?? null) || !is_numeric($location['longitude'] ?? null)) {
            continue;
        }
        $latitudes[] = (string) $location['latitude'];
        $longitudes[] = (string) $location['longitude'];
    }

    if ($latitudes === [] || count($latitudes) !== count($longitudes)) {
        return null;
    }

    $url = 'https://api.open-meteo.com/v1/forecast?' . http_build_query([
        'latitude' => implode(',', $latitudes),
        'longitude' => implode(',', $longitudes),
        'current' => 'temperature_2m,relative_humidity_2m,weather_code,wind_speed_10m',
        'daily' => 'precipitation_probability_max',
        'timezone' => 'Asia/Tbilisi',
        'forecast_days' => 3,
        'wind_speed_unit' => 'kmh',
    ]);

    $data = weather_http_json($url);
    if (!is_array($data)) {
        return null;
    }

    $responses = array_is_list($data) ? $data : [$data];
    if (!isset($responses[0]) || !is_array($responses[0])) {
        return null;
    }

    $main = weather_format_open_meteo_location($responses[0], 'ბანძა');
    $nearby = [];
    foreach (array_slice($responses, 1) as $index => $response) {
        if (!is_array($response)) {
            continue;
        }
        $nearby[] = weather_format_open_meteo_location($response, (string) ($locations[$index + 1]['name'] ?? 'ახლოს'));
    }

    return array_merge($weather, [
        'summary' => $main['summary'],
        'temperature' => $main['temperature'],
        'wind' => $main['wind'],
        'humidity' => $main['humidity'],
        'rain' => $main['rain'],
        'nearby' => $nearby !== [] ? $nearby : ($weather['nearby'] ?? []),
        'source_label' => 'Open-Meteo live',
        'updated_at' => date('d/m/Y H:i'),
        'live' => $live,
    ]);
}

function resolve_weather_data(array $weather): array
{
    $live = weather_default_live_config($weather);
    if (($live['provider'] ?? 'open_meteo') !== 'open_meteo' || empty($live['enabled'])) {
        return array_merge($weather, ['live' => $live, 'source_label' => 'Admin fallback']);
    }

    $ttlSeconds = max(5, (int) ($live['cache_minutes'] ?? 30)) * 60;
    $cacheKey = sha1((string) json_encode($live, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    $cached = weather_cache_read($cacheKey, $ttlSeconds);
    if ($cached !== null) {
        return $cached;
    }

    $liveWeather = weather_fetch_open_meteo($weather);
    if ($liveWeather === null) {
        return array_merge($weather, ['live' => $live, 'source_label' => 'Admin fallback']);
    }

    weather_cache_write($cacheKey, $liveWeather);
    return $liveWeather;
}

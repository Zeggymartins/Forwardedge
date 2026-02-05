<?php

namespace App\Services;

use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberType;
use libphonenumber\geocoding\PhoneNumberOfflineGeocoder;
use libphonenumber\PhoneNumberToCarrierMapper;
use libphonenumber\NumberParseException;
use Twilio\Rest\Client as TwilioClient;

class PhoneNumberNormalizer
{
    public function normalize(?string $raw, ?string $location = null, ?string $regionHint = null, bool $useTwilio = true): array
    {
        $raw = is_string($raw) ? trim($raw) : '';

        if ($raw === '') {
            return $this->emptyResult($raw);
        }

        $region = $this->resolveRegion($raw, $location, $regionHint);
        $util = PhoneNumberUtil::getInstance();

        try {
            $number = $util->parse($raw, $region ?? 'ZZ');
        } catch (NumberParseException $e) {
            return [
                'raw' => $raw,
                'is_valid' => false,
                'error' => $e->getMessage(),
            ];
        }

        $isValid = $util->isValidNumber($number);

        $result = [
            'raw' => $raw,
            'is_valid' => $isValid,
            'country_code' => $number->getCountryCode(),
            'region_code' => $util->getRegionCodeForNumber($number),
            'e164' => $util->format($number, PhoneNumberFormat::E164),
            'international' => $util->format($number, PhoneNumberFormat::INTERNATIONAL),
            'national' => $util->format($number, PhoneNumberFormat::NATIONAL),
            'type' => $this->typeLabel($util->getNumberType($number)),
        ];

        $geocoder = PhoneNumberOfflineGeocoder::getInstance();
        $carrier = PhoneNumberToCarrierMapper::getInstance();
        $result['location_description'] = $geocoder->getDescriptionForNumber($number, 'en') ?: null;
        $result['carrier'] = $carrier->getNameForNumber($number, 'en') ?: null;
        $result['twilio'] = null;

        if ($useTwilio && $result['e164']) {
            $twilio = $this->lookupTwilio($result['e164']);
            if ($twilio) {
                $result['twilio'] = $twilio;
            }
        }

        return $result;
    }

    private function resolveRegion(string $raw, ?string $location, ?string $regionHint): ?string
    {
        $raw = trim($raw);

        if ($raw !== '' && str_starts_with($raw, '+')) {
            return null;
        }

        if ($regionHint) {
            $regionHint = strtoupper(trim($regionHint));
            if (preg_match('/^[A-Z]{2}$/', $regionHint)) {
                return $regionHint;
            }
        }

        if ($location) {
            $country = $this->extractCountryFromLocation($location);
            $region = $this->countryNameToRegion($country);
            if ($region) {
                return $region;
            }
        }

        return null;
    }

    private function extractCountryFromLocation(string $location): string
    {
        $parts = preg_split('/[,\-\/|]/', $location);
        $parts = array_map('trim', $parts ?: []);
        $parts = array_filter($parts);
        $last = end($parts);
        return $last ? (string) $last : trim($location);
    }

    private function countryNameToRegion(string $country): ?string
    {
        $country = strtolower(trim($country));
        if ($country === '') {
            return null;
        }

        $map = [
            'nigeria' => 'NG',
            'ghana' => 'GH',
            'pakistan' => 'PK',
            'kenya' => 'KE',
            'south africa' => 'ZA',
            'united states' => 'US',
            'usa' => 'US',
            'united kingdom' => 'GB',
            'uk' => 'GB',
            'canada' => 'CA',
            'germany' => 'DE',
            'france' => 'FR',
            'spain' => 'ES',
            'italy' => 'IT',
            'netherlands' => 'NL',
            'australia' => 'AU',
            'india' => 'IN',
            'bangladesh' => 'BD',
            'uganda' => 'UG',
            'tanzania' => 'TZ',
            'rwanda' => 'RW',
            'cameroon' => 'CM',
            'sierra leone' => 'SL',
            'liberia' => 'LR',
            'zambia' => 'ZM',
            'zimbabwe' => 'ZW',
            'senegal' => 'SN',
            'cote d\'ivoire' => 'CI',
            'ivory coast' => 'CI',
            'egypt' => 'EG',
            'morocco' => 'MA',
            'algeria' => 'DZ',
        ];

        return $map[$country] ?? null;
    }

    private function typeLabel(int $type): string
    {
        return match ($type) {
            PhoneNumberType::FIXED_LINE => 'fixed_line',
            PhoneNumberType::MOBILE => 'mobile',
            PhoneNumberType::FIXED_LINE_OR_MOBILE => 'fixed_or_mobile',
            PhoneNumberType::TOLL_FREE => 'toll_free',
            PhoneNumberType::PREMIUM_RATE => 'premium_rate',
            PhoneNumberType::SHARED_COST => 'shared_cost',
            PhoneNumberType::VOIP => 'voip',
            PhoneNumberType::PERSONAL_NUMBER => 'personal_number',
            PhoneNumberType::PAGER => 'pager',
            PhoneNumberType::UAN => 'uan',
            PhoneNumberType::VOICEMAIL => 'voicemail',
            default => 'unknown',
        };
    }

    private function lookupTwilio(string $e164): ?array
    {
        $enabled = (bool) config('phone.twilio.enabled', false);
        $sid = (string) config('phone.twilio.sid');
        $token = (string) config('phone.twilio.token');
        $fields = (string) config('phone.twilio.fields', 'carrier,line_type_intelligence');

        if (!$enabled || $sid === '' || $token === '') {
            return null;
        }

        try {
            $client = new TwilioClient($sid, $token);
            $response = $client->lookups->v2->phoneNumbers($e164)->fetch([
                'fields' => $fields,
            ]);

            return [
                'valid' => $response->valid ?? null,
                'country_code' => $response->countryCode ?? null,
                'national_format' => $response->nationalFormat ?? null,
                'phone_number' => $response->phoneNumber ?? null,
                'caller_name' => $response->callerName ?? null,
                'carrier' => $response->carrier ?? null,
                'line_type_intelligence' => $response->lineTypeIntelligence ?? null,
            ];
        } catch (\Throwable $e) {
            return [
                'error' => $e->getMessage(),
            ];
        }
    }

    private function emptyResult(string $raw): array
    {
        return [
            'raw' => $raw,
            'is_valid' => false,
            'country_code' => null,
            'region_code' => null,
            'e164' => null,
            'international' => null,
            'national' => null,
            'type' => null,
            'location_description' => null,
            'carrier' => null,
            'twilio' => null,
        ];
    }
}

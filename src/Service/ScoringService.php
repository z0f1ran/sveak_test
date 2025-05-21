<?php

namespace App\Service;

use App\Entity\Client;

class ScoringService
{
    private const SCORE_OPERATOR_MEGAFON = 10;
    private const SCORE_OPERATOR_BEELINE = 5;
    private const SCORE_OPERATOR_MTS = 3;
    private const SCORE_OPERATOR_OTHER = 1;

    private const SCORE_EMAIL_GMAIL = 10;
    private const SCORE_EMAIL_YANDEX = 8;
    private const SCORE_EMAIL_MAIL = 6;
    private const SCORE_EMAIL_OTHER = 3;

    private const SCORE_EDUCATION_HIGHER = 15;
    private const SCORE_EDUCATION_SPECIAL = 10;
    private const SCORE_EDUCATION_MEDIUM = 5;

    private const SCORE_CONSENT_AGREED = 4;
    private const SCORE_CONSENT_NOT_AGREED = 0;

    /**
     * Рассчитывает скоринг для клиента и возвращает массив с детализацией и общим баллом.
     *
     * @param Client $client
     * @return array{'total': int, 'details': array<string, int>}
     */
    public function calculateScoreDetails(Client $client): array
    {
        $details = [
            'operator' => 0,
            'email' => 0,
            'education' => 0,
            'consent' => 0,
        ];

        // 1. Скоринг по сотовому оператору
        $phoneNumber = $client->getPhoneNumber();
        if (str_starts_with($phoneNumber, '+792') || str_starts_with($phoneNumber, '892')) {
            $details['operator'] = self::SCORE_OPERATOR_MEGAFON;
        } elseif (str_starts_with($phoneNumber, '+790') || str_starts_with($phoneNumber, '890') || str_starts_with($phoneNumber, '+796') || str_starts_with($phoneNumber, '896')) {
            $details['operator'] = self::SCORE_OPERATOR_BEELINE;
        } elseif (str_starts_with($phoneNumber, '+791') || str_starts_with($phoneNumber, '891') || str_starts_with($phoneNumber, '+798') || str_starts_with($phoneNumber, '898')) {
            $details['operator'] = self::SCORE_OPERATOR_MTS;
        } else {
            $details['operator'] = self::SCORE_OPERATOR_OTHER;
        }

        // 2. Скоринг по домену Э-почты
        $email = $client->getEmail();
        if (str_ends_with($email, '@gmail.com')) {
            $details['email'] = self::SCORE_EMAIL_GMAIL;
        } elseif (str_ends_with($email, '@yandex.ru') || str_ends_with($email, '@ya.ru')) {
            $details['email'] = self::SCORE_EMAIL_YANDEX;
        } elseif (str_ends_with($email, '@mail.ru') || str_ends_with($email, '@bk.ru') || str_ends_with($email, '@list.ru') || str_ends_with($email, '@inbox.ru')) {
            $details['email'] = self::SCORE_EMAIL_MAIL;
        } else {
            $details['email'] = self::SCORE_EMAIL_OTHER;
        }

        // 3. Скоринг по образованию
        $education = $client->getEducation();
        match ($education) {
            'Высшее образование' => $details['education'] = self::SCORE_EDUCATION_HIGHER,
            'Специальное образование' => $details['education'] = self::SCORE_EDUCATION_SPECIAL,
            'Среднее образование' => $details['education'] = self::SCORE_EDUCATION_MEDIUM,
            default => $details['education'] = 0,
        };

        // 4. Скоринг по галочке согласия
        if ($client->isConsentProcessingPersonalData()) {
            $details['consent'] = self::SCORE_CONSENT_AGREED;
        } else {
            $details['consent'] = self::SCORE_CONSENT_NOT_AGREED;
        }

        $totalScore = array_sum($details);

        return [
            'total' => $totalScore,
            'details' => $details,
        ];
    }

    // в ИДЕАЛЕ использовать calculateScoreDetails и брать только total.
    public function calculateScore(Client $client): int
    {
        $scoreData = $this->calculateScoreDetails($client);
        return $scoreData['total'];
    }
} 
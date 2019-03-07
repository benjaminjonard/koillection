<?php

declare(strict_types=1);

namespace App\Service;

use App\Enum\DateFormatEnum;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class DateFormatter
 *
 * @package App\Service
 */
class DateFormatter
{
    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * DateFormatter constructor.
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @return string
     */
    public function guessForForm() : string
    {
        $format = $this->tokenStorage->getToken()->getUser()->getDateFormat();

        return DateFormatEnum::MAPPING[$format][DateFormatEnum::CONTEXT_FORM];
    }

    /**
     * @return string
     */
    public function guessForTwig() : string
    {
        $format = $this->tokenStorage->getToken()->getUser()->getDateFormat();

        return DateFormatEnum::MAPPING[$format][DateFormatEnum::CONTEXT_TWIG];
    }

    /**
     * @return string
     */
    public function guessForJs() : string
    {
        $format = $this->tokenStorage->getToken()->getUser()->getDateFormat();

        return DateFormatEnum::MAPPING[$format][DateFormatEnum::CONTEXT_JS];
    }
}

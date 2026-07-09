<?php

namespace App\Twig\Extension;

use App\Entity\SiteSettings;
use App\Repository\SiteSettingsRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SiteSettingsExtension extends AbstractExtension
{
    public function __construct(
        private SiteSettingsRepository $siteSettingsRepository
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('site_settings', [$this, 'getSiteSettings']),
        ];
    }

    public function getSiteSettings(): ?SiteSettings
    {
        return $this->siteSettingsRepository->findOneBy([]);
    }
}

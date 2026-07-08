<?php

namespace App\Entity;

use App\Repository\SiteSettingsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SiteSettingsRepository::class)]
class SiteSettings
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $siteName = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 30)]
    private ?string $phone = null;

    #[ORM\Column(length: 255)]
    private ?string $address = null;

    #[ORM\Column(length: 10)]
    private ?string $postalCode = null;

    #[ORM\Column(length: 100)]
    private ?string $city = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $openingHoursWeek = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $openingHoursSaturday = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $openingHoursSunday = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSiteName(): ?string
    {
        return $this->siteName;
    }

    public function setSiteName(string $siteName): static
    {
        $this->siteName = $siteName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): static
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getOpeningHoursWeekdays(): ?string
    {
        return $this->openingHoursWeek;
    }

    public function setOpeningHoursWeekdays(?string $openingHoursWeekdays): static
    {
        $this->openingHoursWeek = $openingHoursWeekdays;

        return $this;
    }

    public function getOpeningHoursSaturday(): ?string
    {
        return $this->openingHoursSaturday;
    }

    public function setOpeningHoursSaturday(?string $openingHoursSaturday): static
    {
        $this->openingHoursSaturday = $openingHoursSaturday;

        return $this;
    }

    public function getOpeningHoursSunday(): ?string
    {
        return $this->openingHoursSunday;
    }

    public function setOpeningHoursSunday(?string $openingHoursSunday): static
    {
        $this->openingHoursSunday = $openingHoursSunday;

        return $this;
    }
}

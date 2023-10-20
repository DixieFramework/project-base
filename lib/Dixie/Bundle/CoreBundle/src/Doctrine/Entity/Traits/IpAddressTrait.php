<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Doctrine\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait IpAddressTrait
{
    
    #[ORM\Column(type: Types::STRING, length: 15, nullable: true)]
    #[Assert\Ip(version: '4')]
    #[Assert\Length(max: 15)]
    #[ORM\Column(type: 'string', length: 15, nullable: true)]
    #[Assert\Ip(version: '4')]
    #[Assert\Length(max: 15)]
    protected ?string $ip_address = null;

    public function getIpAddress(): ?string
    {
        return $this->ip_address;
    }

    public function setIpAddress(?string $ip_address): self
    {
        $this->ip_address = $ip_address === null ? null : trim($ip_address);

        return $this;
    }
}

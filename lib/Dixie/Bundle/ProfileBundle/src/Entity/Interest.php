<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Talav\ProfileBundle\Repository\InterestRepository;

#[ORM\Entity(repositoryClass: InterestRepository::class)]
#[ORM\Table('interest')]
class Interest
{
//    /**
//     * @var Uuid
//     *
//     * @ORM\Id()
//     * @ORM\GeneratedValue(strategy="CUSTOM")
//     * @ORM\CustomIdGenerator("doctrine.uuid_generator")
//     * @ORM\Column(type="uuid")
//     */
//    private Uuid $id;
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column(name: 'id', type: 'integer')]
	protected mixed $id;

	#[ORM\Column(name: 'name', type: 'string', length: 255)]
	protected string $name;

    public function getId(): mixed
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}

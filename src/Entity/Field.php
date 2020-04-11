<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\DatumTypeEnum;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * Class Field
 *
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(name="koi_field")
 */
class Field
{
    /**
     * @var UuidInterface
     *
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     */
    private UuidInterface $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private ?string $name = null;

    /**
     * @var integer
     * @ORM\Column(type="integer")
     */
    private ?int $position = null;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private ?string $type = null;

    /**
     * @var Template
     * @ORM\ManyToOne(targetEntity="Template", inversedBy="fields")
     */
    private Template $template;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
    }

    /**
     * @return string
     */
    public function getTypeLabel() : string
    {
        return DatumTypeEnum::getTypeLabel($this->type);
    }

    /**
     * @return null|string
     */
    public function getId() : ?string
    {
        return $this->id->toString();
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getTemplate(): ?Template
    {
        return $this->template;
    }

    public function setTemplate(?Template $template): self
    {
        $this->template = $template;

        return $this;
    }
}

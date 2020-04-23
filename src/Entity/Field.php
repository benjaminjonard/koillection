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
     * @var \Ramsey\Uuid\UuidInterface
     *
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @var integer
     * @ORM\Column(type="integer")
     */
    private $position;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @var \App\Entity\Template
     * @ORM\ManyToOne(targetEntity="Template", inversedBy="fields")
     */
    private $template;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
    }

    /**
     * @return null|string
     */
    public function getId() : ?string
    {
        return $this->id->toString();
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Field
     */
    public function setName(string $name) : self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName() : ?string
    {
        return $this->name;
    }

    /**
     * Set type.
     *
     * @param string $type
     *
     * @return Field
     */
    public function setType(string $type) : self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type.
     *
     * @return string
     */
    public function getType() : ?string
    {
        return $this->type;
    }

    /**
     * Get type.
     *
     * @return string
     */
    public function getTypeLabel() : string
    {
        return DatumTypeEnum::getTypeLabel($this->type);
    }

    /**
     * Set template.
     *
     * @param \App\Entity\Template $template
     *
     * @return Field
     */
    public function setTemplate(Template $template = null) : self
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Get template.
     *
     * @return \App\Entity\Template
     */
    public function getTemplate() : ?Template
    {
        return $this->template;
    }

    /**
     * Set position
     *
     * @param integer $position
     *
     * @return Field
     */
    public function setPosition(int $position) : self
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition() : ?int
    {
        return $this->position;
    }
}

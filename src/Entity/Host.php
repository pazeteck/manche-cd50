<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\HostRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation\Timestampable;

#[ApiResource(paginationEnabled: false)]
#[ApiFilter(SearchFilter::class, properties: ['name' => 'partial'])]
#[ORM\Entity(repositoryClass: HostRepository::class)]
class Host
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ip = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $os = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $dns = null;

    #[ORM\Column]
    private ?int $cpu = null;

    #[ORM\Column]
    private ?int $cores = null;

    #[ORM\Column]
    private ?int $memory = null;

    #[ORM\Column]
    private ?bool $powerstate = null;

    #[ORM\Column]
    private ?bool $is_monitored = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $source_os = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $source_ip = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $source_dns = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $additional_ip = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $model = null;

    #[ORM\Column(nullable: true)]
    private ?int $tools = null;

    #[Timestampable(on: "create")]
    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[Timestampable(on: "update")]
    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $deleted_at = null;

    #[ORM\Column(length: 255)]
    private ?string $uuid = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(?string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    public function getOs(): ?string
    {
        return $this->os;
    }

    public function setOs(?string $os): self
    {
        $this->os = $os;

        return $this;
    }

    public function getDns(): ?string
    {
        return $this->dns;
    }

    public function setDns(?string $dns): self
    {
        $this->dns = $dns;

        return $this;
    }

    public function getCpu(): ?int
    {
        return $this->cpu;
    }

    public function setCpu(int $cpu): self
    {
        $this->cpu = $cpu;

        return $this;
    }

    public function getCores(): ?int
    {
        return $this->cores;
    }

    public function setCores(int $cores): self
    {
        $this->cores = $cores;

        return $this;
    }

    public function getMemory(): ?int
    {
        return $this->memory;
    }

    public function setMemory(int $memory): self
    {
        $this->memory = $memory;

        return $this;
    }

    public function isPowerstate(): ?bool
    {
        return $this->powerstate;
    }

    public function setPowerstate(bool $powerstate): self
    {
        $this->powerstate = $powerstate;

        return $this;
    }

    public function isIsMonitored(): ?bool
    {
        return $this->is_monitored;
    }

    public function setIsMonitored(bool $is_monitored): self
    {
        $this->is_monitored = $is_monitored;

        return $this;
    }

    public function getSourceOs(): ?string
    {
        return $this->source_os;
    }

    public function setSourceOs(?string $source_os): self
    {
        $this->source_os = $source_os;

        return $this;
    }

    public function getSourceIp(): ?string
    {
        return $this->source_ip;
    }

    public function setSourceIp(?string $source_ip): self
    {
        $this->source_ip = $source_ip;

        return $this;
    }

    public function getSourceDns(): ?string
    {
        return $this->source_dns;
    }

    public function setSourceDns(?string $source_dns): self
    {
        $this->source_dns = $source_dns;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getAdditionalIp(): ?string
    {
        return $this->additional_ip;
    }

    public function setAdditionalIp(?string $additional_ip): self
    {
        $this->additional_ip = $additional_ip;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(?string $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function getTools(): ?int
    {
        return $this->tools;
    }

    public function setTools(?int $tools): self
    {
        $this->tools = $tools;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeImmutable $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deleted_at;
    }

    public function setDeletedAt(?\DateTimeImmutable $deleted_at): self
    {
        $this->deleted_at = $deleted_at;

        return $this;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }
}

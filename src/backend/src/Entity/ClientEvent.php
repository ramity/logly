<?php

namespace App\Entity;

use App\Repository\ClientEventRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClientEventRepository::class)]
class ClientEvent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $message = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $source = null;

    #[ORM\Column(nullable: true)]
    private ?int $lineno = null;

    #[ORM\Column(nullable: true)]
    private ?int $colno = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $error = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?ErrorType $error_type = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?LLMResponse $llm_response = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $target = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $reason = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(?string $source): static
    {
        $this->source = $source;

        return $this;
    }

    public function getLineno(): ?int
    {
        return $this->lineno;
    }

    public function setLineno(?int $lineno): static
    {
        $this->lineno = $lineno;

        return $this;
    }

    public function getColno(): ?int
    {
        return $this->colno;
    }

    public function setColno(?int $colno): static
    {
        $this->colno = $colno;

        return $this;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function setError(?string $error): static
    {
        $this->error = $error;

        return $this;
    }

    public function getErrorType(): ?ErrorType
    {
        return $this->error_type;
    }

    public function setErrorType(?ErrorType $error_type): static
    {
        $this->error_type = $error_type;

        return $this;
    }

    public function getLlmResponse(): ?LLMResponse
    {
        return $this->llm_response;
    }

    public function setLlmResponse(?LLMResponse $llm_response): static
    {
        $this->llm_response = $llm_response;

        return $this;
    }

    public function getTarget(): ?string
    {
        return $this->target;
    }

    public function setTarget(?string $target): static
    {
        $this->target = $target;

        return $this;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(?string $reason): static
    {
        $this->reason = $reason;

        return $this;
    }
}

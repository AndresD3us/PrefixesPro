<?php

declare(strict_types=1);

namespace AndresD3us\libs\formapi;

use pocketmine\form\FormValidationException;

class CustomForm extends Form
{
    private array $labelMap          = [];
    private array $validationMethods = [];

    public function __construct(?callable $callable)
    {
        parent::__construct($callable);
        $this->data["type"]    = "custom_form";
        $this->data["title"]   = "";
        $this->data["content"] = [];
    }

    public function processData(mixed &$data): void
    {
        if ($data !== null && !is_array($data)) {
            throw new FormValidationException("Expected an array response, got " . gettype($data));
        }
        if (is_array($data)) {
            if (count($data) !== count($this->validationMethods)) {
                throw new FormValidationException("Expected array size " . count($this->validationMethods) . ", got " . count($data));
            }
            $new = [];
            foreach ($data as $i => $v) {
                $method = $this->validationMethods[$i] ?? null;
                if ($method === null) {
                    throw new FormValidationException("Invalid element $i");
                }
                if (!$method($v)) {
                    throw new FormValidationException("Invalid type for element " . $this->labelMap[$i]);
                }
                $new[$this->labelMap[$i]] = $v;
            }
            $data = $new;
        }
    }

    public function setTitle(string $title): void
    {
        $this->data["title"] = $title;
    }

    public function addLabel(string $text, ?string $label = null): void
    {
        $this->data["content"][]   = ["type" => "label", "text" => $text];
        $this->labelMap[]          = $label ?? count($this->labelMap);
        $this->validationMethods[] = static fn($v) => $v === null;
    }

    public function addInput(string $text, string $placeholder = "", ?string $default = null, ?string $label = null): void
    {
        $this->data["content"][]   = ["type" => "input", "text" => $text, "placeholder" => $placeholder, "default" => $default ?? ""];
        $this->labelMap[]          = $label ?? count($this->labelMap);
        $this->validationMethods[] = static fn($v) => is_string($v);
    }

    public function addToggle(string $text, ?bool $default = null, ?string $label = null): void
    {
        $content = ["type" => "toggle", "text" => $text];
        if ($default !== null) {
            $content["default"] = $default;
        }
        $this->data["content"][]   = $content;
        $this->labelMap[]          = $label ?? count($this->labelMap);
        $this->validationMethods[] = static fn($v) => is_bool($v);
    }

    public function addDropdown(string $text, array $options, ?int $default = null, ?string $label = null): void
    {
        $this->data["content"][]   = ["type" => "dropdown", "text" => $text, "options" => $options, "default" => $default];
        $this->labelMap[]          = $label ?? count($this->labelMap);
        $this->validationMethods[] = static fn($v) => is_int($v) && isset($options[$v]);
    }
}

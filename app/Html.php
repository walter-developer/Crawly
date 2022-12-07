<?php

namespace App;

class Html
{

    protected string $html = '';

    public function __construct(string $html = '')
    {
        $this->html =  $html;
    }

    public function setHtml(string $html): static
    {
        $this->html = $html;
        return $this;
    }

    public function getHtml(): string
    {
        return $this->html;
    }

    public function find(string $element = 'input'): array|null
    {
        return match (strtolower(trim($element))) {
            default => $this->findTagHtml($this->getHtml(), $element)
        };
    }

    public function input(string $type = 'text', bool $assoc = false): array|null
    {
        if ($assoc) {
            return match (strtolower(trim($type))) {
                'text' => $this->inputsTextToAssoc(
                    $this->findInputText($this->getHtml()) ?: []
                ),
                default => $this->inputsTextToAssoc(
                    $this->findInputText($this->getHtml()) ?: []
                )
            };
        }
        return match (strtolower(trim($type))) {
            'text' => $this->findInputText($this->getHtml()),
            default => $this->findInputText($this->getHtml())
        };
    }

    public function text(string $tag = 'span'): string|null
    {
        return match (strtolower(trim($tag))) {
            default => $this->findTextTagHtml($this->getHtml(), $tag)
        };
    }

    public function tag(string $tag = 'span'): string|null
    {
        return match (strtolower(trim($tag))) {
            default => $this->findTagHtml($this->getHtml(), $tag)
        };
    }

    protected function findInputText(string $html): array|null
    {
        $inputs = [];
        preg_match_all('/\<input([^\>]+)\>/i', $html, $inputs,   PREG_UNMATCHED_AS_NULL);
        $inputs = $inputs ?: [];
        $inputs = array_shift($inputs);
        return count($inputs) ? $inputs : null;
    }

    protected function findTagHtml(string $html, string $tag): string|null
    {
        preg_match("#<$tag(.*?)\/$tag\>#", $html, $matches);
        $tagHtml = "<$tag" . ($matches[1] ?? '') . "/$tag>";
        return count($matches) ? $tagHtml : null;
    }

    protected function findTextTagHtml(string $html, string $tag): string|null
    {
        preg_match("#<$tag(.*?)\/$tag\>#", $html, $matches);
        $tagHtml = $matches[0] ?? '';
        return empty($tagHtml) ? null : $tagHtml;
    }

    public function inputsTextToAssoc(array $inputs = []): array
    {
        $form = [];
        $inputs = count($inputs) ? $inputs : ($this->find('input') ?: []);
        array_walk_recursive($inputs, function ($input) use (&$form) {
            preg_match('/name(\s?+)\=(\s?+)\"([^"]+)\"(\s?+)/i', $input, $matchsName);
            $matchsName = array_filter($matchsName, fn ($name) => strlen(trim($name ?: '')));
            $name = end($matchsName);
            preg_match('/value(\s?+)\=(\s?+)\"([^"]+)\"(\s?+)/i', $input, $matchsValue);
            $matchsValue = array_filter($matchsValue, fn ($value) => strlen(trim($value ?: '')));
            $value = end($matchsValue);
            if ($name && $value && strlen($name) && strlen($value)) {
                $form[$name] = $value;
            }
        });
        return $form;
    }
}

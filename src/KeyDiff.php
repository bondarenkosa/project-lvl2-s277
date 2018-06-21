<?php

namespace Gendiff;

class KeyDiff
{
    private $name;
    private $firstValue;
    private $secondValue;
    private $status;

    public function __construct(string $key, array $firstCollection, array $secondCollection)
    {
        $this->name = $key;
        $this->firstValue = isset($firstCollection[$key]) ? $firstCollection[$key] : null;
        $this->secondValue = isset($secondCollection[$key]) ? $secondCollection[$key] : null;
        $this->status = $this->calculateStatus($firstCollection, $secondCollection);
    }

    public function __toString()
    {
        $firstValue = is_string($this->firstValue) ? $this->firstValue : json_encode($this->firstValue);
        $secondValue = is_string($this->secondValue) ? $this->secondValue : json_encode($this->secondValue);
        switch ($this->status) {
            case "added":
                return "  + {$this->name}: {$secondValue}";
            case "deleted":
                return "  - {$this->name}: {$firstValue}";
            case "changed":
                return "  + {$this->name}: {$secondValue}" . PHP_EOL
                    . "  - {$this->name}: {$firstValue}";
            case "unchanged":
                return "    {$this->name}: {$firstValue}";
        }
    }

    private function calculateStatus($firstCollection, $secondCollection)
    {
        if (!array_key_exists($this->name, $firstCollection)) {
            return "added";
        }
        if (!array_key_exists($this->name, $secondCollection)) {
            return "deleted";
        }

        return $firstCollection[$this->name] === $secondCollection[$this->name] ? "unchanged" : "changed";
    }
}

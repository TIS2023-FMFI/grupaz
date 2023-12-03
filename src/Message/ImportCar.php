<?php

namespace App\Message;

class ImportCar
{
    private string $vis;
    private ?string $gid;
    private ?string $destination;

    public function __construct(string $vis, ?string $gid, ?string $destination)
    {
        $this->vis = $vis;
        $this->gid = $gid;
        $this->destination = $destination;
    }

    public function getVis(): string
    {
        return $this->vis;
    }

    public function getGid(): ?string
    {
        if($this->gid === '')
        {
            return null;
        }
        return $this->gid;
    }

    public function getDestination(): ?string
    {
        if($this->destination === '')
        {
            return null;
        }
        return $this->destination;
    }
}
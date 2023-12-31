<?php

namespace App\Message;

class ImportCar
{
    private string $vis;
    private ?string $gid;
    private ?string $destination;
    private ?string $receiver;

    public function __construct(string $vis, ?string $gid, ?string $destination, ?string $receiver)
    {
        $this->vis = $vis;
        $this->gid = $gid;
        $this->destination = $destination;
        $this->receiver = $receiver;
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
    public function getReceiver(): ?string
    {
        if($this->receiver === '')
        {
            return null;
        }
        return $this->receiver;
    }
}
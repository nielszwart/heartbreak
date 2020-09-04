<?php

namespace Heartbreak;

class Player
{
    public $name;
    public $cards = [];
    public $score = 0;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getRandomCard() : Card
    {
        return $this->cards[array_rand($this->cards)];
    }
}

<?php

namespace Heartbreak;

class Card
{
    public $suit;
    public $value;
    public $rank;
    public $score = 0;

    public function __construct(string $suit, string $value)
    {
        $this->suit = $suit;
        $this->value = $value;
        $this->setRank();
        $this->setScore();
    }

    public function setRank() : void
    {
        switch ($this->value) {
            case '7':
                $this->rank = 1;
                break;
            case '8':
                $this->rank = 2;
                break;
            case '9':
                $this->rank = 3;
                break;
            case '10':
                $this->rank = 4;
                break;
            case 'J':
                $this->rank = 5;
                break;
            case 'Q':
                $this->rank = 6;
                break;
            case 'K':
                $this->rank = 7;
                break;
            case 'A':
                $this->rank = 8;
                break;
        }
    }

    public function setScore() : void
    {
        if ($this->suit === 'hearts') {
            $this->score = 1;
        }

        if ($this->suit === 'clubs' && $this->value === 'J') {
            $this->score = 2;
        }

        if ($this->suit === 'spades' && $this->value === 'Q') {
            $this->score = 5;
        }
    }
}

<?php

namespace Heartbreak;

class Game
{
    private $names = ['Huub', 'Niels', 'Barack', 'Vladimir'];
    private $cardValues = ['7', '8', '9', '10', 'J', 'Q', 'K', 'A'];
    private $suits = ['hearts', 'clubs', 'spades', 'diamonds'];
    private $numberOfPlayers = 4;

    public $players = [];
    public $deck = [];
    public $playedCards = [];
    public $round = 0;
    public $echoMessage;

    public function __construct(bool $echoMessage = true)
    {
        $this->createPlayers();
        $this->createAndShuffleDeck();
        $this->echoMessage = $echoMessage;
    }

    public function createPlayers() : void
    {
        for ($i = 0; $i < $this->numberOfPlayers; $i++) {
            $this->players[] = new Player($this->names[$i]);
        }

        shuffle($this->players);
    }

    public function createAndShuffleDeck() : void
    {
        foreach ($this->suits as $suit) {
            foreach ($this->cardValues as $cardValue) {
                $this->deck[] = new Card($suit, $cardValue);
            }
        }

        shuffle($this->deck);
    }

    public function play() : void
    {
        $this->echoMessage('<p>Starting a game</p>');
        $this->distributeCards();

        $round = 1;
        while ($this->noLoser()) {
            $this->round = $round;

            $turn = 1;
            foreach ($this->players as $player) {
                if ($turn === 1) {
                    $this->echoMessage('<p style="font-weight: bold;">Round ' . $round . ': ' . $player->name . ' starts the round.</p>');
                }
                $this->playCard($player);
                $turn++;
            }

            $loser = $this->determineRoundLoser();
            $this->addScores($loser);
            $this->setLoserAsStarter($loser);

            if (count($loser->cards) === 0 && $this->noLoser()) {
                $this->shuffleAndDistributeDeck();
            }

            $round++;
        }

        $this->endGame($loser);
    }

    public function distributeCards() : void
    {
        foreach ($this->deck as $key => $card) {
            $this->players[$key % $this->numberOfPlayers]->cards[] = $card;
            unset($this->deck[$key]);
        }

        foreach ($this->players as $player) {
            $msg = $player->name . ' has been dealt: ';
            foreach ($player->cards as $card) {
                $msg .= $this->suitsMapper($card->suit) . $card->value . ' - ';
            }
            $msg .= '<br>';
            $this->echoMessage($msg);
        }
    }

    public function playCard(Player $player) : void
    {
        if (empty($this->playedCards[$this->round])) {
            $this->playedCards[$this->round] = [];
            $this->moveCardToTable($player, $player->getRandomCard());

            return;
        }

        $lastPlayedCard = end($this->playedCards[$this->round])['card'];
        $card = null;
        foreach ($player->cards as $cardInHand) {
            if ($cardInHand->suit === $lastPlayedCard->suit) {
                if ($card === null) {
                    $card = $cardInHand;
                    continue;
                }

                if ($card->rank > $cardInHand->rank) {
                    $card = $cardInHand;
                }
            }
        }

        if ($card === null) {
            $card = $player->getRandomCard();
        }

        $this->moveCardToTable($player, $card);
    }

    public function determineRoundLoser() : Player
    {
        $turn = 0;
        foreach ($this->getCardsPlayedThisRound() as $played) {
            $turn++;

            if ($turn === 1) {
                $playToMatch = $played;
                continue;
            }

            if ($playToMatch['card']->suit === $played['card']->suit && $playToMatch['card']->rank < $played['card']->rank) {
                $playToMatch = $played;
            }
        }

        $this->echoMessage('<br>' . $playToMatch['player']->name . ' played ' . $this->suitsMapper($playToMatch['card']->suit) . $playToMatch['card']->value . ' as the highest matching card');

        return $playToMatch['player'];
    }

    public function addScores(Player $loser) : void
    {
        $roundScore = 0;
        foreach ($this->getCardsPlayedThisRound() as $played) {
            $roundScore += $played['card']->score;
        }

        $loser->score += $roundScore;

        $this->echoMessage(' and got ' . $roundScore . ' added to his total score. ' . $loser->name . "'s total score is " . $loser->score);
    }

    public function setLoserAsStarter(Player $loser) : void
    {
        foreach ($this->players as $key => $player) {
            if ($player === $loser) {
                $playersToMove = array_slice($this->players, 0, $key);
                array_splice($this->players, 0, $key);
                $this->players = array_merge($this->players, $playersToMove);
                break;
            }
        }
    }

    public function shuffleAndDistributeDeck() : void
    {
        $this->createAndShuffleDeck();
        $this->echoMessage('<p>Players ran out of cards. Reshuffled the cards.</p>');
        $this->distributeCards();
    }

    public function noLoser() : bool
    {
        foreach ($this->players as $player) {
            if ($player->score >= 50) {
                return false;
            }
        }

        return true;
    }

    public function endGame(Player $loser) : void
    {
        $msg = $loser->name . ' loses the game!<br>Final scores:<br>';
        foreach ($this->players as $player) {
            $msg .= $player->name . ': ' . $player->score . '<br>';
        }
        $this->echoMessage($msg);
    }

    private function getCardsPlayedThisRound() : array
    {
        return $this->playedCards[$this->round];
    }

    private function moveCardToTable(Player $player, Card $card) : void
    {
        foreach ($player->cards as $key => $cardInHand) {
            if ($cardInHand === $card) {
                unset($player->cards[$key]);
                $player->cards = array_values($player->cards);
                break;
            }
        }

        $this->echoMessage($player->name . ' plays: ' . $this->suitsMapper($card->suit) . $card->value . '<br>');

        $this->playedCards[$this->round][] = [
            'player' => $player,
            'card' => $card,
        ];
    }

    private function suitsMapper(string $suit) : string
    {
        switch ($suit) {
            case 'hearts':
                return '&hearts;';
                break;
            case 'clubs':
                return '&clubs;';
                break;
            case 'spades':
                return '&spades;';
                break;
            case 'diamonds':
                return '&diams;';
                break;
        }

        return '?';
    }

    private function echoMessage(string $message) : void
    {
        if (!$this->echoMessage) {
            return;
        }

        echo $message;
    }
}

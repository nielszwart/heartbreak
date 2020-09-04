<?php

use PHPUnit\Framework\TestCase;
use Heartbreak\Game;
use Heartbreak\Card;
use Heartbreak\Player;

class HeartbreakTest extends TestCase
{
    public function testCards(): void
    {
        $heartsTen = new Card('hearts', '10');
        $this->assertEquals($heartsTen->suit, 'hearts');
        $this->assertEquals($heartsTen->value, '10');
        $this->assertEquals($heartsTen->score, 1);

        $clubsJack = new Card('clubs', 'J');
        $this->assertEquals($clubsJack->suit, 'clubs');
        $this->assertEquals($clubsJack->value, 'J');
        $this->assertEquals($clubsJack->score, 2);

        $spadesQueen = new Card('spades', 'Q');
        $this->assertEquals($spadesQueen->suit, 'spades');
        $this->assertEquals($spadesQueen->value, 'Q');
        $this->assertEquals($spadesQueen->score, 5);

        $this->assertTrue($heartsTen->rank < $clubsJack->rank);
        $this->assertTrue($clubsJack->rank < $spadesQueen->rank);
    }

    public function testPlayer(): void
    {
        $player = new Player('Boris');
        $this->assertEquals($player->name, 'Boris');
        $this->assertEquals($player->score, 0);
        $this->assertEmpty($player->cards);
    }

    public function testGame(): void
    {
        $game = new Game(false);
        $this->assertEquals(32, count($game->deck));
        $this->assertEquals(4, count($game->players));
        $this->assertTrue($game->noLoser());

        $game->play();
        $this->assertFalse($game->noLoser());
    }
}


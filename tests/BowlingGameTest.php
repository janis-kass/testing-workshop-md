<?php

use PF\BowlingGame;
use PF\Exceptions\BowlingGameException;
use PHPUnit\Framework\TestCase;

class BowlingGameTest extends TestCase
{
    public function testGetScore_withAllZeros_getZeroScore()
    {
        // set up
        $game = new BowlingGame();
        for ($i = 0; $i < 20; $i++) {
            $game->roll(0);
        }
        // test
        $score = $game->getScore();
        // assert
        self::assertEquals(0, $score);
    }

    public function testGetScore_withAllOnes_get20asScore()
    {
        // set up
        $game = new BowlingGame();
        for ($i = 0; $i < 20; $i++) {
            $game->roll(1);
        }
        // test
        $score = $game->getScore();
        // assert
        self::assertEquals(20, $score);
    }

    public function testgetScore_withASpare_returnsScoreWithSpareBonus()
    {
        // set up
        $game = new BowlingGame();
        $game->roll(2);
        $game->roll(8);
        $game->roll(5);
        // 2 + 8 + 5 (spare bonus) + 5 + 17
        for ($i = 0; $i < 17; $i++) {
            $game->roll(1);
        }
        // test
        $score = $game->getScore();
        // assert
        self::assertEquals(37, $score);
    }

    public function testGetScore_withAStrike_addsStrikeBonus()
    {
        // set up
        $game = new BowlingGame();
        $game->roll(10);
        $game->roll(5);
        $game->roll(3);
        // 10 + 5 (bonus) + 3 (bonus) + 5 + 3 + 16
        for ($i = 0; $i < 16; $i++) {
            $game->roll(1);
        }
        // test
        $score = $game->getScore();
        // assert
        self::assertEquals(42, $score);
    }

    public function testGetScore_withPerfectGame_returns300()
    {
        // set up
        $game = new BowlingGame();
        for ($i = 0; $i < 12; $i++) {
            $game->roll(10);
        }
        // test
        $score = $game->getScore();
        // assert
        self::assertEquals(300, $score);
    }

    //Illegal cases

    public function testRoll_withNegativeScore_returnsInvalidArgumentException()
    {
        $this->expectException(InvalidArgumentException::class);

        $game = new BowlingGame();
        $game->roll(-2);
    }

    public function testRoll_withStringScore_returnsError()
    {
        $this->expectException(Error::class);

        $game = new BowlingGame();
        $game->roll('asdf');
    }

    public function testRoll_21NormalRolls_returnsBowlingGameException()
    {
        $this->expectException(BowlingGameException::class);
        $this->expectExceptionMessage("Game is over, can't have more rolls");

        $game = new BowlingGame();
        for ($i = 0; $i < 21; $i++) {
            $game->roll(3);
        }
    }

    public function testRoll_13Strikes_returnsBowlingGameException()
    {
        $this->expectException(BowlingGameException::class);
        $this->expectExceptionMessage("Game is over, can't have more rolls");

        $game = new BowlingGame();
        for ($i = 0; $i < 13; $i++) {
            $game->roll(10);
        }
    }

    public function testRoll_Invalid3rdRollOn10thFrame_returnsBowlingGameException()
    {
        $this->expectException(BowlingGameException::class);
        $this->expectExceptionMessage("Game is over, can't have more rolls");

        // set up
        $game = new BowlingGame();
        for ($i = 0; $i < 20; $i++) {
            $game->roll(3);
        }
        //test
        $game->roll(3);
    }
}
<?php

namespace PF;

use InvalidArgumentException;
use PF\Exceptions\BowlingGameException;

class BowlingGame
{
    public array $rolls = [];
    public int $currentFrame = 1;
    public int $rollInFrame = 1;

    /**
     * @param int $points
     * @throws BowlingGameException
     */
    public function roll(int $points): void
    {
        if ($points < 0) {
            throw new InvalidArgumentException();
        }
        if ($this->currentFrame > 10) {
            throw new BowlingGameException("Game is over, can't have more rolls");
        }

        $this->rolls[] = $points;

        if ($this->shouldGoToNextFrame($points)) {
            $this->rollInFrame = 1;
            $this->currentFrame++;
        } else {
            $this->rollInFrame++;
        }
    }

    public function getScore(): int
    {
        $score = 0;
        $roll = 0;
        for ($frame = 0; $frame < 10; $frame++) {
            if ($this->isStrike($roll)) {
                $score += $this->getScoreForStrike($roll);
                $roll++;
                continue;
            }
            if ($this->isSpare($roll)) {
                $score += $this->getSpareBonus($roll);
            }
            $score += $this->getNormalScore($roll);
            $roll += 2;
        }
        return $score;
    }

    /**
     * @param int $roll
     * @return int
     */
    private function getNormalScore(int $roll): int
    {
        return $this->rolls[$roll] + $this->rolls[$roll + 1];
    }

    /**
     * @param int $roll
     * @return bool
     */
    private function isSpare(int $roll): bool
    {
        return $this->getNormalScore($roll) === 10;
    }

    /**
     * @param int $roll
     * @return int
     */
    private function getSpareBonus(int $roll): int
    {
        return $this->rolls[$roll + 2];
    }

    /**
     * @param int $roll
     * @return bool
     */
    private function isStrike(int $roll): bool
    {
        return $this->rolls[$roll] === 10;
    }

    /**
     * @param int $roll
     * @return int
     */
    private function getScoreForStrike(int $roll): int
    {
        return 10 + $this->rolls[$roll + 1] + $this->rolls[$roll + 2];
    }

    /**
     * @param int $points
     * @return bool
     */
    private function shouldGoToNextFrame(int $points): bool
    {
        return ($points === 10 && $this->currentFrame !== 10) || ($this->rollInFrame === 2 && !$this->isExtraLastRoll()) || ($this->currentFrame === 10 && $this->rollInFrame === 3);
    }

    /**
     * @return bool
     */
    private function isExtraLastRoll(): bool
    {
        $rollCount = count($this->rolls);
        if ($this->currentFrame === 10 && $this->rollInFrame === 2 && (array_sum([$this->rolls[$rollCount - 2], $this->rolls[$rollCount - 1]]) >= 10)) {
            return true;
        }
        return false;
    }
}
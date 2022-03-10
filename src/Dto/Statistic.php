<?php

namespace C4Y\Reviews\Dto;

class Statistic
{
    private float $rating;
    private int $numberOfReviews;
    private float $ratingInPercent;

    /**
     * @return float
     */
    public function getRating(): float
    {
        return $this->rating;
    }

    /**
     * @param float $rating
     */
    public function setRating(float $rating): void
    {
        $this->rating = $rating;
    }

    /**
     * @return int
     */
    public function getNumberOfReviews(): int
    {
        return $this->numberOfReviews;
    }

    /**
     * @param int $numberOfReviews
     */
    public function setNumberOfReviews(int $numberOfReviews): void
    {
        $this->numberOfReviews = $numberOfReviews;
    }

    /**
     * @return float
     */
    public function getRatingInPercent(): float
    {
        return $this->ratingInPercent;
    }

    /**
     * @param float $ratingInPercent
     */
    public function setRatingInPercent(float $ratingInPercent): void
    {
        $this->ratingInPercent = $ratingInPercent;
    }


}

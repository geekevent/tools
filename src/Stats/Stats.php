<?php


namespace App\Stats;


class Stats
{
    public function prepare(array $moments): array
    {

        $length = count($moments);
        if (0 === $length) {
            return [];
        }

        $this->formatMoment($moments[0]);
        if (1 === $length) {
            return $moments;
        }
        for($i = 1; $i < $length; $i++) {
            $this->formatMoment($moments[$i]);
            
            $previous = $moments[$i - 1];
            $moments[$i]['gauge'] += $previous['gauge'];
        }

        return $moments;
    }

    public function createDatePeriod(array $dates): \DatePeriod
    {
        return new \DatePeriod(
            new \DateTime($this->prepareDate($dates['minDate'])),
            new \DateInterval('P1D'),
            (new \DateTime($this->prepareDate($dates['maxDate'])))->modify('+1 day'),
        );
    }

    private function prepareDate(string $dateTime): string
    {
        return (new \DateTime($dateTime))->format('Y-m-d');
    }

    private function formatMoment(array &$moment): void
    {
        $moment['moment'] = sprintf('%sh%s', substr($moment['moment'], 0, 2), substr($moment['moment'], -2));
    }
}
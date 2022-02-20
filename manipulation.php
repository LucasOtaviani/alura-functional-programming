<?php

/* Class 1 */
$data = require "data.php";

$counter = count($data);
echo "Number of countries: $counter\n";

$brazil = $data[0];

function sumMedals(int $accumulatedMedals, $medals): int
{
    return $accumulatedMedals + $medals;
}

$numberOfMedals = array_reduce($brazil['medals'], 'sumMedals', 0);

echo "Number of medals: $numberOfMedals\n";

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/* Class 2 */
$countries = array_map(function (array $country) {
    $country['country'] = mb_convert_case($country['country'], MB_CASE_UPPER);
    return $country;
}, $data);

var_dump($countries);

/*--------------------------------------------------------------------------------------------------------------------*/

echo "\n";
echo "Converts all country names to uppercase.";
echo "\n";

function convertCountryToUpperCase(array $country): array
{
    $country['country'] = mb_convert_case($country['country'], MB_CASE_UPPER);
    return $country;
}

$countries = array_map('convertCountryToUpperCase', $data);

var_dump($countries);

/*--------------------------------------------------------------------------------------------------------------------*/

echo "\n";
echo "Checks if country has space in the name.";
echo "\n";

function checksCountryHasSpaceInTheName(array $country): bool
{
    return str_contains($country['country'], ' ');
}

$countries = array_map('convertCountryToUpperCase', $data);
$countries = array_filter($data, 'checksCountryHasSpaceInTheName');

var_dump($countries);

/*--------------------------------------------------------------------------------------------------------------------*/

echo "\n";
echo "Count all medals";
echo "\n";

function accumulatedMedals(int $accumulatedMedals, array $country): int
{
    return $accumulatedMedals + array_reduce($country['medals'], 'sumMedals', 0);
}

$countOfMedals = array_reduce($data, 'accumulatedMedals', 0);

echo $countOfMedals;

/*--------------------------------------------------------------------------------------------------------------------*/

echo "\n";
echo "Applying MapReduce";
echo "\n";

$medals = array_reduce(
    array_map(function (array $medals) {
        return array_reduce($medals, 'sumMedals', 0);
    }, array_column($data, 'medals')),
    'sumMedals',
    0
);

echo $medals;

/*--------------------------------------------------------------------------------------------------------------------*/

echo "\n";
echo "Applying Sort";
echo "\n";

usort($data, function (array $firstCountry, array $secondCountry) {
    $firstCountryMedals = $firstCountry['medals'];
    $secondCountryMedals = $secondCountry['medals'];

    $compareGold = $secondCountryMedals['gold'] <=> $firstCountryMedals['gold'];
    $compareSilver = $secondCountryMedals['silver'] <=> $firstCountryMedals['silver'];
    $compareBronze = $secondCountryMedals['bronze'] <=> $firstCountryMedals['bronze'];

    return $compareGold !== 0 ? $compareGold : ($compareSilver !== 0 ? $compareSilver : $compareBronze);
});

var_dump($data);
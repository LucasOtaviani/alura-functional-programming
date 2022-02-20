<?php

require_once 'vendor/autoload.php';

/* Class 1 */
$data = require "data.php";

$counter = count($data);
echo "Number of countries: $counter\n";

$brazil = $data[0];

$sumMedals = fn (int $accumulatedMedals, int $medals): int => $accumulatedMedals + $medals;
$numberOfMedals = array_reduce($brazil['medals'], $sumMedals, 0);

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

$checksCountryHasSpaceInTheName = fn (array $country): bool => str_contains($country['country'], ' ');

$countries = array_map('convertCountryToUpperCase', $data);
$countries = array_filter($data, $checksCountryHasSpaceInTheName);

var_dump($countries);

/*--------------------------------------------------------------------------------------------------------------------*/

echo "\n";
echo "Count all medals";
echo "\n";

$accumulatedMedals = fn (int $accumulatedMedals, array $country)
    => $accumulatedMedals + array_reduce($country['medals'], $sumMedals, 0);

$countOfMedals = array_reduce($data, $accumulatedMedals, 0);

echo $countOfMedals;

/*--------------------------------------------------------------------------------------------------------------------*/

echo "\n";
echo "Applying MapReduce";
echo "\n";

$medals = array_reduce(
    array_map(
        fn (array $medals) => array_reduce($medals, $sumMedals, 0),
        array_column($data, 'medals')
    ),
    $sumMedals,
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

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/* Class 3 */

echo "\n";
echo "Refactor sort with HOF (High-Order Functions) using currying";
echo "\n";

function compareMedals(array $firstCountryMedals, array $secondCountryMedals): callable
{
    return fn (string $modality): int => $secondCountryMedals[$modality] <=> $firstCountryMedals[$modality];
}

usort($data, function (array $firstCountry, array $secondCountry) {
    $firstCountryMedals = $firstCountry['medals'];
    $secondCountryMedals = $secondCountry['medals'];

    $comparator = compareMedals($firstCountryMedals, $secondCountryMedals);

    return $comparator('gold') !== 0 ? $comparator('gold')
        : ($comparator('silver') !== 0 ? $comparator('silver')
        : $comparator('bronze'));
});

var_dump($data);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/* Class 4 */

$countryNamesToUpperCase = fn ($data) => array_map('convertCountryToUpperCase', $data);
$filterCountryNamesWithoutSpace = fn ($data) => array_filter($data, $checksCountryHasSpaceInTheName);

$data = $countryNamesToUpperCase($data);
$data = $filterCountryNamesWithoutSpace($data);

function pipe(callable ...$callbacks): callable
{
    return fn ($value) => array_reduce($callbacks, fn ($value, callable $callback) => $callback($value), $value);
}

$functions = pipe($countryNamesToUpperCase, $filterCountryNamesWithoutSpace);
$data = $functions($data);

echo "\nPIPE:\n";
var_dump($data);

/*--------------------------------------------------------------------------------------------------------------------*/

$countryNamesToUpperCase = fn ($data) => array_map('convertCountryToUpperCase', $data);
$filterCountryNamesWithoutSpace = fn ($data) => array_filter($data, $checksCountryHasSpaceInTheName);

$data = $countryNamesToUpperCase($data);
$data = $filterCountryNamesWithoutSpace($data);

$functions = \igorw\pipeline($countryNamesToUpperCase, $filterCountryNamesWithoutSpace);
$data = $functions($data);

echo "\nPIPE OF PACKAGE:\n";
var_dump($data);

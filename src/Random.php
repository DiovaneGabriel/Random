<?php

namespace DBarbieri\Random;

use Exception;
use Faker;

class Random
{

    private $probability;
    private $parameters;

    public function __construct($prob, array $params)
    {

        if (!is_numeric($prob)) {
            throw new Exception("Parameter '$prob' must be numeric!");
        }

        $this->probability = $prob;
        $this->parameters = $params;
    }

    public function getProbability()
    {
        return $this->probability;
    }
    public function getParameters()
    {
        return $this->parameters;
    }

    public static function getFaker($language = 'pt_BR')
    {
        return Faker\Factory::create($language);
    }

    public static function makeProbabilities(array $params)
    {
        $return = [];
        foreach ($params as $param) {
            $return[] = new self($param[0], is_array($param[1]) ? $param[1] : [$param[1]]);
        }

        return $return;
    }

    public static function generate($function, array $probabilities)
    {

        if (!is_callable($function) && !method_exists(self::class, $function)) {
            throw new Exception("Function " . $function . " does'n exists!");
        }

        $sum = 0;
        foreach ($probabilities as $probability) {
            if ($probability instanceof self) {
                $sum += $probability->getProbability();
            } else {
                throw new Exception("This function needs a array of Probabilities!");
            }
        }

        $num = mt_rand(1, $sum);

        $sum = 0;
        foreach ($probabilities as $probability) {
            $sum += $probability->getProbability();
            if ($sum >= $num) {
                if (is_string($function)) {
                    return call_user_func_array([self::class, $function], $probability->getParameters());
                } else {
                    return call_user_func_array($function, $probability->getParameters());
                }
            }
        }

        return null;
    }

    public static function boolean($chanceToTrue = 50)
    {
        return mt_rand(1, 100) <= $chanceToTrue;
    }

    public static function uniqueCode()
    {
        return mt_rand(100, 999) . number_format(microtime(true) * 1000000, 0, '.', '');
    }

    public static function code($length = 1)
    {
        return mt_rand(pow(10, $length - 1), pow(10, $length) - 1);
    }

    public static function int($min, $max)
    {
        return mt_rand($min, $max);
    }

    public static function date($start = null, $end = null)
    {
        $start = strtotime($start ? $start : '2000-01-01');
        $end = strtotime($end ? $end : date('Y-m-d'));

        return date('Y-m-d', mt_rand($start, $end));
    }

    public static function time($start = null, $end = null)
    {
        $start = strtotime('2000-01-01 ' . ($start ? $start : '00:00') . ":00");
        $end = strtotime('2000-01-01 ' . ($end ? $end : '23:59') . ":59");

        return date('H:i:s', mt_rand($start, $end));
    }

    public static function float($min, $max, $precision = 2)
    {
        $precision = pow(10, $precision);
        return mt_rand($min, $max * $precision) / $precision;
    }

    public static function money($min, $max, $multipleOf = null)
    {
        $value = self::float($min, $max, is_numeric($multipleOf) ? 0 : 2);
        if (is_numeric($multipleOf) && $multipleOf > 0) {
            $value = round($value / $multipleOf) * $multipleOf;
        }

        return $value;
    }

    public static function janKenPon(array $params)
    {
        return Random::generate(function ($value) {
            return $value;
        }, Random::makeProbabilities($params));
    }

    public static function dice(array $values)
    {
        $countValues = $values ? count($values) : 0;
        $values = $countValues ? array_values($values) : null;
        return $countValues ? $values[mt_rand(0, $countValues - 1)] : false;
    }

    public static function name($country = null)
    {
        $faker = self::getFaker($country ? $country : "pt_BR");
        return preg_replace('/(Sr\.|Sra\.|Dr\.|Mr\.|Mrs\.|Dra\.|Srta\.)\s/', '', $faker->name);
    }

    public static function cpf()
    {
        $faker = self::getFaker("pt_BR");
        return preg_replace('/[^0-9]/', '', $faker->cpf);
    }

    public static function cep()
    {
        $faker = self::getFaker("pt_BR");
        return preg_replace('/[^0-9]/', '', $faker->postcode);
    }

    public static function uf()
    {
        $ufs = [
            'AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA',
            'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN',
            'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'
        ];
        return $ufs[self::int(0, count($ufs) - 1)];
    }
}

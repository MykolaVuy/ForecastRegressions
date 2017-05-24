<?php

/**
 * ForecastRegressionsTrait trait
 *
 * @author  Mykola Vuy  <mykola.vuy@gmail.com>
 */

trait ForecastRegressionsTrait
{
    /**
     * Calculate forecast use Linear Regression algorithm
     *
     * @param    array      $data        Set parameters period => values | null
     * @return    array      $data        Returns parameters period => values | result
     */
    
    public function countLinearRegression(array $data)
    {
        $buffArray = array_diff($data, array(''));
        $n = count($buffArray);

        if($n < 3) return $data;

        $xSumm = array_sum(array_keys($buffArray));
        $ySumm = array_sum($buffArray);

        $xSummSquare = 0;
        $xySumm = 0;

        foreach ($buffArray as $key => $value) {
            $xSummSquare += pow($key, 2);
            $xySumm += $key * $value;
        }

        $aCoef = ($xSumm * $ySumm - $n * $xySumm) / (pow($xSumm, 2) - $n * $xSummSquare);
        $bCoef = ($xSumm * $xySumm - $xSummSquare * $ySumm) / (pow($xSumm, 2) - $n * $xSummSquare);

        foreach ($data as $key => $value) {
            if($value) continue;
            $data[$key] = round($aCoef * $key + $bCoef, 0);
        }

        return $data;
    }
    
    /**
     * Calculate forecast use Power Regression algorithm
     *
     * @param   array      $data        Set parameters period => values | null
     * @return  array      $data        Returns parameters period => values | result
     */

    public function countPowerRegression(array $data)
    {
        $buffArray = array_diff($data, array(''));
        $n = count($buffArray);

        if ($n < 3) return $data;

        $xSumm = array_sum(array_keys($buffArray));
        $ySumm = array_sum($buffArray);

        $logXSumm = 0;
        $logXSummSquare = 0;
        $logYSumm = 0;
        $logYXSumm = 0;

        foreach ($buffArray as $key => $value) {
            $logX = log($key);
            $logY = log($value);
            $logXSumm += $logX;
            $logXSummSquare += pow($logX, 2);
            $logYSumm += $logY;
            $logYXSumm += $logX * $logY;
        }

        $bCoef = ($n * $logYXSumm - $logXSumm * $logYSumm) / ($n * $logXSummSquare - pow($logXSumm, 2));

        $aCoef = exp(1 / $n * $logYSumm - $bCoef / $n * $logXSumm);

        foreach ($data as $key => $value) {
            if($value) continue;
            $data[$key] = round($aCoef * pow($key, $bCoef), 0);
        }

        return $data;
    }
    
    /**
     * Calculate forecast use Logarithmic Regression algorithm
     *
     * @param   array      $data        Set parameters period => values | null
     * @return  array      $data        Returns parameters period => values | result
     */

    public function countLogarithmicRegression(array $data)
    {
        $buffArray = array_diff($data, array(''));
        $n = count($buffArray);

        if ($n < 3) return $data;

        $xSumm = array_sum(array_keys($buffArray));
        $ySumm = array_sum($buffArray);
        
        $logXSumm = 0;
        $logXSummSquare = 0;
        $yLogXSumm = 0;

        foreach ($buffArray as $key => $value) {
            $logX = log($key);
            $logXSumm += $logX;
            $logXSummSquare += pow($logX, 2);
            $yLogXSumm += $value * $logX;
        }

        $bCoef = ($n * $yLogXSumm - $logXSumm * $ySumm) / ($n * $logXSummSquare - pow($logXSumm, 2));

        $aCoef = 1 / $n * $ySumm - $bCoef / $n * $logXSumm;

        foreach ($data as $key => $value) {
            if($value) continue;
            $data[$key] = round($aCoef + $bCoef * log($key), 0);
        }

        return $data;
    }
    
}

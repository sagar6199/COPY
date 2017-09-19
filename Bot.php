$data =  Bittrex::getChartData('BTC-BLK', $tickInterval='hour');

foreach ($data['result'] as $d){
    $clean[] = $d['C'];
    $clean2[] = $d['C'];
}

#---------------------MACD----------------#
    $macd = trader_macd($clean, 12, 26, 9);
    $macd_raw = $macd[0];
    $signal   = $macd[1];
    $hist     = $macd[2];

    if(!$macd || !$macd_raw){
        return 0;
    }

    $macd = (array_pop($macd_raw) - array_pop($signal));

    # Close position for the pair when the MACD signal is negative
    if ($macd < 0) {
        //return -1;
        $rmacd = -1;
        # Enter the position for the pair when the MACD signal is positive
    } elseif ($macd > 0) {
        $rmacd = 1;
    } else {
        $rmacd = 0;
    }
#---------------------CLOSE----------------#
#---------------------BBAND----------------#

    $current = array_pop($clean2);
    $bbands = trader_bbands($clean2, 20, 2);

    $upper  = $bbands[0];
    $lower  = $bbands[2];

    # If price is below the recent lower band
    if ($current <= array_pop($lower)) {
        $rbb = 1; // buy long
        # If price is above the recent upper band
    } elseif ($current >= array_pop($upper)) {
        $rbb = -1; // sell (or short)
    } else {
        $rbb = 0; // notta
    }
#---------------------CLOSE----------------#

if($rbb == 1 and $rmacd == 1){
    //execute order buy
    return 'buy';
} elseif($rbb == -1 and $rmacd == -1) {
    //execute order sell
    return 'sell';
} else {
    return 'nothing';
}
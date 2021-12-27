<?php //>

return function ($lng1, $lat1, $lng2, $lat2) {
    $theta = $lng1 - $lng2;
    $radtheta = deg2rad($theta);
    $radlat1 = deg2rad($lat1);
    $radlat2 = deg2rad($lat2);

    $dist = sin($radlat1) * sin($radlat2) + cos($radlat1) * cos($radlat2) * cos($radtheta);
    $dist = acos($dist);
    $dist = rad2deg($dist);

    $miles = $dist * 60 * 1.1515;
    $kilometers = $miles * 1.609344;

    return round($kilometers, 3);
};

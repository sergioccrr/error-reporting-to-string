<?php

require 'ErrorReportingToString.php';

$ers = new ErrorReportingToString('5.4');

echo '<pre>';
echo '22527 :: '. $ers->convert(22527) . PHP_EOL;
echo PHP_EOL;
echo '32759 :: '. $ers->convert(32759) . PHP_EOL;
echo '</pre>';

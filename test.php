<?php
$operator = '-';
$x=15;
$y=10;
 $num = $y . $operator . $x;
eval("\$num=$num;");
echo $num;
?>
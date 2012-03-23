<?php
class Y
{

	public static function declOfNum($number, $titles)
		{
    		$cases = array (2, 0, 1, 1, 1, 2);
    		$number=abs($number);
    		return $number."&nbsp;".$titles[ ($number%100>4 && $number%100<20)? 2 : $cases[min($number%10, 5)] ];
		}

	public function declOfNumArr($number, $titles)
		{
    		$cases = array (2, 0, 1, 1, 1, 2);
    		$number=abs($number);
    		return Array($number,$titles[ ($number%100>4 && $number%100<20)? 2 : $cases[min($number%10, 5)] ]);
		}
		
	public function dateFromTime($time)
		{
    		return date('d F Y',$time);
		}
	public function dateFromTimeShort($time)
		{
    		return date('d.m.Y H:i',$time);
		}		
	
}
?>
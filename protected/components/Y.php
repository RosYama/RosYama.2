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
    		return Yii::app()->dateFormatter->format('d MMMM yyyy', $time);
		}
	public function dateFromTimeShort($time)
		{
    		return date('d.m.Y H:i',$time);
		}		
		
	function recursiveRemDir($directory, $empty=FALSE)
	{
		 if(substr($directory,-1) == '/')
		 {
			 $directory = substr($directory,0,-1);
		 }
		 if(!file_exists($directory) || !is_dir($directory))
		 {
			 return FALSE;
		 }elseif(is_readable($directory))
		 {
			 $handle = opendir($directory);
			 while (FALSE !== ($item = readdir($handle)))
			 {
				 if($item != '.' && $item != '..')
				 {
					 $path = $directory.'/'.$item;
					 if(is_dir($path)) 
					 {
						 Y::recursiveRemDir($path);
					 }else{
						 unlink($path);
					 }
				 }
			 }
			 closedir($handle);
			 if($empty == FALSE)
			 {
				 if(!rmdir($directory))
				 {
					 return FALSE;
				 }
			 }
		 }
     return TRUE;
	}	
	
}
?>
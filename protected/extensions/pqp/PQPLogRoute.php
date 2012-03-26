<?php
/**
 * PQPLogRoute displays the log results in Web page.
 *
 * PQPLogRoute is a Yii LogRoute that displays log entries in a fancy way
 * using the styles from PHP Quick Profiler by http://particletree.com/
 *
 * The memory profiling is done by calling {@link logMemory()}.
 *
 * @author AsgarothBelem <asgaroth[dot]belem[at]gmail[dot]com>
 * @version 0.1
 * @see http://particletree.com/features/php-quick-profiler/
 */
class PQPLogRoute extends CWebLogRoute{

	public $ignoreDbTraces = false;
	protected $stack=array();
	protected $speed=array();
	protected $db=array();
	protected $memory=array();
	/**
	 * @var boolean whether to aggregate results according to profiling tokens.
	 * If false, the results will be aggregated by categories.
	 * Defaults to true. Note that this property only affects the summary report
	 * that is enabled when {@link report} is 'summary'.
	 * @since 1.0.6
	 */
	public $groupByToken=true;

	protected $entries = array(
			'logs' => array(
				'console' => array(),
				'logCount' => 0,
            	'memoryCount' => 0,
            	'speedCount' => 0,
            	'errorCount' => 0
	),
			'files' => array(),
			'fileTotals' => array(),
			'memoryTotals' => array('used' => 0),
			'queries' => array(),
			'queryTotals' => array('count' => 0, 'time'=>0, 'duplicates' => 0),
			'speedTotals' => array(),
	);


	public function init(){
		$url = Yii::app()->assetManager->publish(dirname(__FILE__).'/assets');
		Yii::app()->clientScript->registerCssFile($url."/css/pqp.css");
		Yii::app()->clientScript->registerScriptFile($url."/js/pqp.js");
		$this->levels = 'error, trace, info, profile, warning, memory';//PQP displays all levels
		if($this->categories != '' && strpos($this->categories, 'system.db') === false){
			$this->categories .= ', system.db.*';
		}
	}
	/**
	 * Displays the log messages.
	 * @param array list of log messages
	 */
	public function processLogs($logs)
	{
		$app=Yii::app();
		if(!($app instanceof CWebApplication) || $app->getRequest()->getIsAjaxRequest()){
			return;
		}
		$this->process($logs);
	}

	/**
	 * Displays the callstack of the profiling procedures for display.
	 * @param array list of logs
	 */
	protected function process($logs)
	{
		foreach($logs as $log)
		{
			switch ($log[1]) {
				case 'memory':
					$this->processMemoryLog($log);
					break;
				case CLogger::LEVEL_PROFILE:
					$this->processProfileLog($log);
					break;
				case CLogger::LEVEL_ERROR:
					$this->processErrorLog($log);
					break;
				case CLogger::LEVEL_INFO:
				case CLogger::LEVEL_WARNING:
				case CLogger::LEVEL_TRACE:
				default:
					$this->processTraceLog($log);
					break;
			}

		}

		$this->entries['memoryTotals']['total'] = ini_get("memory_limit");
		$this->entries['memoryTotals']['used'] = $this->getReadableFileSize(Yii::getLogger()->getMemoryUsage());
		$this->entries['speedTotals']['total'] = $this->getReadableTime(Yii::getLogger()->getExecutionTime());
		$this->entries['speedTotals']['allowed'] = ini_get("max_execution_time");

		$this->gatherFileData();

		$this->emptyStack($this->speed);
		$this->emptyStack($this->db);

		$speed=array_values($this->speed);
		$db=array_values($this->db);

		$func=create_function('$a,$b','return $a[4]<$b[4]?1:0;');
		usort($speed,$func);
		usort($db,$func);
		$func=create_function('$a,$b','return $a["rawdata"]<$b["rawdata"]?1:0;');
		usort($this->memory,$func);

		$this->entries['profile'] = $speed;
		$this->entries['queries'] = $db;
		$this->entries['memory'] = $this->memory;


		$this->render('pqp',$this->entries);
	}

	protected function emptyStack(&$stack){
		$now=microtime(true);
		while(($last=array_pop($this->stack))!==null)
		{
			$delta=$now-$last[3];
			$token=$this->groupByToken ? $last[0] : $last[2];
			if(isset($stack[$token])){
				$stack[$token]=$this->aggregateResult($stack[$token],$delta);
			}
			else{
				$stack[$token]=array($token,1,$delta,$delta,$delta);
			}
		}
	}

	protected function processMemoryLog($log){
		list($message, $level, $category, $timestamp) = $log;
		if($message[0] == '[')
		{
			$pos = strpos($message, ']');
			$memory = substr(substr($message,1), 0, $pos-1);
			$message = substr($message,$pos+1);
			if($message[0] == '['){
				$pos = strpos($message, ']');
				$dataType = substr(substr($message,1), 0, $pos-1);
				$message = substr($message,$pos+1);
			}
			$this->entries['logs']['memoryCount']++;
			$data = array(
							'type' => 'memory',
							'data' => $this->getReadableFileSize($memory),
							'name' => $message,
							'dataType' => $dataType,
							'rawdata' => $memory,
			);
			$this->entries['logs']['console'][] = $data;
			$this->memory[] = $data;
		}
	}

	protected function processErrorLog($log){
		list($message, $level, $category, $timestamp) = $log;

		if($message instanceof Exception){
			$message = $message->getMessage();
		}
		$this->entries['logs']['console'][] = array(
			'type' => 'error',
			'data' => $message,
		);
		$this->entries['logs']['errorCount']++;
	}

	protected function processTraceLog($log){

		if($this->ignoreDbTraces && strpos($log[2], 'system.db') !== false){
			return;
		}
		list($message, $level, $category, $timestamp) = $log;

		$this->entries['logs']['console'][] = array(
			'type' => 'log',
			'data' => $message,
		);
		$this->entries['logs']['logCount']++;
	}

	protected function processProfileLog($log){
		$average = 0;
		$message=$log[0];
		if(!strncasecmp($message,'begin:',6))
		{
			$log[0]=substr($message,6);
			$this->stack[]=$log;

		}
		else if(!strncasecmp($message,'end:',4))
		{
			$token=substr($message,4);
			if(($last=array_pop($this->stack))!==null && $last[0]===$token)
			{
				$delta=$log[3]-$last[3];
				if(!$this->groupByToken){
					$token=$log[2];
				}
				$logqueue = array();
				if(strpos($log[2], 'system.db') !== false){
					$logqueue = &$this->db;
					$this->entries['queryTotals']['count']++;
					$this->entries['queryTotals']['time'] += $delta;
				}else{
					$logqueue = &$this->speed;
					$this->entries['logs']['speedCount']++;
				}


				if(isset($logqueue[$token])){
					$result = $this->aggregateResult($logqueue[$token],$delta);
					if(strpos($log[2], 'system.db') !== false){
						$this->entries['queryTotals']['duplicates']++;
					}
				}
				else{
					$result = array($token,1,$delta,$delta,$delta);
				}
				$logqueue[$token]= $result;

				$average = $logqueue[$token][4]/$logqueue[$token][1];
				$this->entries['logs']['console'][] = array(
					'type' => 'speed',
					'name' => $token,
					'data' => $this->getReadableTime($average),
				);

			}
			else{
				throw new CException(Yii::t('yii','CProfileLogRoute found a mismatching code block "{token}" and "{last}". Make sure the calls to Yii::beginProfile() and Yii::endProfile() be properly nested.',
				array('{token}'=>$token, '{last}' => $last[0])));
			}
		}

	}

	/**
	 * Aggregates the report result.
	 * @param array log result for this code block
	 * @param float time spent for this code block
	 */
	protected function aggregateResult($result,$delta)
	{
		list($token,$calls,$min,$max,$total)=$result;
		if($delta<$min){
			$min=$delta;
		}
		else if($delta>$max){
			$max=$delta;
		}
		$calls++;
		$total+=$delta;
		return array($token,$calls,$min,$max,$total);
	}
	/**
	 * Renders the view.
	 * @param string the view name (file name without extension). The file is assumed to be located under framework/data/views.
	 * @param array data to be passed to the view
	 */
	protected function render($view,$data)
	{
		if($this->showInFireBug){
			$view.='-firebug';
		}
		else
		{
			$app=Yii::app();
			if(!($app instanceof CWebApplication) || $app->getRequest()->getIsAjaxRequest()){
				return;
			}
		}
		extract($data);
		require( dirname(__FILE__).DIRECTORY_SEPARATOR."views".DIRECTORY_SEPARATOR.$view.".php");
	}

	public function getReadableFileSize($size, $retstring = null) {
		// adapted from code at http://aidanlister.com/repos/v/function.size_readable.php
		$sizes = array('bytes', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');

		if ($retstring === null) { $retstring = '%01.2f %s'; }

		$lastsizestring = end($sizes);

		foreach ($sizes as $sizestring) {
			if ($size < 1024) { break; }
			if ($sizestring != $lastsizestring) { $size /= 1024; }
		}
		if ($sizestring == $sizes[0]) { $retstring = '%01d %s'; } // Bytes aren't normally fractional
		return sprintf($retstring, $size, $sizestring);
	}

	/*-------------------------------------------
	 AGGREGATE DATA ON THE FILES INCLUDED
	 -------------------------------------------*/

	public function gatherFileData() {
		$files = get_included_files();
		$fileList = array();
		$fileTotals = array(
			"count" => count($files),
			"size" => 0,
			"largest" => 0,
		);

		foreach($files as $key => $file) {
			$size = @filesize($file);
			$fileList[] = array(
					'name' => $file,
					'size' => $this->getReadableFileSize($size),
					'rawsize' => $size,
			);
			$fileTotals['size'] += $size;
			if($size > $fileTotals['largest']) $fileTotals['largest'] = $size;
		}

		$func=create_function('$a,$b','return $a["rawsize"]<$b["rawsize"]?1:0;');
		usort($fileList,$func);


		$fileTotals['size'] = $this->getReadableFileSize($fileTotals['size']);
		$fileTotals['largest'] = $this->getReadableFileSize($fileTotals['largest']);
		$this->entries['files'] = $fileList;
		$this->entries['fileTotals'] = $fileTotals;
	}

	public function getReadableTime($time) {
		$ret = $time;
		$formatter = 0;
		$formats = array('ms', 's', 'm');
		if($time >= 1000 && $time < 60000) {
			$formatter = 1;
			$ret = ($time / 1000);
		}
		if($time >= 60000) {
			$formatter = 2;
			$ret = ($time / 1000) / 60;
		}
		$ret = number_format($ret,5,'.','') . ' ' . $formats[$formatter];
		return $ret;
	}

	/**
	 *
	 * @param mixed $obj any type of variable to profile memory
	 * @param string $msg message to be logged
	 * @param string  $category category of the message (e.g. 'system.web'). It is case-insensitive.
	 */
	public static function logMemory($obj, $msg = '', $category='application'){
		if(is_string($obj)){
			$memory = Yii::getLogger()->getMemoryUsage();
			$msg = $obj;
		}else{
			$memory = strlen(serialize($obj));
		}
		$type = gettype($obj);
		Yii::log("[{$memory}][$type] $msg", 'memory', $category);
	}
}
<?php 
/*
CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright(c)2016 Outdoor Business Network, Inc.
*/
class awfile {
		var $file;
		var $first_visit;
		var $visits;
		var $uniquevisits;
		var $aHours = array();
		var $aDays = array();
		var $aOs = array();
		var $aBrowser = array();
		var $aReferers = array();
		var $aRange = array();
		var $aSections = array();
		var $error = false;
		var $ErrorStr = array(
				1=>"file not found.",
				2=>"File not readable");
		var $_positions = array(
							"POS_GENERAL"=>0,
							"POS_TIME"=>0,
							"POS_OS"=>0,
							"POS_BROWSER"=>0,
							"POS_PAGEREFS"=>0,
							"POS_DAY"=>0,
							"POS_SESSION"=>0,
							"POS_SIDER"=>0);
		
		function awfile($file) {
				if (!file_exists($file))
					$this->error = 1;
				else {
					$this->file = $file;
					$this->__proc();
				}			
		}	
	
		function __proc() {
				if (!$fd= fopen($this->file,"r"))
					$this->error = 2;
				else {

						do {
							$str = trim(fgets($fd));
							if ($str{0}=="#") continue;
							list($elem,$pos) = explode(" ",$str);
							if (isset($this->_positions[$elem])) 
								$this->_positions[$elem] = $pos;
							else continue;												
						} while ($str!="END_MAP");
				
				
						fseek($fd,$this->_positions["POS_GENERAL"]);
						list(,$n) = explode(" ",trim(fgets($fd)));
						$pendientes = 3;
						while ($n>0) {
								list($elem,$num) = explode(" ",trim(fgets($fd)));
								switch($elem) {
										case "FirstTime": 
																$this->first_visit = $num;
																$pendientes--;
																break;
										case "TotalVisits":
																$this->visits = $num;
																$pendientes--;
																break;
												
										case "TotalUnique":
																$this->uniquevisits = $num;
																$pendientes--;
																break;
										default: continue;
								}
								if ($pendientes==0) break;
								$n--;
						}
													
/*
						fseek($fd,$this->_positions["POS_TIME"]);
						list(,$n) = explode(" ",trim(fgets($fd)));
						while ($n>0) {
								list($elem,$num,,,,,) = explode(" ",trim(fgets($fd)));
								$this->aHours[$elem] = $num;
								$n--;
						}
						
						fseek($fd,$this->_positions["POS_OS"]);
						list(,$n) = explode(" ",trim(fgets($fd)));
						while ($n>0) {
								list($elem,$num) = explode(" ",trim(fgets($fd)));
								$this->aOs[$elem] = $num;
								$n--;
						}
						
						fseek($fd,$this->_positions["POS_BROWSER"]);
						list(,$n) = explode(" ",trim(fgets($fd)));
						while ($n>0) {
								list($elem,$num) = explode(" ",trim(fgets($fd)));
								$this->aBrowser[$elem] = $num;
								$n--;
						}
					
						fseek($fd,$this->_positions["POS_PAGEREFS"]);
						list(,$n) = explode(" ",trim(fgets($fd)));
						while ($n>0) {
								list($elem,$num,) = explode(" ",trim(fgets($fd)));
								$this->aReferes[$elem] = $num;
								$n--;
						}
						
						fseek($fd,$this->_positions["POS_DAY"]);
						list(,$n) = explode(" ",trim(fgets($fd)));
						while ($n>0) {
								list($elem,,,,$num) = explode(" ",trim(fgets($fd)));
								$this->aDays[$elem] = $num;
								$n--;
						}
												
						fseek($fd,$this->_positions["POS_SESSION"]);
						list(,$n) = explode(" ",trim(fgets($fd)));
						while ($n>0) {
								list($elem,$num) = explode(" ",trim(fgets($fd)));
								$this->aRange[$elem] = $num;
								$n--;
						}
						
						fseek($fd,$this->_positions["POS_SIDER"]);
						list(,$n) = explode(" ",trim(fgets($fd)));
						while ($n>0) {
								list($elem,$num,,,) = explode(" ",trim(fgets($fd)));
								$this->aSections[$elem] = $num;
								$n--;
						}
                        */
				
				}	
						
		}
		
		
		function GetFirstVisit() {
			return $this->first_visit;
		}
		
		function GetVisits() {
			return $this->visits;
		}
		
		function GetUniqueVisits() {
			return $this->uniquevisits;
		}
		
		function GetHours() {
			return $this->aHours;
		}
		
		function GetDays() {
			return $this->aHours;
		}
		
		function GetOs() {
				return $this->aOs;
		}
		
		function GetBrowser() {
			return $this->aBrowser;
		}
		
		function GetReferers() {
			return $this->aReferes;
		}
		
		function GetRanges() {
			return $this->aRange;
		}
		
		function GetSections() {
			return $this->aSections;
		}
		
		function Error() {
			return $this->error;
		}
		
		function GetError() {
			return $this->ErrorStr[$this->error];
		}
		
		function GetBetterDay() {
			$max = 0;
			foreach ($this->aDays as $day => $num) {
				if ($num>$max) {
					$max = $num;
					$date = $day;
				}
			}
			return array($date,$max);
		}

}

?>
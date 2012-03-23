<?php

/**
 * Display a google calendar on a page.
 * Customisation options provided
 */
class GoogleCalendarPage extends Page{
	
	static $db = array(
		'CalendarID' => 'Varchar(255)',
		'Height' => 'Int',
		
		'ShowNav' => 'Boolean',
		'ShowDate' => 'Boolean',
		'ShowPrint' => 'Boolean',
		'ShowTabs' => 'Boolean',
		
		'Mode' => "Enum('WEEK,MONTH,AGENDA','MONTH')"
	);
	
	static $defaults = array(
		'Height' => 500,
		'ShowNav' => true,
		'ShowDate' => true,
		'ShowPrint' => false,
		'ShowTabs' => false,
		'Mode' => 'MONTH'
	);
	
	function getCMSFields(){
		$fields = parent::getCMSFields();
		$fields->addFieldsToTab('Root.Content.Calendar',array(
			new TextField('CalendarID','Source (Calendar ID)'),
			$this->dbObject('Mode')->formField("Display mode"),
			new CheckboxField('ShowNav',"Show Navigation"),
			new CheckboxField('ShowDate',"Show Date Selector"),
			new CheckboxField('ShowPrint',"Show Print Icon"),
			new CheckboxField('ShowTabs',"Show Tabs (for switching between Week,Month,Agenda views)")
		));
		
		return $fields;
	}
	
	function getURL(){
		
		if(!$this->CalendarID) return null;
		
		$data = array(
			'showTitle'	=> (int) $this->ShowTitle,
			'showNav'	=> (int) $this->ShowNav,
			'showDate'	=> (int) $this->ShowDate,
			'showPrint'	=> (int) $this->ShowPrint,
			'showTabs'	=> (int) $this->ShowTabs,
			'showCalendars'	=> (int) $this->ShowCalendars,
			'showTz'	=> (int) $this->ShowTimeZone,
			'wkst'		=> ($this->WeekStart) ? $this->WeekStart: 1,
			//'bgcolor'	=> $this->BgColor,
			'src'		=> $this->CalendarID,
			//'color'		=> "%2333cc00",
			//'ctz'		=> $this->TimeZone,
			'mode'		=> $this->Mode
		);
		
		$baseurl = "https://www.google.com/calendar/embed";
		$url = $baseurl."?".$this->implode_with_key($data);
		return $url;
	}
	
	function implode_with_key($assoc, $inglue = '=', $outglue = '&amp;') {
		$return = '';
		foreach ($assoc as $tk => $tv) {
			$return .= $outglue . $tk . $inglue . $tv;
		}
		return substr($return, strlen($outglue));
	}
	
}

class GoogleCalendarPage_Controller extends Page_Controller{
	
	function Calendar(){
		
		if(!$this->CalendarID) return false;
		$tdata = array(
			'Height'	=> (int) $this->Height,
			'Width'		=> ($this->Width) ? $this->Width : '100%',
			'Src'		=> $this->getURL()
		);
		return $this->customise($tdata)->renderWith('EmbedGoogleCalendar');
	}
	
	/**
	 * Useful if you don't want to bother creating a page template.
	 */
	function Form(){
		return $this->Calendar();
	}
	
}
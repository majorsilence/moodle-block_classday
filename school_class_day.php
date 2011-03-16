<?php
/*

Copyright 2011 (C) Peter Gill <peter@majorsilence.com>
modified by Joseph Rézeau <joseph@rezeau.org> march 2011
This file is part of classday.

classday is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

classday is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/



class SchoolClassDay
{
    // Variables holding start date and times.
    private $start_year;
    private $end_year;
    private $start_date; 
    private $end_date; 
    private $number_of_days;

    // Array holding all the professional development days in a year
    private $pd_days;

    // Array holding all the holidays in the year
    private $holiday_days;
        
    function __construct($startdate, $enddate, $pd_array, $holiday_array, $number_of_days_per_cycle=7)
    {
        
        $this->start_date = $startdate;
        $this->end_date = $enddate;
        $this->pd_days = $pd_array;
        $this->holiday_days = $holiday_array;
        $this->number_of_days = $number_of_days_per_cycle;
        
    }
    
    
    // This function loops through the calendar year displaying each day and whether it is a School Day/PD/Holiday.
    function PrintCalendar()
    {
        $day_count = 1;
        $working_date = $this->start_date;
        // Loop through start date until end date
        while($working_date <= $this->end_date)
        {
            $day_used = false;
            
            
            if (in_array($working_date, $this->pd_days))
            {
                // Check if it is a PD Day.
                echo $working_date . ", PD Day<br>";
            }
            elseif(in_array($working_date, $this->holiday_days))
            {
                // Check if it is a holdiay.
                echo $working_date . ",  Holiday<br>";
            }
            elseif (date('N', strtotime($working_date)) == 6 || date('N', strtotime($working_date)) ==7)
            {
                // Check if weekend.  6 is saturday.  7 is sunday.
                echo $working_date . ",  Weekend<br>";
                
            }
            else
            {
                echo $working_date . ",  Day " . $day_count . "<br>";
                $day_used=true;
            }
            
            // increment the current date by 1 day.
            $working_date = date("Y-m-d", strtotime ("+1 day", strtotime($working_date)));
            
            if($day_used == true)
            {
                // Days are only used if it is not a weekend/pd/holiday.
                $day_count++;
            }
            
            if ($day_count > $this->number_of_days) 
            {
                // Reset day back 1 once all 7 days have been used
                $day_count = 1;
            }
        }
    }
        
    // This function loops through the calendar year looking for the current day to display.
    function GetCurrentDay() {
        $day_count =1;
        $working_date = $this->start_date;
        $current_date = date('Y-m-d'); // Get todays date.
        
        
        // Loop through start date until end date
        while($working_date <= $this->end_date) {
            if (!in_array($working_date, $this->pd_days) // Check if it is NOT a PD Day. 
                && !in_array($working_date, $this->holiday_days) // Check if it is NOT a holiday Day.
                && (date('N', strtotime($working_date)) < 6) // Check if it is NOT a weekend Day.
                ) {
                	$day_count++;
            }
            $display_message = '';
            $day_used = false;
            if ($current_date == $working_date) {
                // Found the current day; print it and Break out of loop.
                // continue the loop for 6 more days, to display a whole week of class_days (JR)
                // increment the current date by 1 day.
            	for ($i=1; $i<8; $i++) {
            		$day_used = false;
		            if (in_array($working_date, $this->pd_days)) {
		                // Check if it is a PD Day. 
		                $display_message .= $this->getDisplayDate ($working_date) . '<span class="classday classday_professionalday">'.
		                get_string('professionalday','block_classday').'</span><br />';
		            }
		            elseif(in_array($working_date, $this->holiday_days)) {
		                // Check if it is a holdiay.
		                $display_message .= $this->getDisplayDate ($working_date) . '<span class="classday classday_holiday">'.
		                  get_string('holiday','block_classday').'</span><br />';
		            }
		            elseif (date('N', strtotime($working_date)) == 6 || date('N', strtotime($working_date)) ==7) {
		                // Check if weekend.  6 is saturday.  7 is sunday.
		                $display_message .= $this->getDisplayDate ($working_date) . '<span class="classday classday_weekend">'.
		                  get_string('weekend','block_classday').'</span><br />';
		                
		            } else {
		                $display_message .= $this->getDisplayDate ($working_date) . '<span class="classday classday_day">'.
		                  get_string('day','form').' '.$day_count. '</span><br />';
		                $day_used=true;
		            }
		            if ($i == 1) {
		            	$display_message = '<span class="classday_today">'.$display_message.'</span>';
		            }
		            // increment the current date by 1 day.
		            $working_date = date("Y-m-d", strtotime ("+1 day", strtotime($working_date)));
		            if ($working_date > $this->end_date) {
		            	break;
		            }
                    if ($i<7) {
                      $display_message .= '<div class="classday_border"></div>';
                    }
		            if($day_used == true) {
		                // Days are only used if it is not a weekend/pd/holiday.
		                $day_count++;
		            }
		            if ($day_count > $this->number_of_days) {
		                // Reset day back 1 once all 7 days have been used
		                $day_count = 1;
		            }
		        }
            	
            	return $display_message;
                break;  
            }
            
            // increment the current date by 1 day.
            $working_date = date("Y-m-d", strtotime ("+1 day", strtotime($working_date)));
            
            if($day_used == true) {
                // Days are only used if it is not a weekend/pd/holiday.
                $day_count++;
            }
            
            if ($day_count > $this->number_of_days){
                // Reset day back 1 once all 7 days have been used
                $day_count = 1;
            }
        }
    }
    function getDisplayDate ($working_date) {
    	$current_date = date('Y-m-d'); // Get todays date.
    	$displaydate = '<span class="classday_date">';
    	if ($working_date == $current_date) {
    		$displaydate .= get_string('today','calendar').'</span>';
    	} else {
	    	$time = explode("-", $working_date);
	        $timestamp = make_timestamp($time[0],$time[1],$time[2]);
	        $displaydate .= userdate($timestamp, get_string('strftimedayshort')).'</span>';
    	}
        return $displaydate.'<br />';
    }
}
?>
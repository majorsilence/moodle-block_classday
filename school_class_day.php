<?php
/*

Copyright 2011 (C) Peter Gill <peter@majorsilence.com>

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
        
        /* *******************
    
        $this->start_date = date("Y-m-d", mktime(0, 0, 0, 9, 5, $this->start_year)); // 1st hour, 1st minute, 1st second, 1st month, 1st day, 2010 year, otherwords sept 5 2010
        $this->end_date = date("Y-m-d", mktime(0, 0, 0, 6, 25, $this->end_year)); // 1st hour, 1st minute, 1st second, 1st month, 1st day, 2010 year, otherwords sept 5 2010
        
        
        $this->pd_days = array(date("Y-m-d", mktime(0, 0, 0, 9, 8, $this->start_year)),  date("Y-m-d", mktime(0, 0, 0, 9, 9, $this->start_year)), 
            date("Y-m-d", mktime(0, 0, 0, 9, 10, $this->start_year)));
            
        $this->holiday_days = array(date("Y-m-d", mktime(0, 0, 0, 10, 1, $this->start_year)),  date("Y-m-d", mktime(0, 0, 0, 10, 4, $this->start_year)), 
            date("Y-m-d", mktime(0, 0, 0, 10, 5, $this->start_year)), date("Y-m-d", mktime(0, 0, 0, 3, 8, $this->end_year)),  
            date("Y-m-d", mktime(0, 0, 0, 3, 9, $this->end_year)), date("Y-m-d", mktime(0, 0, 0, 3, 10, $this->end_year)));
        */
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
    function GetCurrentDay()
    {
        $day_count =1;
        $working_date = $this->start_date;
        $current_date = date('Y-m-d'); // Get todays date.
        
        
        // Loop through start date until end date
        while($working_date <= $this->end_date)
        {
            
            $day_used = false;
            $display_message = "";
            
            if (in_array($working_date, $this->pd_days))
            {
                // Check if it is a PD Day.
                $display_message = '<p style="' . $this->GetCss() . '">PD Day</p>';
            }
            elseif(in_array($working_date, $this->holiday_days))
            {
                // Check if it is a holdiay.
                $display_message = '<p style="' . $this->GetCss() . '">Holiday</p>';
            }
            elseif (date('N', strtotime($working_date)) == 6 || date('N', strtotime($working_date)) ==7)
            {
                // Check if weekend.  6 is saturday.  7 is sunday.
                $display_message = '<p style="' . $this->GetCss() . '">Weekend</p>';
                
            }
            else
            {
                $display_message = '<p style="' . $this->GetCss() . '">Day ' . $day_count . "</p>";
                $day_used=true;
            }
            
            if ($current_date == $working_date)
            {
                // Found the current day; print it and Break out of loop.
                return $display_message;
                break;  
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
    
    // Return string of css to apply to the output of GetCurrentDay.
    private function GetCss()
    {
        $css_filepath = dirname(__FILE__) . '/classday.css';
        
        if (file_exists($css_filepath)) 
        {
            return file_get_contents($css_filepath);
        } 
        else 
        {
            return "text-align: center; color: #800517; font-size:x-large;";
        }
    }
    

}

?>
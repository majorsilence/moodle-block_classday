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


// See: http://docs.moodle.org/en/Development:Blocks for more information on how to build a block.
//

class block_classday extends block_base {

// Can also extend 'block_list'. See 'moodleblock.class.php'.

    private $config_filepath;

    // The 'init' function is required for all blocks.


    function init() {
        $this->config_filepath = dirname(__FILE__) . '/config.txt';
    
    
        // You need to define a human-friendly title for your block, for example "Test Block".
        // Since Moodle is internationalized, you should read this from a language file with get_string().
        // You can create your very own language file and put the strings your block uses in there,
        // Moodle will automatically know about it and use it. See the general README for information on
        // where to place this file and how to name it.

        // In this case, we assume that you have $string['blockname'] = "Test Block"; in your lang file.
        $this->title = get_string('blockname','block_classday');

        // You can use this so that your block can upgrade itself in the future, if there is need.
        // If you are just creating a new block, you do not need to change this value (but it is
        // considered polite to set it to YYYYMMDD00).
        $this->version = 2011030900;
    }

    // Apart from the constructor, there is only ONE function you HAVE to define, get_content().
    // Let's take a walkthrough! :)

    function get_content() {
        global $CFG;
        if ($this->content !== NULL) {
            return $this->content;
        }
        
    echo '<link rel="stylesheet" type="text/css" href="'.$CFG->wwwroot.'/blocks/classday/classday.css" />';
        
        $hello = new SchoolClassDay($this->get_start_date(), $this->get_end_date(), $this->get_pd_array(), $this->get_holiday_array(), $this->get_number_of_cycle_days());
        
        
        $output = (string)$hello->GetCurrentDay();
        
        $this->content = new stdClass;
        $this->content->text = $output; // //'The content of our NEWBLOCK block!';
        $this->content->footer = '';
    
        return $this->content;
    }

    function StartsWith($Haystack, $Needle)
    {
        // Recommended version, using strpos
         return strpos(trim((string)$Haystack), trim((string)$Needle)) === 0;
    }

    // Returns an array of professional development dates extracted from the config file
    function get_pd_array()
    {
        return $this->get_date_array("pd:");
    }
    
    // Returns an array of holiday dates extracted from the config file
    function get_holiday_array()
    {
        return $this->get_date_array("holiday:");
    }
    
    
    /*
    Sample config file format (also see sampleconfig.txt):
    
    startdate: 2010/9/5
    enddate: 2011/6/25
    daycount: 7
    pd: 2010/9/8, 2010/9/9, 2010/9/10
    holiday: 2010/10/1, 2010/10/4, 2010/10/5, 2011/3/8, 2011/3/9, 2011/3/10

    
    */
    // Returns an array of dates extracted from the config file
    function get_date_array($search="")
    {
        $config_info = file_get_contents ($this->config_filepath);
        $results = explode("\n", (string)$config_info);
        
        foreach($results as $value)
        {
            // Check if pd values and if so extract each date
            if ($this->StartsWith($value, trim($search)))
            {
                $tvalue = $value;
                $tvalue = str_replace($search, "", $tvalue);
                
                $pd_array = explode(",", $tvalue);
                $pd_array = array_filter($pd_array, 'trim');
                
                $final_array;
                foreach($pd_array as $val)
                {
                    $final_array[]= date('Y-m-d', strtotime($val));
                }
                
                 
                return $final_array;
                
            }
            
        }
        
        return null;
    }
        
    // Returns a date.  It is the start date of the school year cycle.
    function get_start_date()
    {
        $arr = $this->get_date_array("startdate:");
        return $arr[0];
    }

    // Returns a date.  It is the end date of the school year cycle.
    function get_end_date()
    {
        $arr = $this->get_date_array("enddate:");
        return $arr[0];
    }

    // Returns integer.  That is the number of days in the school day cycle.
    function get_number_of_cycle_days()
    {
        $config_info = file_get_contents ($this->config_filepath);
        $results = explode("\n", (string)$config_info);
        
        foreach($results as $value)
        {
            if ($this->StartsWith($value, "daycount:"))
            {
                $tvalue = $value;
                $tvalue = trim(str_replace("daycount:", "", $tvalue));
                return (int)$tvalue;
            }
        }
    
        return 7; // default
    }
    
    function instance_allow_multiple() {
        return true;
    }
    
    
}



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
        global $CFG;
        $day_count = 1;
        $working_date = $this->start_date;
        $current_date = date('Y-m-d'); // Get todays date.
        
        
        // Loop through start date until end date
        while($working_date <= $this->end_date) {
            $display_message = '';
            $day_used = false;
            if ($current_date == $working_date) {
                // Found the current day; print it and Break out of loop.
                // continue the loop for 6 more days, to display a whole week of class_days (JR)
                // increment the current date by 1 day.
                for ($i=1; $i<8; $i++) {
                    $day_used = false;
                    // after display of current day, offer a choice of displaying or hiding week
                    if ($i==2) {
                        $showweek = get_string('showweek','block_classday');
                        $hideweek = get_string('hideweek','block_classday');
                        $display_message .= '<script language="javascript"> 
                            function toggle(showweek, hideweek) {
                                var ele = document.getElementById("toggleText");
                                var text = document.getElementById("displayText");
                                if(ele.style.display == "block") {
                                    ele.style.display = "none";
                                    text.innerHTML = showweek;
                                }
                                else {
                                    ele.style.display = "block";
                                    text.innerHTML = hideweek;
                                }
                            } 
                            </script>';
                        $display_message .= '<a id="displayText" href="javascript:toggle(\''.$showweek.'\', \''.$hideweek.'\');">'.$showweek.'</a>
                            <div id="toggleText" style="display: none">';
                    }
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
                

				
                return $display_message.'</div>';
                break;  
            }
            
			
			if (!in_array($working_date, $this->pd_days) // Check if it is NOT a PD Day. 
			&& !in_array($working_date, $this->holiday_days) // Check if it is NOT a holiday Day.
			&& (date('N', strtotime($working_date)) < 6) // Check if it is NOT a weekend Day.
			) {
				$day_count++;
			}
			
			
            // increment the current date by 1 day.
            $working_date = date("Y-m-d", strtotime ("+1 day", strtotime($working_date)));
            
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

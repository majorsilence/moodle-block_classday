For Moodle 1.9.x only.

Currently there is no option to configure the classday (/daily schedule) block 
from within moodle.  The only option is to configure it manually in config.txt 
and classday.css.

Configure:
    SampleConfig.txt should copied and renamed as config.txt.  To configure set the following fields.
    *startdate - The first day of the school year
    *enddate - The last day of the school year
    *daycount - The number of days in a cycle
    *pd - Professional development days
    *holiday - All holidays

classday.css can be used to configure the display properties of the text that shows
within the block.  Any valid css styles can be applied.

TODO:
*Configure within moodle.
*Moodle 2.* support.


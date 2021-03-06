<?php
class Setting extends CActiveRecord {
    static public $timetable = array(
         1 => array( '7:50',  '8:30'),
         2 => array( '8:40',  '9:20'),
         3 => array( '9:30', '10:10'),
         4 => array('10:30', '11:10'),
         5 => array('11:20', '12:00'),
         6 => array('14:30', '15:10'),
         7 => array('15:20', '16:00'),
         8 => array('16:10', '16:50'),
         9 => array('17:00', '17:40'),
        10 => array('19:40', '20:20'),
        11 => array('20:30', '21:10'),
        12 => array('21:20', '22:00'),
    );

    static public $weeksName = array(
        1 => '星期一',
        2 => '星期二',
        3 => '星期三',
        4 => '星期四',
        5 => '星期五',
        6 => '星期六',
        7 => '星期日',
    );

    public static function model($className=__CLASS__) {
        return parent::model($className);
    }
}

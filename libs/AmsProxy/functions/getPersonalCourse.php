<?php
/**
 * 获取个人课表
 *
 * @param string term number
 */
class getPersonalCourse extends __base__ {
    public function getData() {
        $hidyzm = $this->getHidyzm();
        if ($hidyzm !== false) {
            // $m 是一个随机字串, 这里就不随机了.
            $m = 'hDIbFprNaT0AGia';
            $hidsjyzm = strtoupper(md5($this->amsProxy->schoolcode . $this->getXNXQ() . $m));

            return $this->amsProxy->POST(
                'znpk/Pri_StuSel_rpt.aspx',
                array(
                    'Sel_XNXQ' => $this->getXNXQ(),
                    'rad'      => 1,
                    'px'       => 0,
                    'hidyzm' => $hidyzm,
                    'hidsjyzm' => $hidsjyzm,
                ),
                array(
                    'm' => $m,
                )
            );
        } else {
            return false;
        }
    }

    public function getHidyzm() {
        $dom = new DOMDocument;
        @$dom->loadHTML($this->htmlFinishing($this->amsProxy->GET('znpk/Pri_StuSel.aspx')));
        $form = $dom->getElementsByTagName('form')->item(0);
        $hidyzm_input = $form->getElementsByTagName('table')->item(0)->
            getElementsByTagName('tr')->item(6)->
            getElementsByTagName('input')->item(0);
        if ($hidyzm_input->attributes->getNamedItem('value')) {
            return $hidyzm_input->attributes->getNamedItem('value')->textContent;
        } else {
            return false;
        }
    }

    public function parse($dom) {
        $tables = $dom->getElementsByTagName('table');
        $courseType = $tables->item(0)->getElementsByTagName('td')->item(0);

        if ($courseType->textContent == '讲授/上机') {
            $course = array();
            $trs = $tables->item(1)->getElementsByTagName('tr');

            for ($i = 2; $i < $trs->length - 1; $i++) {
                $tds = $trs->item($i)->getElementsByTagName('td');

                for ($j = 0; $j < $tds->length; $j++) {
                    if (isset($tds->item($j + 1)->textContent))
                        $course[$j] = $tds->item($j + 1)->textContent;
                }

                $courses[] = $course;
            }

            return $this->convert($courses);
        } else {
            return array();
        }
    }

    /**
     * @param array $originalCourse
     * @return array
     */
    public function convert($originalCourse) {
        $courses = array();

        foreach ($originalCourse as $course) {
            preg_match('/(...)\[(\d+)-?(\d+)?节\]/', $course[10], $lesson);
            $week = explode('-', $course[9]);

            if ($course[0])
                $_course = $course;

            $courses[] = array(
                'courseName'  => preg_replace('/^\[.*?\]/', '', $_course[0]),
                'credit'      => $_course[1],
                'totalHour'   => $_course[2],
                'courseType'  => $_course[5],
                'teachType'   => $_course[6],
                'examType'    => $_course[7],
                'teacherName' => $_course[8],
                'weekStart'   => (int)$week[0],
                'weekTo'      => isset($week[1]) ? (int)$week[1] : (int)$week[0],
                'weekDay'     => (int)self::$weekDict[$lesson[1]],
                'lessonStart' => (int)$lesson[2],
                'lessonTo'    => isset($lesson[3]) ? (int)$lesson[3] : (int)$lesson[2],
                'location'    => $course[11],
            );
        }

        return $courses;
    }
}

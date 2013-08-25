<?php
/**
 * 基控制器，需要登录验证，要进行学生相关操作请继承该控制器
 */
class StudentController extends BaseController {
    /**
     * @var AmsProxy
     */
    public $amsProxy;

    /**
     * @var Student
     */
    public $student;

    public function init() {
        parent::init();
        if (defined('IS_LOGGED')) {
            $this->amsProxy = new AmsProxy(
                $_SESSION['student']['sid'],
                $_SESSION['student']['pwd']);

            $this->student = Student::model()->findByPk(
                $_SESSION['student']['sid']);
        } else {
            $this->notLoggedHandle();
        }
    }

    /**
     * 未登录的处理
     */
    public function notLoggedHandle() {
        $this->redirect(array('site/login'));
    }

    /**
     * 先尝试从数据库中读取，如果数据库中没有数据，则从教务系统获取
     * 获取的数据会保存到数据库
     * @param bool $json 是否返回 json
     * @return array 成绩表
     */
    public function getScore($json=false) {
        if ($this->student->score) {
            if ($json)
                return $this->student->score;
            else
                return json_decode($this->student->score, true);
        } else {
            $scoreTable = $this->amsProxy->getScore(1);
            $this->student->score = json_encode($scoreTable);
            $this->student->save();

            if ($json)
                return json_encode($scoreTable);
            else
                return $scoreTable;
        }
    }

    /**
     * 先尝试从数据库中读取，如果数据库中没有数据，则从教务系统获取
     * 获取的数据会保存到数据库
     * @param bool $json 是否返回 json
     * @return array 课程表
     */
    public function getCourse($json=false) {
        if ($this->student->course) {
            if ($json)
                return $this->student->course;
            else
                return json_decode($this->student->course, true);
        } else {
            $courses = array_merge(
                $this->amsProxy->getCourse(),
                $this->amsProxy->getClassCourse(
                    $_SESSION['student']['info']['行政班级']));
            $this->student->course = json_encode($courses);
            $this->student->save();

            if ($json)
                return json_encode($courses);
            else
                return $courses;
        }
    }
}

<?php

namespace App\Domain\Course\Service;

use App\Domain\Course\Repository\CourseRepository;

class CourseService
{
    private CourseRepository $courseRepository;

    public function __construct()
    {
        $this->courseRepository = new CourseRepository();
    }

    public function GetAllCourses()
    {
        return $this->courseRepository->GetAllCourses();
    }

    public function GetCourseSchedule($cd_cso)
    {
        return $this->courseRepository->GetCourseSchedule($cd_cso);
    }

    public function GetOpportunitiesByCourse($cd_cso)
    {
        return $this->courseRepository->GetOpportunitiesByCourse($cd_cso);
    }

    public function GetCourseDuration($cd_cso)
    {
        return $this->courseRepository->GetCourseDuration($cd_cso);
    }
}

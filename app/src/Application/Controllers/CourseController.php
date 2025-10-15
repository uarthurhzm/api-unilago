<?php

namespace App\Application\Controllers;

use App\Domain\Course\Service\CourseService;
use App\Infrastructure\Http\Response;
use App\Shared\Attributes\FromRoute;
use App\Shared\Attributes\HttpGet;
use App\Shared\Utils\Routes;

class CourseController extends ControllerBase
{
    public function __construct(
        private CourseService $courseService
    ) {}

    #[HttpGet(Routes::COURSES)]
    public function GetAllCourses()
    {
        try {
            $courses = $this->courseService->GetAllCourses();
            Response::success($courses, "Cursos recuperados com sucesso");
        } catch (\Throwable $th) {
            Response::error("Erro ao recuperar cursos: " . $th->getMessage());
        }
    }

    #[HttpGet(Routes::COURSE_SCHEDULE)]
    public function GetCourseSchedule(#[FromRoute] string $cd_cso)
    {
        try {
            $schedule = $this->courseService->GetCourseSchedule($cd_cso);
            Response::success($schedule, "Horários do curso recuperados com sucesso");
        } catch (\Throwable $th) {
            Response::error("Erro ao recuperar horários do curso: " . $th->getMessage());
        }
    }

    #[HttpGet(Routes::COURSE_OPPORTUNITIES)]
    public function GetOpportunitiesByCourse(#[FromRoute] string $cd_cso)
    {
        try {
            $opportunities = $this->courseService->GetOpportunitiesByCourse($cd_cso);
            Response::success($opportunities, "Oportunidades do curso recuperadas com sucesso");
        } catch (\Throwable $th) {
            Response::error("Erro ao recuperar oportunidades do curso: " . $th->getMessage());
        }
    }

    #[HttpGet(Routes::COURSE_DURATION)]
    public function GetCourseDuration(#[FromRoute] string $cd_cso)
    {
        try {
            $duration = $this->courseService->GetCourseDuration($cd_cso);
            Response::success($duration, "Duração do curso recuperada com sucesso");
        } catch (\Throwable $th) {
            Response::error("Erro ao recuperar duração do curso: " . $th->getMessage());
        }
    }
}

<?php

namespace App\Shared\Utils;

class Routes
{
    const DB_TABLE_NAMES = '/db-table-names';
    const DB_FIELDS_NAMES = '/db/{tableName}/fields';

    const AUTH_LOGIN = '/auth/login';
    const AUTH_REFRESH_TOKEN = '/auth/refresh-token';
    const AUTH_LOGOUT = '/auth/logout';
    const AUTH_RECOVERY_PASSWORD = '/auth/{cpf}/recovery-password';

    const STUDENT = '/student/{cd_mat}';
    const STUDENT_CARD = '/student/{cd_mat}/card-info';
    const STUDENT_DISCIPLINES = '/student/{cd_mat}/disciplines';
    const STUDENT_SUBSTITUTE_DISCIPLINES = '/student/{cd_mat}/sub-disciplines';
    const STUDENT_BY_PASSWORD = '/student/{cd_mat}/by-password';
    const STUDENT_EXTRACURRICULAR_ACTIVITIES = '/extracurricular/{cd_alu}/activities';
    const STUDENT_DISCIPLINES_CONTENT = '/student/{login}/disciplines/{cd_disc}/content';
    const STUDENT_EXTENSION_CERTIFICATES = '/student/{login}/extension-certificates';
    const STUDENT_SCIENTIFIC_MEETING = '/student/{cd_mat}/scientific-meeting';
    const STUDENT_LISTENER_MEETING = '/student/{cd_mat}/listener-meeting';
    const STUDENT_ACADEMIC_WEEK = '/student/{login}/academic-week';
    const STUDENT_PRESENCE = '/student/{cd_mat}/presence';
    const STUDENT_GRADES = '/student/{cd_mat}/grades';
    const STUDENT_DOCUMENTS = '/student/{cd_mat}/documents';
    const STUDENT_ABSENCES = '/student/{cd_mat}/absences';
    const STUDENT_ALL_PROFESSORS = '/student/{cd_mat}/professors';
    const STUDENT_DISCIPLINES_SYLLABUS = '/student/{cd_mat}/disciplines-syllabus';
    const PATCH_STUDENT_PASSWORD = '/student/{cd_mat}/password';
    const STUDENT_TICKET = '/student/{cd_mat}/ticket';

    const CAMPUSES = '/campuses';

    const SCHEDULED_EVENTS = '/events';

    const COURSES = '/courses';

    const POST_MESSAGE = '/messages';
    const MESSAGES = '/student/{cd_mat}/messages';
    const MESSAGE_POST_COMMENT = '/messages/{messageId}/comments';
    const MESSAGE_COMMENTS = '/messages/{messageId}/comments';

    const EXTRACURRICULAR_ACTIVITIES = '/extracurricular/activities';
    const POST_EXTRACURRICULAR_ACTIVITY = '/extracurricular/activities';
    const DELETE_EXTRACURRICULAR_ACTIVITY = '/extracurricular/activities/{cod_lanc}';

    const COURSE_SCHEDULE = '/courses/{cd_cso}/schedule';
    const COURSE_OPPORTUNITIES = '/courses/{cd_cso}/opportunities';
    const COURSE_DURATION = '/courses/{cd_cso}/duration';

    const LIBRARY_COLLECTIONS = '/library/collections';
    const LIBRARY_BOOK = '/library/collections/{bookId}';
    const LIBRARY_LOANED_BOOKS = '/library/loaned-books/{cd_mat}';
    const LIBRARY_RESERVE_BOOK = '/library/reserve-book';
    const LIBRARY_RESERVED_BOOKS = '/library/reserved-books/{cd_mat}';
    const LIBRARY_CANCEL_RESERVE = '/library/cancel-reservation/{reserveId}';
    const LIBRARY_RENEW_BOOK = 'library/renew-book/{seq_epr}';

    const GET_ALL_IES_PROFESSORS = '/professors/ies';

    const FINANCIAL_TAXES = '/financial/taxes';

    const SECRETARY_ENROLLMENT_CERTIFICATE_REQUEST = '/secretary/enrollment-certificate/request';
    const SECRETARY_ENROLLMENT_CERTIFICATES = '/secretary/student/{cd_mat}/enrollment-certificates';
    const SECRETARY_STUDENT_ATTESTS = '/secretary/student-attests/{cd_mat}';
    const SECRETARY_ACADEMIC_RECORD_REQUEST = '/secretary/academic-record/request';
    const SECRETARY_STUDENT_DOCUMENTS_PERMISSION = '/secretary/student/{cd_mat}/documents/permission';
    const SECRETARY_STUDENT_ACADEMIC_RECORD = '/secretary/student/{cd_mat}/academic-record';
    const SECRETARY_STUDENT_SUBSTITUTE_EXAM_REQUESTS = '/secretary/student/{cd_mat}/substitute-exams';
    const SECRETARY_SUBSTITUTE_EXAM_REQUEST = '/secretary/substitute-exam/request';
    const SECRETARY_DELETE_SUBSTITUTE_EXAM_REQUEST = '/secretary/substitute-exam/request/{protocol}';
    const SECRETARY_STUDENT_DEPENDENCIES = '/secretary/student/{cd_mat}/dp';
    const SECRETARY_SECTORS = '/secretary/sectors';

    const CPA_QUESTIONS = '/cpa/{cd_mat}/institution/questions';
    const POST_CPA_ANSWER = '/cpa/answer';
    const CPA_CHECK = '/cpa/{cd_mat}/check';

    const NOTICES = '/notices';
}

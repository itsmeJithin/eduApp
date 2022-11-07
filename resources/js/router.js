import VueRouter from "vue-router";
import ListCourses from "./components/courses/ListCourses";
import ListClasses from "./components/classes/ListClasses";
import DesignClassSubjects from "./components/subjects/DesignClassSubjects";
import ListClassGroups from "./components/classGroups/ListClassGroups";
import ListSyllabuses from "./components/syllabuses/ListSyllabuses";
import ListAllClassSubscriptionMonths from "./components/subscriptionMonths/ListAllClassSubscriptionMonths";
import AssignSubscriptionMonthFee from "./components/courseFee/AssignSubscriptionMonthFee";
import ManageExams from "./components/exams/ManageExams";
import ManageExamModes from "./components/examModes/ManageExamModes";
import QuestionPool from "./components/questionPool/QuestionPool";
import DashBoard from "./components/home/DashBoard";
import UsersList from "./components/users/UsersList";
import ManageDemoTopics from "./components/demoTopics/ManageDemoTopics";
import AnswerDoubts from "./components/doubts/AnswerDoubts";

const router = new VueRouter({
    mode: "history",
    routes: [
        {
            path: "/",
            name: "dashboard",
            component: DashBoard
        },
        {
            path: "/courses",
            name: 'listCourse',
            component: ListCourses
        },
        {
            path: "/classes",
            name: "listClasses",
            component: ListClasses
        },
        {
            path: "/design-class-subjects/:classGroupSyllabusId?/:subjectId?/:chapterId?",
            name: "designClassSubjects",
            component: DesignClassSubjects
        },
        {
            path: "/manage-demo-topics",
            name: "manageDemoTopics",
            component: ManageDemoTopics
        },
        {
            path: "/class-groups",
            name: "classGroups",
            component: ListClassGroups
        },
        {
            path: "/syllabuses",
            name: "syllabuses",
            component: ListSyllabuses
        },
        {
            path: "/subscription-months/manage-subscription-month/:classGroupSyllabusId",
            name: "subscriptionMonths",
            component: ListAllClassSubscriptionMonths
        },
        {
            path: "/course-fee/assign-fee/:classGroupSyllabusId",
            name: "courseFee",
            component: AssignSubscriptionMonthFee
        },
        {
            path: "/exams/manage-exams/:classGroupSyllabusId?/:classGroupSyllabusSubjectId?/:examId?",
            name: "exams",
            component: ManageExams
        },
        {
            path: "/exams/manage-exam-modes",
            name: "manageExamModes",
            component: ManageExamModes
        },
        {
            path: "/exams/manage-subject-questions/:classGroupSyllabusSubjectId?",
            name: "manageQuestionPool",
            component: QuestionPool
        },
        {
            path: "/users",
            name: "users",
            component: UsersList
        },
        {
            path: "/doubts/:classGroupSyllabusSubjectId?",
            name: "manageDoubts",
            component: AnswerDoubts
        }
    ]
});

export default router;

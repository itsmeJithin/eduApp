<template>
    <div id="rootwizard">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs nav-tabs-linetriangle nav-tabs-separator nav-stack-sm" role="tablist"
            data-init-reponsive-tabs="dropdownfx">
            <li class="nav-item">
                <a class="d-flex align-items-center" href="#" :class="activeTab==='FILTER_SUBJECTS'?'active':''">
                    <i class="material-icons fs-14 tab-icon">filter_alt</i>
                    <span>Filter Subjects</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="d-flex align-items-center" href="#" :class="activeTab==='MANAGE_EXAMS'?'active':''">
                    <i class="material-icons fs-14 tab-icon">fact_check</i>
                    <span>Manage Exams</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="d-flex align-items-center" href="#" :class="activeTab==='MANAGE_QUESTIONS'?'active':''">
                    <i class="material-icons fs-14 tab-icon">drag_indicator</i>
                    <span>Manage Question Paper</span>
                </a>
            </li>
        </ul>
        <!-- Tab panes -->
        <div :class="activeTab==='MANAGE_EXAMS'?'custom-tab-content':'tab-content'">
            <FilterSubjects :is-from-exam="true" v-if="activeTab==='FILTER_SUBJECTS'"/>
            <CreateSubjectExam :class-group-syllabus-subject-id="classGroupSyllabusSubjectId"
                               :class-group-syllabus-id="classGroupSyllabusId"
                               v-else-if="activeTab==='MANAGE_EXAMS'"/>
            <ManageQuestions :class-group-syllabus-subject-id="classGroupSyllabusSubjectId"
                             :class-group-syllabus-id="classGroupSyllabusId"
                             :exam-id="examId"
                             v-else-if="activeTab==='MANAGE_QUESTIONS'"/>
            <div class="row p-l-20 p-r-20 p-b-20" v-if="activeTab !== 'FILTER_SUBJECTS'">
                <div class="col-md-12 text-right">
                    <button class="btn btn-primary" @click.prevent="goBack">
                        <i class="fa fa-arrow-circle-left mr-1"></i> Go Back
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import FilterSubjects from "../questionPool/FilterSubjects";
import CreateSubjectExam from "./CreateSubjectExam";
import ManageQuestions from "./ManageQuestions";

export default {
    name: "ManageExams",
    components: {
        ManageQuestions,
        CreateSubjectExam,
        FilterSubjects
    },
    data() {
        return {
            activeTab: "FILTER_SUBJECTS",
            classGroupSyllabusSubjectId: "",
            classGroupSyllabusId: "",
            examId: ""
        }
    },
    mounted() {
        this.decideManageSubjectShow();
    },
    watch: {
        $route() {
            this.decideManageSubjectShow();
        }
    },
    methods: {
        decideManageSubjectShow() {
            const route = this.$route;
            this.classGroupSyllabusSubjectId = route.params.classGroupSyllabusSubjectId;
            this.classGroupSyllabusId = route.params.classGroupSyllabusId;
            this.examId = route.params.examId;
            if (this.classGroupSyllabusSubjectId && !this.examId) {
                this.activeTab = "MANAGE_EXAMS";
            } else if (this.examId) {
                this.activeTab = "MANAGE_QUESTIONS";
            } else {
                this.activeTab = "FILTER_SUBJECTS";
            }
        },
        goBack() {
            if (this.classGroupSyllabusSubjectId && this.examId) {
                this.$router.push({
                    name: "exams",
                    params: {
                        classGroupSyllabusSubjectId: this.classGroupSyllabusSubjectId,
                        classGroupSyllabusId: this.classGroupSyllabusId,
                        examId: null
                    }
                });
            } else if (this.classGroupSyllabusSubjectId && !this.examId) {
                this.$router.push({
                    name: "exams",
                    params: {
                        classGroupSyllabusSubjectId: null,
                        classGroupSyllabusId: null,
                        examId: null
                    }
                });
            }
        }
    }
}
</script>

<style scoped>

</style>

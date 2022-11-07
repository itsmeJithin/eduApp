<template>
    <div id="rootwizard">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs nav-tabs-linetriangle nav-tabs-separator nav-stack-sm" role="tablist"
            data-init-reponsive-tabs="dropdownfx">
            <li class="nav-item">
                <a class="d-flex align-items-center" href="#" :class="activeTab==='FILTER_SUBJECTS'?'active':''">
                    <i class="material-icons fs-14 tab-icon">touch_app</i>
                    <span>Filter Subjects</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="d-flex align-items-center" href="#" :class="activeTab==='ANSWER_DOUBTS'?'active':''">
                    <i class="material-icons fs-14 tab-icon">drag_indicator</i>
                    <span>Answer Doubts</span>
                </a>
            </li>
        </ul>
        <div :class="activeTab==='ANSWER_DOUBTS'?'custom-tab-content':'tab-content'">
            <FilterSubjects v-if="activeTab==='FILTER_SUBJECTS'"/>
            <SubjectDoubts v-else-if="activeTab==='ANSWER_DOUBTS'"
                           :class-group-syllabus-subject-id="classGroupSyllabusSubjectId"/>
            <div class="row p-l-20 p-r-20 p-b-20" v-if="classGroupSyllabusSubjectId">
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
import FilterSubjects from "./FilterSubjects";
import SubjectDoubts from "./SubjectDoubts";

export default {
    name: "AnswerDoubts",
    components: {SubjectDoubts, FilterSubjects},
    data() {
        return {
            classGroupSyllabusSubjectId: "",
            activeTab: "FILTER_SUBJECTS"
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
            if (this.classGroupSyllabusSubjectId) {
                this.activeTab = "ANSWER_DOUBTS";
            } else {
                this.activeTab = "FILTER_SUBJECTS";
            }
        },
        goBack() {
            if (this.classGroupSyllabusSubjectId) {
                this.$router.push({
                    name: "designClassSubjects",
                    params: {
                        classGroupSyllabusSubjectId: null,
                    }
                });
            }
        }
    }
}
</script>

<style scoped>

</style>

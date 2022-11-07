<template>
    <div id="rootwizard">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs nav-tabs-linetriangle nav-tabs-separator nav-stack-sm" role="tablist"
            data-init-reponsive-tabs="dropdownfx">
            <li class="nav-item">
                <a class="d-flex align-items-center" href="#" :class="activeTab==='ASSIGN_CLASS_GROUP'?'active':''">
                    <i class="material-icons fs-14 tab-icon">touch_app</i>
                    <span>Assign Subject to Class</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="d-flex align-items-center" href="#" :class="activeTab==='MANAGE_SUBJECTS'?'active':''">
                    <i class="material-icons fs-14 tab-icon">drag_indicator</i>
                    <span>Manage Subjects</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="d-flex align-items-center" href="#" :class="activeTab==='MANAGE_CHAPTERS'?'active':''">
                    <i class="material-icons fs-14 tab-icon">dns</i>
                    <span>Manage Chapters</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="d-flex align-items-center" href="#" :class="activeTab==='MANAGE_TOPICS'?'active':''">
                    <i class="material-icons fs-14 tab-icon">backup_table</i>
                    <span>Manage Topics</span>
                </a>
            </li>
        </ul>
        <!-- Tab panes -->
        <div :class="activeTab==='MANAGE_TOPICS'?'custom-tab-content':'tab-content'">
            <AssignClassGroupToSyllabus v-if="activeTab==='ASSIGN_CLASS_GROUP'"/>
            <CreateSubject v-else-if="activeTab==='MANAGE_SUBJECTS'"
                           :class-group-syllabus-id="classGroupSyllabusId"/>
            <ManageChapters v-else-if="activeTab==='MANAGE_CHAPTERS'"/>
            <ManageTopics v-else-if="activeTab==='MANAGE_TOPICS'"/>
            <div class="row p-l-20 p-r-20 p-b-20" v-if="classGroupSyllabusId">
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
    import CreateSubject from "./CreateSubject";
    import AssignClassGroupToSyllabus from "./AssignClassGroupToSyllabus";
    import ManageChapters from "../chapters/ManageChapters";
    import ManageTopics from "../topics/ManageTopics";

    export default {
        name: "DesignClassSubjects",
        components: {
            ManageTopics,
            ManageChapters,
            AssignClassGroupToSyllabus,
            CreateSubject
        },
        data() {
            return {
                classGroupSyllabusId: "",
                subjectId: "",
                chapterId: "",
                activeTab: "ASSIGN_CLASS_GROUP"
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
                this.classGroupSyllabusId = route.params.classGroupSyllabusId;
                this.subjectId = route.params.subjectId;
                this.chapterId = route.params.chapterId;
                if (this.classGroupSyllabusId && this.subjectId && this.chapterId) {
                    this.activeTab = "MANAGE_TOPICS";
                } else if (this.classGroupSyllabusId && this.subjectId && !this.chapterId) {
                    this.activeTab = "MANAGE_CHAPTERS";
                } else if (this.classGroupSyllabusId && !this.subjectId) {
                    this.activeTab = "MANAGE_SUBJECTS";
                } else {
                    this.activeTab = "ASSIGN_CLASS_GROUP";
                }
            },
            goBack() {
                if (this.chapterId) {
                    this.$router.push({
                        name: "designClassSubjects",
                        params: {
                            classGroupSyllabusId: this.classGroupSyllabusId,
                            subjectId: this.subjectId,
                            chapterId: null
                        }
                    });
                } else if (this.subjectId) {
                    this.$router.push({
                        name: "designClassSubjects",
                        params: {
                            classGroupSyllabusId: this.classGroupSyllabusId,
                            subjectId: null
                        }
                    });
                } else {
                    this.$router.push({
                        name: "designClassSubjects",
                        params: {classGroupSyllabusId: null}
                    });
                }
            }
        }
    }
</script>

<style scoped>

</style>

<template>
    <div id="rootwizard">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs nav-tabs-linetriangle nav-tabs-separator nav-stack-sm" role="tablist"
            data-init-reponsive-tabs="dropdownfx">
            <li class="nav-item">
                <a class="d-flex align-items-center" href="#" :class="activeTab==='FILTER_SUBJECTS'?'active':''">
                    <i class="material-icons fs-14 tab-icon">touch_app</i>
                    <span>Select Subject</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="d-flex align-items-center" href="#" :class="activeTab==='MANAGE_QUESTION'?'active':''">
                    <i class="material-icons fs-14 tab-icon">drag_indicator</i>
                    <span>Manage Questions</span>
                </a>
            </li>
        </ul>
        <!-- Tab panes -->
        <div :class="activeTab==='MANAGE_TOPICS'?'custom-tab-content':'tab-content'">
            <FilterSubjects v-if="activeTab==='FILTER_SUBJECTS'"/>
            <ManageQuestion v-if="activeTab==='MANAGE_QUESTION'"/>
            <!--            <div class="row p-l-20 p-r-20 p-b-20" v-if="classGroupSyllabusId">-->
            <!--                <div class="col-md-12 text-right">-->
            <!--                    <button class="btn btn-primary" @click.prevent="goBack">-->
            <!--                        <i class="fa fa-arrow-circle-left mr-1"></i> Go Back-->
            <!--                    </button>-->
            <!--                </div>-->
            <!--            </div>-->
        </div>
    </div>
</template>

<script>
    import FilterSubjects from "./FilterSubjects";
    import ManageQuestion from "./ManageQuestion";

    export default {
        name: "QuestionPool",
        components: {
            ManageQuestion,
            FilterSubjects
        },
        data() {
            return {
                activeTab: "FILTER_SUBJECTS",
                classGroupSyllabusSubjectId: ""
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
                    this.activeTab = "MANAGE_QUESTION";
                } else {
                    this.activeTab = "FILTER_SUBJECTS";
                }
            },
        }
    }
</script>

<style scoped>

</style>

<template>
    <div id="manage-demo-topics">
        <div class="card" id="subject-filtering">
            <div class="card-header">
                <h4 class="card-title">Filter Subjects</h4>
            </div>
            <div class="card-body">
                <form role="form">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group form-group-default required"
                                 :class="{'has-error':$v.selectedSyllabus.$error}">
                                <label for="syllabus">Syllabus</label>
                                <select name="syllabus" id="syllabus" class="form-control select2"
                                        v-model="$v.selectedSyllabus.$model">
                                    <option value="" disabled>
                                        Select syllabus
                                    </option>
                                    <option :value="syllabus.syllabus_id" v-for="syllabus in syllabuses">
                                        {{ syllabus.syllabus_name }}
                                    </option>
                                </select>
                            </div>
                            <label class="error" role="alert" for="syllabus"
                                   v-if="$v.selectedSyllabus.$error&&!$v.selectedSyllabus.required">
                                Select a valid syllabus
                            </label>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group form-group-default required">
                                <label for="syllabus">Course</label>
                                <select name="course" id="course" class="form-control select2" v-model="selectedCourse">
                                    <option value="" disabled>
                                        Select Course
                                    </option>
                                    <option :value="course.course_id" v-for="course in courses">
                                        {{ course.course_name }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group form-group-default required">
                                <label for="course">Class</label>
                                <select name="course" id="class" class="form-control select2" v-model="selectedClass"
                                        :disabled="!selectedCourse">
                                    <option value="" disabled>
                                        Select Class
                                    </option>
                                    <option :value="courseClass.class_id" v-for="courseClass in classes">
                                        {{ courseClass.class_name }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group form-group-default required"
                                 :class="{'has-error':$v.selectedClassGroup.$error}">
                                <label for="class-group">Class Group</label>
                                <select name="class-group" id="class-group" class="form-control select2"
                                        v-model="$v.selectedClassGroup.$model" :disabled="!selectedClass">
                                    <option value="" disabled>
                                        Select Class Group
                                    </option>
                                    <option :value="group.class_group_id" v-for="group in classGroups">
                                        {{ group.class_group_name }}
                                    </option>
                                </select>
                            </div>
                            <label class="error" role="alert" for="class-group"
                                   v-if="$v.selectedClassGroup.$error&&!$v.selectedClassGroup.required">
                                Select a valid class group
                            </label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <div class="form-group mt-3">
                                <button class="btn btn-default" @click.prevent="reset">
                                    <i class="fa fa-times mr-1"></i> Reset
                                </button>
                                <button class="btn btn-primary" @click.prevent="filterSubjects">
                                    <i class="fa fa-search mr-1"></i> Search Now
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Available Subject</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <div id="stripedTable_wrapper" class="dataTables_wrapper no-footer">
                        <table class="table  dataTable no-footer" id="stripedTable" role="grid">
                            <thead>
                            <tr role="row">
                                <th width="5%">#</th>
                                <th width="25%">
                                    Subject Name
                                </th>
                                <th width="20%" class="sorting" tabindex="0" aria-controls="stripedTable">
                                    Subject Code
                                </th>
                                <th width="35%" class="sorting" tabindex="0" aria-controls="stripedTable">
                                    Subject Description
                                </th>
                                <th width="15%" class="sorting" tabindex="0" aria-controls="stripedTable">
                                    Manage Demo Topic
                                </th>
                            </tr>
                            </thead>
                            <tbody>

                            <tr role="row" class="odd" v-for="(subject,index) in subjects">
                                <td>
                                    {{ index + 1 }}
                                </td>
                                <td class="v-align-middle semi-bold sorting_1">
                                    {{ subject.subject_name }}
                                </td>
                                <td class="v-align-middle">
                                    {{ subject.subject_code }}
                                </td>
                                <td class="v-align-middle">
                                    {{ subject.subject_description }}
                                </td>
                                <td class="v-align-middle">
                                    <button class="btn btn-outline-success" @click.prevent="manageDemoTopic(subject)">
                                        <i class="fa fa-plus mr-1"></i> Manage Demo Topic
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="!subjects.length">
                                <td colspan="5" class="hint-text text-center">
                                    <h5 class="font-weight-light">No subjects assigned to this class group</h5>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <CreateDemoTopic :show-slider="showCreateSlider" :selected-subject="selectedSubject"
                         @on-close="onCloseManageDemoTopicSlider"/>
    </div>
</template>

<script>
import {required} from "vuelidate/lib/validators";
import SubjectAPI from "../subjects/SubjectAPI";
import ClassGroupAPI from "../classGroups/ClassGroupAPI";
import ClassesAPI from "../classes/ClassesAPI";
import CreateDemoTopic from "./CreateDemoTopic";

export default {
    name: "ManageDemoTopics",
    components: {CreateDemoTopic},
    data() {
        return {
            courses: [],
            selectedCourse: "",
            classes: [],
            selectedClass: "",
            classGroups: [],
            selectedClassGroup: "",
            syllabuses: [],
            selectedSyllabus: "",
            subjects: [],
            showCreateSlider: false,
            selectedSubject: null
        }
    },
    validations: {
        selectedSyllabus: {
            required
        },
        selectedClassGroup: {
            required
        }
    },
    mounted() {
        this.getAllBasicDetails();
    },
    watch: {
        selectedCourse() {
            this.classGroups = [];
            this.classes = [];
            this.selectedClass = "";
            this.selectedClassGroup = "";
            if (this.selectedCourse)
                this.getClassesByCourse();
        },
        selectedClass() {
            this.classGroups = [];
            this.selectedClassGroup = "";
            if (this.selectedClass)
                this.getClassGroupsByClass();
        }
    },
    methods: {
        filterSubjects() {
            $('#manage-demo-topics').block();
            this.$axios.get(SubjectAPI.FILTER_SUBJECTS, {
                params: {
                    class_group_id: this.selectedClassGroup,
                    syllabus_id: this.selectedSyllabus
                }
            })
                .then(response => {
                    this.$emit("on-list-fetched");
                    $('#manage-demo-topics').unblock();
                    const responseData = response.data;
                    if (responseData.success) {
                        this.subjects = responseData.data;
                    } else {
                        $.showNotification(responseData.error, "error");
                    }
                })
                .catch(() => {
                    this.$emit("on-list-fetched");
                    $('#manage-demo-topics').unblock();
                    $.showNotification("Error occurred while fetching the subject details. Try again later", "error");
                })
        },
        getClassGroupsByClass() {
            $('#subject-filtering').block();
            this.$axios.get(ClassGroupAPI.GET_ALL_CLASS_GROUPS_BY_CLASS, {
                params: {
                    class_id: this.selectedClass
                }
            })
                .then(response => {
                    $('#subject-filtering').unblock();
                    const responseData = response.data;
                    if (responseData.success) {
                        this.classGroups = responseData.data;
                    } else {
                        $.showNotification(responseData.error, "error");
                    }
                })
                .catch(() => {
                    $('#subject-filtering').unblock();
                    $.showNotification("Error occurred while fetching the class list. Try again later", "error");
                })
        },
        getClassesByCourse() {
            $('#subject-filtering').block();
            this.$axios.get(ClassesAPI.GET_CLASSES_BY_COURSE, {
                params: {
                    course_id: this.selectedCourse
                }
            })
                .then(response => {
                    $('#subject-filtering').unblock();
                    const responseData = response.data;
                    if (responseData.success) {
                        this.classes = responseData.data;
                    } else {
                        $.showNotification(responseData.error, "error");
                    }
                })
                .catch(() => {
                    $('#subject-filtering').unblock();
                    $.showNotification("Error occurred while fetching the class list. Try again later", "error");
                })
        },
        getAllBasicDetails() {
            $('#subject-filtering').block();
            this.$axios.get(SubjectAPI.GET_ALL_BASIC_DETAILS)
                .then(response => {
                    $('#subject-filtering').unblock();
                    const responseData = response.data;
                    if (responseData.success) {
                        this.courses = responseData.data.courses;
                        this.syllabuses = responseData.data.syllabuses;
                    } else {
                        $.showNotification(responseData.error, "error");
                    }
                })
                .catch(() => {
                    $('#subject-filtering').unblock();
                    $.showNotification("Error occurred while fetching some form details. Try again later", "error");
                })
        },
        reset() {
            this.selectedCourse = "";
            this.classes = [];
            this.selectedClass = "";
            this.classGroups = [];
            this.selectedClassGroup = "";
            this.selectedSyllabus = "";
            this.subjects = [];
            this.$v.$reset();
        },
        manageDemoTopic(subject) {
            this.showCreateSlider = true;
            this.selectedSubject = subject;
        },
        onCloseManageDemoTopicSlider() {
            this.showCreateSlider = false;
            this.selectedSubject = null;
        }
    }
}
</script>

<style scoped>

</style>

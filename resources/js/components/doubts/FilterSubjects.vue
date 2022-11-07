<template>
    <div class="tab-pane slide-left padding-20 sm-no-padding active" id="tab11">
        <div class="row row-same-height">
            <div class="col-md-5 b-r b-dashed b-grey ">
                <div class="padding-30 sm-padding-5 sm-m-t-15 m-t-50">
                    <h2>Filter Subjects!</h2>
                    <p>Here you can filter subjects assigned to each class group. You should fill all the required
                        fields to filter subjects.</p>
                    <p class="small hint-text">
                        Click on the <code class="text-success">Manage Doubts</code> to view doubts and write your
                        answer.
                    </p>
                </div>
            </div>
            <div class="col-md-7" id="subject-filtering">
                <div class="padding-30 sm-padding-5">
                    <form role="form">
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
                        <div class="form-group text-right mt-3">
                            <button class="btn btn-default" @click.prevent="reset">
                                <i class="fa fa-times mr-1"></i> Reset
                            </button>
                            <button class="btn btn-primary" @click.prevent="filterSubjects">
                                <i class="fa fa-search mr-1"></i> Search Now
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <ViewClassGroupSubjects
                    :subjects="subjects"/>
            </div>
        </div>
    </div>
</template>

<script>
import ViewClassGroupSubjects from "./ViewClassGroupSubjects";
import {required} from "vuelidate/lib/validators";
import SubjectAPI from "../subjects/SubjectAPI";
import ClassGroupAPI from "../classGroups/ClassGroupAPI";
import ClassesAPI from "../classes/ClassesAPI";

export default {
    name: "FilterSubjects",
    components: {ViewClassGroupSubjects},
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
            subjects: []
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
            $('#subject-filtering').block();
            this.$axios.get(SubjectAPI.FILTER_SUBJECTS, {
                params: {
                    class_group_id: this.selectedClassGroup,
                    syllabus_id: this.selectedSyllabus
                }
            })
                .then(response => {
                    this.$emit("on-list-fetched");
                    $('#subject-filtering').unblock();
                    const responseData = response.data;
                    if (responseData.success) {
                        this.subjects = responseData.data;
                    } else {
                        $.showNotification(responseData.error, "error");
                    }
                })
                .catch(() => {
                    this.$emit("on-list-fetched");
                    $('#subject-filtering').unblock();
                    $.showNotification("Error occurred while fetching the subject details. Try again later", "error");
                })
        },
        getClassGroupsByClass() {
            $('#class-assigning').block();
            this.$axios.get(ClassGroupAPI.GET_ALL_CLASS_GROUPS_BY_CLASS, {
                params: {
                    class_id: this.selectedClass
                }
            })
                .then(response => {
                    $('#class-assigning').unblock();
                    const responseData = response.data;
                    if (responseData.success) {
                        this.classGroups = responseData.data;
                    } else {
                        $.showNotification(responseData.error, "error");
                    }
                })
                .catch(() => {
                    $('#class-assigning').unblock();
                    $.showNotification("Error occurred while fetching the class list. Try again later", "error");
                })
        },
        getClassesByCourse() {
            $('#class-assigning').block();
            this.$axios.get(ClassesAPI.GET_CLASSES_BY_COURSE, {
                params: {
                    course_id: this.selectedCourse
                }
            })
                .then(response => {
                    $('#class-assigning').unblock();
                    const responseData = response.data;
                    if (responseData.success) {
                        this.classes = responseData.data;
                    } else {
                        $.showNotification(responseData.error, "error");
                    }
                })
                .catch(() => {
                    $('#class-assigning').unblock();
                    $.showNotification("Error occurred while fetching the class list. Try again later", "error");
                })
        },
        getAllBasicDetails() {
            $('#class-assigning').block();
            this.$axios.get(SubjectAPI.GET_ALL_BASIC_DETAILS)
                .then(response => {
                    $('#class-assigning').unblock();
                    const responseData = response.data;
                    if (responseData.success) {
                        this.courses = responseData.data.courses;
                        this.syllabuses = responseData.data.syllabuses;
                    } else {
                        $.showNotification(responseData.error, "error");
                    }
                })
                .catch(() => {
                    $('#class-assigning').unblock();
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
        },
    }
}
</script>

<style scoped>

</style>

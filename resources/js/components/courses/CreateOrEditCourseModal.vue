<template>
    <SliderModal id="create-update-course-modal" :show-slider="showSlider" @on-close="onClose">
        <template slot="modal-header">
            <h4 class="mt-0">Create Course</h4>
        </template>
        <template slot="modal-content">
            <form action="" role="form">
                <div class="form-group" :class="{ 'has-error': $v.courseName.$error }">
                    <label>Course Name</label>
                    <input type="text" class="form-control" required name="courseName"
                           v-model.trim="$v.courseName.$model">
                </div>
                <label class="error" role="alert" for="course_code"
                       v-if="!$v.courseName.required">
                    Enter a valid course name
                </label>
                <label class="error" role="alert" for="course_code"
                       v-if="!$v.courseName.minLength">
                    Course name must have at least {{$v.courseName.$params.minLength.min}}
                    letters.
                </label>
                <div class="form-group" :class="{ 'has-error': $v.courseCode.$error }">
                    <label>Course Code</label>
                    <input type="text" id="course_code" class="form-control" required name="courseCode"
                           v-model.trim="$v.courseCode.$model">
                </div>
                <label class="error" role="alert" for="course_code"
                       v-if="!$v.courseCode.required">
                    Enter a unique course code
                </label>
                <label class="error" role="alert" for="course_code"
                       v-if="!$v.courseCode.minLength">
                    Course code must have at least {{$v.courseName.$params.minLength.min}}
                    letters.
                </label>
                <div class="form-group">
                    <label>Course Description</label>
                    <input type="text" class="form-control" name="courseCode" v-model="courseDescription">
                </div>
            </form>
        </template>
        <template slot="modal-footer">
            <button type="button" class="btn btn-default btn-lg btn-xs-block"
                    @click.prevent="onClose()">
                Cancel
            </button>
            <button type="button" class="btn btn-primary btn-lg btn-xs-block"
                    @click.prevent="onSubmit()">
                {{selectedCourse?'Update':'Create'}}
            </button>
        </template>
    </SliderModal>
</template>

<script>
    import _ from "lodash";
    import {required, minLength} from "vuelidate/lib/validators";
    import SliderModal from "../common/SliderModal";
    import CourseAPIs from "./CourseAPIs";

    export default {
        name: "CreateOrEditCourseModal",
        components: {
            SliderModal
        },
        props: ["showSlider", "selectedCourse"],
        data() {
            return {
                courseName: "",
                courseCode: "",
                courseDescription: ""
            }
        },
        validations: {
            courseName: {
                required,
                minLength: minLength(3)
            },
            courseCode: {
                required,
                minLength: minLength(3)
            }
        },
        watch: {
            selectedCourse() {
                if (this.selectedCourse) {
                    this.courseName = _.clone(this.selectedCourse.course_name);
                    this.courseCode = _.clone(this.selectedCourse.course_code);
                    this.courseDescription = _.clone(this.selectedCourse.course_description);
                }
            }
        },
        methods: {
            onClose(status) {
                this.courseName = "";
                this.courseCode = "";
                this.courseDescription = "";
                this.$emit("on-close", status);
            },
            onSubmit() {
                this.$v.$touch();
                if (this.$v.$invalid) {
                    return
                }
                const data = {
                    course_name: this.courseName,
                    course_code: this.courseCode,
                    course_description: this.courseDescription
                };
                if (this.selectedCourse) {
                    data.course_id = this.selectedCourse.course_id
                }
                $("#create-update-course-modal").block();
                this.$axios.post(CourseAPIs.CREATE_OR_UPDATE_COURSE, data)
                    .then(response => {
                        $("#create-update-course-modal").unblock();
                        const responseData = response.data;
                        if (responseData.success) {
                            if (this.selectedCourse)
                                $.showNotification("Course details updated successfully", "success");
                            else
                                $.showNotification("Course created successfully", "success");
                            this.onClose(true);
                        } else {
                            // if (responseData.code === "DUPLICATE_COURSE_CODE")
                            $.showNotification(responseData.error, "error");
                        }
                    })
                    .catch(() => {
                        $("#create-update-course-modal").unblock();
                        $.showNotification("Error occurred while sending the data. Try again later");
                    });
            },

        }
    }
</script>

<style scoped>

</style>

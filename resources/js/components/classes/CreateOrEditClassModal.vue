<template>
    <SliderModal id="create-update-class-modal" :show-slider="showSlider" @on-close="onClose">
        <template slot="modal-header">
            <h4 class="mt-0">Create Class</h4>
        </template>
        <template slot="modal-content">
            <form action="" role="form">
                <div class="form-group" :class="{ 'has-error': $v.className.$error }">
                    <label>Class Name</label>
                    <input type="text" class="form-control" required name="className" id="class_name"
                           v-model.trim="$v.className.$model">
                </div>
                <label class="error" role="alert" for="class_name"
                       v-if="!$v.className.required">
                    Enter a valid class name
                </label>
                <label class="error" role="alert" for="class_name"
                       v-if="!$v.className.minLength">
                    Class name must have at least {{$v.className.$params.minLength.min}}
                    letters.
                </label>
                <div class="form-group" :class="{ 'has-error': $v.classCode.$error }">
                    <label>Class Code</label>
                    <input type="text" id="class_code" class="form-control" required name="classCode"
                           v-model.trim="$v.classCode.$model">
                </div>
                <label class="error" role="alert" for="class_code"
                       v-if="!$v.classCode.required">
                    Enter a unique class code
                </label>
                <label class="error" role="alert" for="class_code"
                       v-if="!$v.classCode.minLength">
                    Class code must have at least {{$v.classCode.$params.minLength.min}}
                    letters.
                </label>

                <div class="form-group" :class="{ 'has-error': $v.courseId.$error }">
                    <label>Course</label>
                    <select name="course_id" id="course_id" class="form-control" v-model="$v.courseId.$model">
                        <option value="" disabled>
                            Select course
                        </option>
                        <option v-for="course in courses" :value="course.course_id">
                            {{course.course_name}}
                        </option>
                    </select>
                </div>
                <label class="error" role="alert" for="course_id"
                       v-if="!$v.courseId.required">
                    Select a course
                </label>

                <div class="form-group">
                    <label>Class Description</label>
                    <textarea class="form-control" name="class_description" id="class_description"
                              v-model.trim="classDescription">
                    </textarea>
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
                {{selectedClass?'Update':'Create'}}
            </button>
        </template>
    </SliderModal>
</template>

<script>
    import _ from "lodash";
    import {required, minLength} from "vuelidate/lib/validators";
    import SliderModal from "../common/SliderModal";
    import ClassesAPI from "./ClassesAPI";
    import CourseAPIs from "../courses/CourseAPIs";

    export default {
        name: "CreateOrEditClassModal",
        components: {
            SliderModal
        },
        props: ["showSlider", "selectedClass"],
        data() {
            return {
                className: "",
                classCode: "",
                classDescription: "",
                courseId: "",
                courses: []

            }
        },
        validations: {
            className: {
                required,
                minLength: minLength(3)
            },
            classCode: {
                required,
                minLength: minLength(3)
            },
            courseId: {
                required,
            }
        },
        mounted() {
            this.getCourses();
        },
        watch: {
            selectedClass() {
                if (this.selectedClass) {
                    this.className = _.clone(this.selectedClass.class_name);
                    this.classCode = _.clone(this.selectedClass.class_code);
                    this.classDescription = _.clone(this.selectedClass.class_description);
                    this.courseId = _.clone(this.selectedClass.course.course_id);
                }
            }
        },
        methods: {
            getCourses() {
                this.$axios.get(CourseAPIs.GET_ALL_COURSES)
                    .then(response => {
                        const responseData = response.data;
                        if (responseData.success) {
                            this.courses = responseData.data;
                        } else {
                            $.showNotification(responseData.error, "error");
                        }
                    })
                    .catch(() => {
                        $.showNotification("Error occurred while fetching the course details. Try again later", "error");
                    })
            },
            onClose(status) {
                this.className = "";
                this.classCode = "";
                this.classDescription = "";
                this.courseId = "";
                this.$emit("on-close", status);
            },
            onSubmit() {
                this.$v.$touch();
                if (this.$v.$invalid) {
                    return
                }
                const data = {
                    class_name: this.className,
                    class_code: this.classCode,
                    class_description: this.classDescription,
                    course_id: this.courseId
                };
                if (this.selectedClass) {
                    data.class_id = this.selectedClass.class_id
                }
                $("#create-update-class-modal").block();
                this.$axios.post(ClassesAPI.CREATE_OR_UPDATE_CLASS, data)
                    .then(response => {
                        $("#create-update-class-modal").unblock();
                        const responseData = response.data;
                        if (responseData.success) {
                            if (this.selectedClass)
                                $.showNotification("Class details updated successfully", "success");
                            else
                                $.showNotification("Class created successfully", "success");
                            this.onClose(true);
                        } else {
                            // if (responseData.code === "DUPLICATE_COURSE_CODE")
                            $.showNotification(responseData.error, "error");
                        }
                    })
                    .catch(() => {
                        $("#create-update-class-modal").unblock();
                        $.showNotification("Error occurred while sending the data. Try again later","error");
                    });
            },

        }
    }
</script>

<style scoped>

</style>

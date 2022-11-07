<template>
    <SliderModal id="create-update-class-group-modal" :show-slider="showSlider" @on-close="onClose">
        <template slot="modal-header">
            <h4 class="mt-0">Create Class Group</h4>
        </template>
        <template slot="modal-content">
            <form action="" role="form">
                <div class="form-group" :class="{ 'has-error': $v.classGroupName.$error }">
                    <label>Group Name</label>
                    <input type="text" class="form-control" required name="class-group-name" id="class_group_name"
                           v-model.trim="$v.classGroupName.$model">
                </div>
                <label class="error" role="alert" for="class_group_name"
                       v-if="!$v.classGroupName.required">
                    Enter a valid group name
                </label>
                <label class="error" role="alert" for="class_group_name"
                       v-if="!$v.classGroupName.minLength">
                    Group name must have at least {{$v.classGroupName.$params.minLength.min}}
                    letters.
                </label>
                <div class="form-group" :class="{ 'has-error': $v.classGroupCode.$error }">
                    <label>Group Code</label>
                    <input type="text" id="class_code" class="form-control" required name="class-group-code"
                           v-model.trim="$v.classGroupCode.$model">
                </div>
                <label class="error" role="alert" for="class_code"
                       v-if="!$v.classGroupCode.required">
                    Enter a unique group code
                </label>
                <label class="error" role="alert" for="class_code"
                       v-if="!$v.classGroupCode.minLength">
                    Group code must have at least {{$v.classGroupCode.$params.minLength.min}}
                    letters.
                </label>
                <div class="form-group">
                    <label>Course</label>
                    <select name="course_id" id="course_id" class="form-control" v-model="courseId">
                        <option value="" disabled>
                            Select course
                        </option>
                        <option v-for="course in courses" :value="course.course_id">
                            {{course.course_name}}
                        </option>
                    </select>
                </div>
                <div class="form-group" :class="{ 'has-error': $v.classId.$error }">
                    <label>Class</label>
                    <select name="course_id" id="class_id" class="form-control" v-model="$v.classId.$model"
                            :disabled="!courseId">
                        <option value="" disabled>
                            Select class
                        </option>
                        <option v-for="courseClass in classes" :value="courseClass.class_id">
                            {{courseClass.class_name}}
                        </option>
                    </select>
                </div>
                <label class="error" role="alert" for="class_id"
                       v-if="!$v.classId.required">
                    Select a course
                </label>

                <div class="form-group">
                    <label>Class Description</label>
                    <textarea class="form-control" name="class_description" id="class_description"
                              v-model.trim="classGroupDescription">
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
                {{selectedClassGroup?'Update':'Create'}}
            </button>
        </template>
    </SliderModal>
</template>

<script>
    import _ from "lodash";
    import {required, minLength} from "vuelidate/lib/validators";
    import SliderModal from "../common/SliderModal";
    import ClassesAPI from "../classes/ClassesAPI";
    import ClassGroupAPI from "./ClassGroupAPI";
    import CourseAPIs from "../courses/CourseAPIs";

    export default {
        name: "CreateOrEditClassGroupModal",
        components: {
            SliderModal
        },
        props: ["showSlider", "selectedClassGroup"],
        data() {
            return {
                classGroupName: "",
                classGroupCode: "",
                classGroupDescription: "",
                courseId: "",
                classId: "",
                classes: [],
                courses: []

            }
        },
        validations: {
            classGroupName: {
                required,
                minLength: minLength(3)
            },
            classGroupCode: {
                required,
                minLength: minLength(3)
            },
            classId: {
                required,
            }
        },
        mounted() {
            this.getCourse();
        },
        watch: {
            courseId() {
                if (this.courseId)
                    this.getClasses();
                else {
                    this.classes = [];
                    this.classId = "";
                }

            },
            selectedClassGroup() {
                if (this.selectedClassGroup) {
                    this.classGroupName = _.clone(this.selectedClassGroup.class_group_name);
                    this.classGroupCode = _.clone(this.selectedClassGroup.class_group_code);
                    this.classGroupDescription = _.clone(this.selectedClassGroup.class_group_description);
                    this.classId = _.clone(this.selectedClassGroup.user_classes.class_id);
                    this.courseId = _.clone(this.selectedClassGroup.user_classes.course.course_id);
                }
            }
        },
        methods: {
            getCourse() {
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
                        $.showNotification("Error occurred while fetching the courses details. Try again later", "error");
                    })
            },
            getClasses() {
                $("#create-update-class-group-modal").block();
                this.$axios.get(ClassesAPI.GET_ALL_CLASSES)
                    .then(response => {
                        $("#create-update-class-group-modal").unblock();
                        const responseData = response.data;
                        if (responseData.success) {
                            this.classes = responseData.data;
                        } else {
                            $.showNotification(responseData.error, "error");
                        }
                    })
                    .catch(() => {
                        $("#create-update-class-group-modal").unblock();
                        $.showNotification("Error occurred while fetching the classes details. Try again later", "error");
                    })
            },
            onClose(status) {
                this.classGroupName = "";
                this.classGroupCode = "";
                this.classGroupDescription = "";
                this.classId = "";
                this.courseId = "";
                this.classes = [];
                this.$emit("on-close", status);
            },
            onSubmit() {
                this.$v.$touch();
                if (this.$v.$invalid) {
                    return
                }
                const data = {
                    class_group_name: this.classGroupName,
                    class_group_code: this.classGroupCode,
                    class_group_description: this.classGroupDescription,
                    class_id: this.classId
                };
                if (this.selectedClassGroup) {
                    data.class_group_id = this.selectedClassGroup.class_group_id
                }
                $("#create-update-class-group-modal").block();
                this.$axios.post(ClassGroupAPI.CREATE_OR_UPDATE_CLASS_GROUP, data)
                    .then(response => {
                        $("#create-update-class-group-modal").unblock();
                        const responseData = response.data;
                        if (responseData.success) {
                            if (this.selectedClassGroupGroup)
                                $.showNotification("Class group details updated successfully", "success");
                            else
                                $.showNotification("Class group created successfully", "success");
                            this.onClose(true);
                        } else {
                            // if (responseData.code === "DUPLICATE_COURSE_CODE")
                            $.showNotification(responseData.error, "error");
                        }
                    })
                    .catch(() => {
                        $("#create-update-class-group-modal").unblock();
                        $.showNotification("Error occurred while sending the data. Try again later", "error");
                    });
            },

        }
    }
</script>

<style scoped>

</style>

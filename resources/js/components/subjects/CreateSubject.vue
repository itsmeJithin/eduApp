<template>
    <div class="tab-pane padding-20 sm-no-padding slide-left active" id="tab22">
        <div class="row row-same-height">
            <div class="col-md-5 b-r b-dashed b-grey sm-b-b">
                <div class="padding-30 sm-padding-5 sm-m-t-15 m-t-50">
                    <i class="material-icons hint-text">drag_indicator</i>
                    <h2>Create subjects here!</h2>
                    <p>You can create subjects in this window and in the next step you can assign that subject to
                        required class!</p>
                    <p class="small hint-text">Create subjects with unique name details. Otherwise filtering of subjects
                        will be difficult</p>
                </div>
            </div>
            <div class="col-md-7" id="create-subject">
                <div class="padding-30 sm-padding-5" :class="selectedSubject?'notify-subject-edit':''">
                    <form role="form">
                        <div class="form-group form-group-default required"
                             :class="{ 'has-error': $v.subjectName.$error }">
                            <label for="subject-name">Subject Name</label>
                            <input id="subject-name" type="text" class="form-control" required
                                   placeholder="Enter a unique subject name" v-model.trim="$v.subjectName.$model"/>
                        </div>
                        <label class="error" role="alert" for="subject-name"
                               v-if="$v.subjectName.$error&&!$v.subjectName.required">
                            Enter a valid subject name
                        </label>
                        <label class="error" role="alert" for="subject-name"
                               v-if="!$v.subjectName.minLength">
                            Subject name must have at least {{$v.subjectName.$params.minLength.min}}
                            letters.
                        </label>
                        <div class="form-group form-group-default required"
                             :class="{ 'has-error': $v.subjectCode.$error }">
                            <label for="subject-code">Subject Code</label>
                            <input id="subject-code" type="text" class="form-control" required
                                   placeholder="Enter a unique subject code" v-model.trim="$v.subjectCode.$model"/>
                        </div>
                        <label class="error" role="alert" for="subject-code"
                               v-if="$v.subjectCode.$error&&!$v.subjectCode.required">
                            Enter a valid subject code
                        </label>
                        <label class="error" role="alert" for="subject-code"
                               v-if="!$v.subjectCode.minLength">
                            Subject code must have at least {{$v.subjectCode.$params.minLength.min}}
                            letters.
                        </label>
                        <label class="error" role="alert" for="subject-name"
                               v-if="errors&&errors.subject_code&&errors.subject_code.length">
                            {{errors.subject_code[0]}}
                        </label>
                        <div class="form-group form-group-default">
                            <label for="subject-description">Subject Description</label>
                            <textarea id="subject-description" name="subject_description"
                                      cols="30" v-model.trim="subjectDescription"
                                      class="form-control height-3rem" placeholder="Enter subject description"
                                      rows="10">
                            </textarea>
                        </div>
                        <div class="form-group text-right mt-2">
                            <button class="btn btn-default mr-1" @click.prevent="reset">
                                <i class="fa fa-times mr-1"></i> Reset
                            </button>
                            <button class="btn btn-success" @click.prevent="saveSubjectDetails">
                                <i class="fa fa-save mr-1"></i> Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-12">
                <ListSubjects :class-group-syllabus-id="classGroupSyllabusId"
                              :fetch-subject-list="fetchSubjectList"
                              @on-list-fetched="changeFlag"
                              @on-edit-subject="editSubject"/>
            </div>
        </div>
    </div>
</template>

<script>
    import SubjectAPI from "./SubjectAPI";
    import {minLength, required} from "vuelidate/lib/validators";
    import ListSubjects from "./ListSubjects";

    export default {
        name: "CreateSubject",
        components: {ListSubjects},
        props: {
            classGroupSyllabusId: {
                type: String,
                default: ""
            }
        },
        data() {
            return {
                subjectName: "",
                subjectCode: "",
                subjectDescription: "",
                subjectId: "",
                fetchSubjectList: false,
                errors: null,
                selectedSubject: null
            }
        },
        validations: {
            subjectName: {
                required,
                minLength: minLength(3)
            },
            subjectCode: {
                required,
                minLength: minLength(3)
            }
        },
        watch: {
            subjectCode() {
                this.errors = null;
            },
            selectedSubject() {
                if (this.selectedSubject) {
                    this.subjectCode = _.clone(this.selectedSubject.subject_code);
                    this.subjectName = _.clone(this.selectedSubject.subject_name);
                    this.subjectDescription = _.clone(this.selectedSubject.subject_description);
                }
            }
        },
        methods: {
            editSubject(subject) {
                this.selectedSubject = subject;
                window.scrollTo(0, $(".tab-content").offset().top-20);
            },
            reset() {
                this.subjectName = "";
                this.subjectCode = "";
                this.subjectDescription = "";
                this.selectedSubject = null;
                this.$v.$reset();
            },
            changeFlag() {
                this.fetchSubjectList = false;
            },
            saveSubjectDetails() {
                this.$v.$touch();
                if (this.$v.$invalid) {
                    $.showNotification("Fill all required fields with valid data", "error");
                    return
                }
                const data = {
                    subject_name: this.subjectName,
                    subject_code: this.subjectCode,
                    subject_description: this.subjectDescription,
                    class_group_syllabus_id: this.classGroupSyllabusId
                };
                if (this.selectedSubject) {
                    data.subject_id = this.selectedSubject.subject_id
                }
                $('#create-subject').block();
                this.$axios.post(SubjectAPI.CREATE_OR_UPDATE_SUBJECT, data)
                    .then(response => {
                        $('#create-subject').unblock();
                        const responseData = response.data;
                        if (responseData.success) {
                            this.reset();
                            this.fetchSubjectList = true;
                            $.showNotification("Subject created successfully", "success");
                        } else {
                            this.errors = responseData.messages;
                            if (responseData.messages && this.messages.subject_code) {
                                this.$v.$error = true;
                            }
                            $.showNotification(responseData.error, "error", 10000);
                        }
                    })
                    .catch(() => {
                        $('#create-subject').unblock();
                        $.showNotification("Error occurred while saving subjects details. Try again later", "error");
                    })
            }
        }
    }
</script>

<style scoped>
    .height-3rem {
        height: 3rem !important;
    }
</style>

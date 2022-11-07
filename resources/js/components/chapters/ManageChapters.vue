<template>
    <div class="tab-pane padding-20 sm-no-padding slide-left active" id="tab22">
        <div class="row row-same-height">
            <div class="col-md-5 b-r b-dashed b-grey sm-b-b">
                <div class="padding-30 sm-padding-5 sm-m-t-15 m-t-50">
                    <i class="material-icons hint-text">dns</i>
                    <h2>Create Chapters here!</h2>
                    <p>You can create chapters for the selected subjects in this window and you can also delete
                        previously created chapter or you can manage topics inside each chapter</p>
                    <p class="small hint-text">Create chapters with unique code. Otherwise filtering of chapters
                        will be difficult</p>
                </div>
            </div>
            <div class="col-md-7" id="create-chapter">
                <div class="padding-30 sm-padding-5" :class="selectedChapter?'notify-subject-edit':''">
                    <form role="form">
                        <div class="form-group form-group-default required"
                             :class="{ 'has-error': $v.chapterName.$error }">
                            <label for="chapter-name">Chapter Name</label>
                            <input id="chapter-name" type="text" class="form-control" required
                                   placeholder="Enter a unique chapter name" v-model.trim="$v.chapterName.$model"/>
                        </div>
                        <label class="error" role="alert" for="chapter-name"
                               v-if="$v.chapterName.$error&&!$v.chapterName.required">
                            Enter a valid chapter name
                        </label>
                        <label class="error" role="alert" for="chapter-name"
                               v-if="!$v.chapterName.minLength">
                            Chapter name must have at least {{$v.chapterName.$params.minLength.min}}
                            letters.
                        </label>
                        <div class="form-group form-group-default required"
                             :class="{ 'has-error': $v.chapterCode.$error }">
                            <label for="chapter-code">Chapter Code</label>
                            <input id="chapter-code" type="text" class="form-control" required
                                   placeholder="Enter a unique chapter code" v-model.trim="$v.chapterCode.$model"/>
                        </div>
                        <label class="error" role="alert" for="chapter-code"
                               v-if="$v.chapterCode.$error&&!$v.chapterCode.required">
                            Enter a valid chapter code
                        </label>
                        <label class="error" role="alert" for="chapter-code"
                               v-if="!$v.chapterCode.minLength">
                            Chapter code must have at least {{$v.chapterCode.$params.minLength.min}}
                            letters.
                        </label>
                        <label class="error" role="alert" for="chapter-code"
                               v-if="errors&&errors.chapter_code&&errors.chapter_code.length">
                            {{errors.chapter_code[0]}}
                        </label>
                        <div class="form-group form-group-default">
                            <label for="chapter-description">Chapter Description</label>
                            <textarea id="chapter-description" name="chapter_description"
                                      cols="30" v-model.trim="chapterDescription"
                                      class="form-control height-3rem" placeholder="Enter chapter description"
                                      rows="10">
                            </textarea>
                        </div>
                        <div class="form-group text-right mt-2">
                            <button class="btn btn-default mr-1" @click.prevent="reset">
                                <i class="fa fa-times mr-1"></i> Reset
                            </button>
                            <button class="btn btn-success" @click.prevent="saveChapterDetails">
                                <i class="fa fa-save mr-1"></i> Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-12">
                <ListChapters v-if="classGroupSyllabusId&&subjectId"
                              :class-group-syllabus-id="classGroupSyllabusId"
                              :subject-id="subjectId"
                              :fetch-chapters-list="fetchChaptersList"
                              @on-list-fetched="changeFlag"
                              @on-edit-chapter="editChapter"/>

            </div>
        </div>
    </div>
</template>

<script>
    import ListChapters from "./ListChapters";
    import {minLength, required} from "vuelidate/lib/validators";
    import SubjectAPI from "../subjects/SubjectAPI";
    import ChaptersAPI from "./ChaptersAPI";

    export default {
        name: "ManageChapters",
        components: {ListChapters},
        data() {
            return {
                selectedChapter: null,
                classGroupSyllabusId: "",
                subjectId: "",
                chapterCode: "",
                chapterName: "",
                chapterDescription: "",
                fetchChaptersList: false,
                errors: null,
            }
        },
        mounted() {
            this.fetchDataFromRoutes();
        },
        validations: {
            chapterCode: {
                required,
                minLength: minLength(3)
            },
            chapterName: {
                required,
                minLength: minLength(3)
            }
        },
        watch: {
            chapterCode() {
                this.errors = null;
            },
            selectedChapter() {
                if (this.selectedChapter) {
                    this.chapterCode = _.clone(this.selectedChapter.chapter_code);
                    this.chapterName = _.clone(this.selectedChapter.chapter_name);
                    this.chapterDescription = _.clone(this.selectedChapter.chapter_description);
                }
            }
        },
        methods: {
            fetchDataFromRoutes() {
                const route = this.$route;
                this.classGroupSyllabusId = route.params.classGroupSyllabusId;
                this.subjectId = route.params.subjectId;
            },
            changeFlag() {
                this.fetchChaptersList = false;
            },
            editChapter(chapter) {
                this.selectedChapter = chapter;
                window.scrollTo(0, $(".tab-content").offset().top - 20);

            },
            reset() {
                this.chapterCode = "";
                this.chapterName = "";
                this.chapterDescription = "";
                this.selectedChapter = null;
                this.$v.$reset();
            },
            saveChapterDetails() {
                this.$v.$touch();
                if (this.$v.$invalid) {
                    $.showNotification("Fill all required fields with valid data", "error");
                    return
                }
                const data = {
                    chapter_name: this.chapterName,
                    chapter_code: this.chapterCode,
                    chapter_description: this.chapterDescription,
                    class_group_syllabus_id: this.classGroupSyllabusId,
                    subject_id: this.subjectId
                };
                if (this.selectedChapter) {
                    data.chapter_id = this.selectedChapter.chapter_id
                }
                $('#create-chapter').block();
                this.$axios.post(ChaptersAPI.CREATE_OR_UPDATE_CHAPTER, data)
                    .then(response => {
                        $('#create-chapter').unblock();
                        const responseData = response.data;
                        if (responseData.success) {
                            this.reset();
                            this.fetchChaptersList = true;
                            $.showNotification("Chapter details saved successfully", "success");
                        } else {
                            this.errors = responseData.messages;
                            if (responseData.messages && this.messages.subject_code) {
                                this.$v.$error = true;
                            }
                            $.showNotification(responseData.error, "error", 10000);
                        }
                    })
                    .catch(() => {
                        $('#create-chapter').unblock();
                        $.showNotification("Error occurred while saving chapter details. Try again later", "error");
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
